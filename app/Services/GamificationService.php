<?php

namespace App\Services;

use App\Enums\AchievementType;
use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\ChallengeType;
use App\Enums\ExperienceSource;
use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Enums\StreakType;
use App\Models\Achievement;
use App\Models\Challenge;
use App\Models\DiagnosticAttempt;
use App\Models\ExperienceTransaction;
use App\Models\GamificationProfile;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\StudentChallenge;
use App\Models\StudentStreak;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class GamificationService
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly UserNotificationService $notifications,
    ) {}

    /**
     * @param  array<string, mixed>  $meta
     */
    public function awardXp(
        Student $student,
        ExperienceSource $source,
        int $points,
        string $description,
        ?Model $reference = null,
        array $meta = [],
    ): ExperienceTransaction {
        $this->bootstrapCatalog();
        $points = max(0, $points);

        return DB::transaction(function () use ($student, $source, $points, $description, $reference, $meta): ExperienceTransaction {
            $profile = GamificationProfile::query()->firstOrCreate(
                ['student_id' => $student->id],
                ['total_xp' => 0, 'current_level' => 1, 'xp_to_next_level' => $this->xpRequiredForLevel(2)]
            );

            $transaction = ExperienceTransaction::query()->create([
                'student_id' => $student->id,
                'source' => $source,
                'points' => $points,
                'description' => $description,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
                'meta' => $meta,
            ]);

            $previousLevel = $profile->current_level;
            $profile->total_xp += $points;
            [$level, $xpToNext] = $this->resolveLevelState($profile->total_xp);
            $profile->current_level = $level;
            $profile->xp_to_next_level = $xpToNext;
            $profile->engagement_score = min(100, $this->calculateEngagementScore($student));
            $profile->save();

            $this->updateChallengesProgress($student, $source, 1);
            $this->updateStreakBySource($student, $source);
            $this->checkAchievements($student);

            if ($student->user !== null && $level > $previousLevel) {
                $this->notifications->notifyUser(
                    user: $student->user,
                    title: 'Subiste de nivel',
                    message: "Ahora estás en el nivel {$level}.",
                    category: NotificationCategory::Gamification,
                    priority: NotificationPriority::High,
                    actionUrl: route('student.gamification.index', absolute: false),
                    actionLabel: 'Ver progreso',
                    mailTemplate: 'achievement-unlocked',
                    meta: ['level' => $level]
                );
            }

            return $transaction;
        });
    }

    public function onAiTutorUsage(Student $student): void
    {
        $this->awardXp(
            $student,
            ExperienceSource::AiTutorUsage,
            30,
            'Uso pedagógico del tutor IA'
        );
    }

    public function onDiagnosticCompleted(Student $student, DiagnosticAttempt $attempt): void
    {
        $this->awardXp(
            $student,
            ExperienceSource::DiagnosticCompleted,
            80,
            'Diagnóstico adaptativo completado',
            $attempt
        );

        $latestProfile = $student->adaptiveProfile;
        if ($latestProfile !== null && (float) $latestProfile->last_diagnostic_score >= 70) {
            $this->awardXp(
                $student,
                ExperienceSource::AdaptiveImprovement,
                60,
                'Mejora de nivel adaptativo detectada',
                $attempt
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function studentSummary(Student $student): array
    {
        $this->bootstrapCatalog();
        $profile = GamificationProfile::query()->firstOrCreate(
            ['student_id' => $student->id],
            ['total_xp' => 0, 'current_level' => 1, 'xp_to_next_level' => $this->xpRequiredForLevel(2)]
        );

        $currentLevelFloorXp = $this->xpRequiredForLevel($profile->current_level);
        $nextLevel = $profile->current_level + 1;
        $nextLevelXp = $this->xpRequiredForLevel($nextLevel);
        $levelRange = max(1, $nextLevelXp - $currentLevelFloorXp);
        $progressInLevel = max(0, $profile->total_xp - $currentLevelFloorXp);
        $progressPercent = (int) min(100, round(($progressInLevel / $levelRange) * 100));

        $achievements = StudentAchievement::query()
            ->with('achievement')
            ->where('student_id', $student->id)
            ->orderByDesc('unlocked_at')
            ->get();

        $streaks = StudentStreak::query()->where('student_id', $student->id)->get()->keyBy('type');
        $activeChallenges = StudentChallenge::query()
            ->with('challenge')
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->orderByDesc('updated_at')
            ->get();

        $rank = $this->rankingFor($student);
        $recentActivity = ExperienceTransaction::query()
            ->where('student_id', $student->id)
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        return [
            'profile' => [
                'total_xp' => $profile->total_xp,
                'current_level' => $profile->current_level,
                'next_level' => $nextLevel,
                'xp_to_next_level' => $profile->xp_to_next_level,
                'progress_percent' => $progressPercent,
                'engagement_score' => $profile->engagement_score,
            ],
            'achievements' => $achievements->map(fn (StudentAchievement $a): array => [
                'code' => $a->achievement?->code ?? '',
                'title' => $a->achievement?->title ?? '',
                'description' => $a->achievement?->description ?? '',
                'icon' => $a->achievement?->icon,
                'color' => $a->achievement?->color ?? 'navy',
                'rarity' => $a->achievement?->rarity ?? 'common',
                'unlocked_at' => $a->unlocked_at?->toIso8601String(),
            ])->values()->all(),
            'streaks' => [
                'study' => $this->serializeStreak($streaks->get(StreakType::Study->value)),
                'attendance' => $this->serializeStreak($streaks->get(StreakType::Attendance->value)),
                'on_time_submission' => $this->serializeStreak($streaks->get(StreakType::OnTimeSubmission->value)),
            ],
            'challenges' => $activeChallenges->map(function (StudentChallenge $row): array {
                $target = max(1, (int) ($row->challenge?->target_value ?? 1));

                return [
                    'title' => $row->challenge?->title ?? '',
                    'description' => $row->challenge?->description ?? '',
                    'progress_value' => $row->progress_value,
                    'target_value' => $target,
                    'progress_percent' => (int) min(100, round(($row->progress_value / $target) * 100)),
                    'xp_reward' => (int) ($row->challenge?->xp_reward ?? 0),
                    'status' => $row->status,
                ];
            })->values()->all(),
            'ranking' => $rank,
            'recent_activity' => $recentActivity->map(fn (ExperienceTransaction $row): array => [
                'source' => $row->source->value,
                'points' => $row->points,
                'description' => $row->description,
                'created_at' => $row->created_at?->toIso8601String(),
            ])->values()->all(),
        ];
    }

    /**
     * @param  list<int>  $sectionIds
     * @return list<array<string, mixed>>
     */
    public function topStudentsForTeacher(array $sectionIds, int $limit = 5): array
    {
        if ($sectionIds === []) {
            return [];
        }

        return GamificationProfile::query()
            ->select('gamification_profiles.*')
            ->join('students', 'students.id', '=', 'gamification_profiles.student_id')
            ->join('enrollments', 'enrollments.student_id', '=', 'students.id')
            ->whereIn('enrollments.section_id', $sectionIds)
            ->orderByDesc('gamification_profiles.total_xp')
            ->limit($limit)
            ->with('student:id,first_name,last_name,code')
            ->get()
            ->map(fn (GamificationProfile $row): array => [
                'student' => $row->student?->fullName(),
                'code' => $row->student?->code,
                'level' => $row->current_level,
                'xp' => $row->total_xp,
            ])->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function institutionalOverview(): array
    {
        $profiles = GamificationProfile::query()->get();
        $transactions = ExperienceTransaction::query();

        return [
            'students_with_profile' => $profiles->count(),
            'avg_xp' => round((float) $profiles->avg('total_xp'), 2),
            'avg_level' => round((float) $profiles->avg('current_level'), 2),
            'avg_engagement' => round((float) $profiles->avg('engagement_score'), 2),
            'xp_last_30d' => (int) $transactions->where('created_at', '>=', now()->subDays(30))->sum('points'),
            'ai_usage_last_30d' => (int) ExperienceTransaction::query()
                ->where('source', ExperienceSource::AiTutorUsage->value)
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'lms_activity_last_30d' => (int) ExperienceTransaction::query()
                ->whereIn('source', [
                    ExperienceSource::AssignmentCompleted->value,
                    ExperienceSource::ExamApproved->value,
                    ExperienceSource::LmsParticipation->value,
                ])
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'top_students' => GamificationProfile::query()
                ->with('student:id,first_name,last_name,code')
                ->orderByDesc('total_xp')
                ->limit(10)
                ->get()
                ->map(fn (GamificationProfile $row): array => [
                    'student' => $row->student?->fullName(),
                    'code' => $row->student?->code,
                    'level' => $row->current_level,
                    'xp' => $row->total_xp,
                ])->values()->all(),
        ];
    }

    private function updateStreakBySource(Student $student, ExperienceSource $source): void
    {
        $map = [
            ExperienceSource::AssignmentCompleted->value => StreakType::Study,
            ExperienceSource::ExamApproved->value => StreakType::Study,
            ExperienceSource::AttendanceDaily->value => StreakType::Attendance,
            ExperienceSource::AssignmentEarly->value => StreakType::OnTimeSubmission,
        ];
        $streakType = $map[$source->value] ?? null;
        if ($streakType === null) {
            return;
        }

        $row = StudentStreak::query()->firstOrCreate(
            ['student_id' => $student->id, 'type' => $streakType->value],
            ['current_count' => 0, 'best_count' => 0]
        );

        $isSameDay = $row->last_activity_at?->isSameDay(now()) ?? false;
        if ($isSameDay) {
            return;
        }

        $isConsecutive = $row->last_activity_at?->isSameDay(now()->subDay()) ?? false;
        $row->current_count = $isConsecutive ? $row->current_count + 1 : 1;
        $row->best_count = max($row->best_count, $row->current_count);
        $row->last_activity_at = now();
        $row->save();
    }

    private function updateChallengesProgress(Student $student, ExperienceSource $source, int $increment): void
    {
        $activeChallenges = Challenge::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->get();

        /** @var Collection<int, Challenge> $matched */
        $matched = $activeChallenges->filter(function (Challenge $challenge) use ($source): bool {
            return match ($challenge->type) {
                ChallengeType::TaskCompletion => $source === ExperienceSource::AssignmentCompleted,
                ChallengeType::AiUsage => $source === ExperienceSource::AiTutorUsage,
                ChallengeType::Assessment => in_array($source, [ExperienceSource::ExamApproved, ExperienceSource::ExamOutstanding], true),
                ChallengeType::Attendance => $source === ExperienceSource::AttendanceDaily,
                ChallengeType::Adaptive => in_array($source, [ExperienceSource::DiagnosticCompleted, ExperienceSource::AdaptiveImprovement], true),
                ChallengeType::LmsParticipation => in_array($source, [ExperienceSource::AssignmentCompleted, ExperienceSource::ExamApproved, ExperienceSource::LmsParticipation], true),
            };
        });

        foreach ($matched as $challenge) {
            $row = StudentChallenge::query()->firstOrCreate(
                ['student_id' => $student->id, 'challenge_id' => $challenge->id],
                ['status' => 'active', 'progress_value' => 0]
            );

            if ($row->status === 'completed') {
                continue;
            }

            $row->progress_value += $increment;
            if ($row->progress_value >= $challenge->target_value) {
                $row->status = 'completed';
                $row->completed_at = now();
                $row->save();

                $this->awardXp(
                    $student,
                    ExperienceSource::ChallengeCompleted,
                    (int) $challenge->xp_reward,
                    'Reto completado: '.$challenge->title,
                    $challenge
                );
            } else {
                $row->save();
            }
        }
    }

    private function checkAchievements(Student $student): void
    {
        $profile = GamificationProfile::query()->where('student_id', $student->id)->first();
        if ($profile === null) {
            return;
        }

        $transactionsCount = ExperienceTransaction::query()->where('student_id', $student->id)->count();
        $aiCount = ExperienceTransaction::query()
            ->where('student_id', $student->id)
            ->where('source', ExperienceSource::AiTutorUsage->value)
            ->count();
        $attendanceStreak = StudentStreak::query()
            ->where('student_id', $student->id)
            ->where('type', StreakType::Attendance->value)
            ->value('best_count') ?? 0;
        $studyStreak = StudentStreak::query()
            ->where('student_id', $student->id)
            ->where('type', StreakType::Study->value)
            ->value('best_count') ?? 0;

        $candidateCodes = [];
        if ($transactionsCount >= 1) {
            $candidateCodes[] = 'first_exam_completed';
        }
        if ($attendanceStreak >= 7) {
            $candidateCodes[] = 'perfect_attendance_streak';
        }
        if ($studyStreak >= 5) {
            $candidateCodes[] = 'study_streak';
        }
        if ($profile->total_xp >= 1000) {
            $candidateCodes[] = 'academic_excellence';
        }
        if ($aiCount >= 5) {
            $candidateCodes[] = 'ai_explorer';
        }

        foreach ($candidateCodes as $code) {
            $achievement = Achievement::query()->where('code', $code)->where('is_active', true)->first();
            if ($achievement === null) {
                continue;
            }

            $exists = StudentAchievement::query()
                ->where('student_id', $student->id)
                ->where('achievement_id', $achievement->id)
                ->exists();
            if ($exists) {
                continue;
            }

            StudentAchievement::query()->create([
                'student_id' => $student->id,
                'achievement_id' => $achievement->id,
                'unlocked_at' => now(),
                'meta' => ['code' => $code],
            ]);

            if ((int) $achievement->xp_reward > 0) {
                ExperienceTransaction::query()->create([
                    'student_id' => $student->id,
                    'source' => ExperienceSource::ChallengeCompleted,
                    'points' => (int) $achievement->xp_reward,
                    'description' => 'Logro desbloqueado: '.$achievement->title,
                    'meta' => ['achievement_id' => $achievement->id],
                ]);

                $profile->total_xp += (int) $achievement->xp_reward;
                [$level, $xpToNext] = $this->resolveLevelState($profile->total_xp);
                $profile->current_level = $level;
                $profile->xp_to_next_level = $xpToNext;
                $profile->save();
            }

            $this->audit->log(
                AuditAction::Update,
                AuditModule::Gamification,
                $student->user,
                StudentAchievement::class,
                null,
                'Logro desbloqueado: '.$achievement->title
            );

            if ($student->user !== null) {
                $this->notifications->notifyUser(
                    user: $student->user,
                    title: 'Logro desbloqueado',
                    message: $achievement->title,
                    category: NotificationCategory::Gamification,
                    priority: NotificationPriority::High,
                    actionUrl: route('student.gamification.index', absolute: false),
                    actionLabel: 'Ver logro',
                    mailTemplate: 'achievement-unlocked',
                    meta: ['achievement_id' => $achievement->id]
                );
            }
        }
    }

    private function calculateEngagementScore(Student $student): int
    {
        $recent = ExperienceTransaction::query()
            ->where('student_id', $student->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('points');

        return (int) min(100, round($recent / 40));
    }

    /**
     * @return array{0:int,1:int}
     */
    private function resolveLevelState(int $totalXp): array
    {
        $level = 1;
        while ($this->xpRequiredForLevel($level + 1) <= $totalXp) {
            $level++;
        }

        $nextLevelXp = $this->xpRequiredForLevel($level + 1);

        return [$level, max(0, $nextLevelXp - $totalXp)];
    }

    private function xpRequiredForLevel(int $level): int
    {
        if ($level <= 1) {
            return 0;
        }

        return 100 * (($level - 1) * $level / 2) + 100;
    }

    /**
     * @return array<string, mixed>
     */
    private function rankingFor(Student $student): array
    {
        $orderedIds = GamificationProfile::query()
            ->orderByDesc('total_xp')
            ->pluck('student_id')
            ->values();

        $position = $orderedIds->search($student->id);
        $position = $position === false ? null : ((int) $position + 1);

        return [
            'position' => $position,
            'total_students' => $orderedIds->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeStreak(?StudentStreak $streak): array
    {
        return [
            'current' => (int) ($streak?->current_count ?? 0),
            'best' => (int) ($streak?->best_count ?? 0),
            'last_activity_at' => $streak?->last_activity_at?->toIso8601String(),
        ];
    }

    private function bootstrapCatalog(): void
    {
        $achievements = [
            ['code' => 'first_exam_completed', 'type' => AchievementType::Academic, 'title' => 'Primer examen completado', 'description' => 'Completaste tu primera evaluación digital.', 'icon' => 'award', 'color' => 'navy', 'rarity' => 'common', 'xp_reward' => 40],
            ['code' => 'perfect_attendance_streak', 'type' => AchievementType::Attendance, 'title' => 'Asistencia perfecta', 'description' => 'Mantuviste 7 días consecutivos de asistencia.', 'icon' => 'calendar-check', 'color' => 'emerald', 'rarity' => 'rare', 'xp_reward' => 80],
            ['code' => 'study_streak', 'type' => AchievementType::Consistency, 'title' => 'Racha de estudio', 'description' => 'Mantuviste una racha de estudio sostenida.', 'icon' => 'flame', 'color' => 'amber', 'rarity' => 'rare', 'xp_reward' => 90],
            ['code' => 'ai_explorer', 'type' => AchievementType::Ai, 'title' => 'Explorador IA', 'description' => 'Usaste el tutor IA de forma consistente.', 'icon' => 'sparkles', 'color' => 'violet', 'rarity' => 'epic', 'xp_reward' => 120],
            ['code' => 'academic_excellence', 'type' => AchievementType::Academic, 'title' => 'Excelencia académica', 'description' => 'Superaste 1000 XP académicos.', 'icon' => 'trophy', 'color' => 'rose', 'rarity' => 'legendary', 'xp_reward' => 200],
        ];

        foreach ($achievements as $row) {
            Achievement::query()->firstOrCreate(
                ['code' => $row['code']],
                [
                    'type' => $row['type']->value,
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'icon' => $row['icon'],
                    'color' => $row['color'],
                    'rarity' => $row['rarity'],
                    'xp_reward' => $row['xp_reward'],
                    'is_active' => true,
                ]
            );
        }

        $challenges = [
            ['code' => 'weekly_tasks_3', 'type' => ChallengeType::TaskCompletion, 'title' => 'Reto semanal de tareas', 'description' => 'Completa 3 tareas.', 'target_value' => 3, 'xp_reward' => 90],
            ['code' => 'ai_sessions_5', 'type' => ChallengeType::AiUsage, 'title' => 'Reto IA', 'description' => 'Usa el tutor IA en 5 sesiones.', 'target_value' => 5, 'xp_reward' => 110],
            ['code' => 'attendance_week', 'type' => ChallengeType::Attendance, 'title' => 'Asistencia semanal', 'description' => 'Registra 5 asistencias efectivas.', 'target_value' => 5, 'xp_reward' => 80],
        ];

        foreach ($challenges as $row) {
            Challenge::query()->firstOrCreate(
                ['code' => $row['code']],
                [
                    'type' => $row['type']->value,
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'target_value' => $row['target_value'],
                    'xp_reward' => $row['xp_reward'],
                    'is_active' => true,
                ]
            );
        }
    }
}
