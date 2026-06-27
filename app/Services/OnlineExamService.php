<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Enums\EnrollmentStatus;
use App\Enums\ExperienceSource;
use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Enums\OnlineExamAttemptStatus;
use App\Enums\OnlineExamGradingMode;
use App\Enums\OnlineExamQuestionType;
use App\Models\OnlineExam;
use App\Models\OnlineExamAttempt;
use App\Models\OnlineExamQuestion;
use App\Models\Student;
use App\Models\User;
use App\Models\VirtualClassroom;
use Illuminate\Support\Collection;

final class OnlineExamService
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly LMSService $lms,
        private readonly LMSAdaptiveIntegrationService $adaptive,
        private readonly LMSGradeSyncService $gradeSync,
        private readonly GamificationService $gamification,
        private readonly UserNotificationService $notifications,
    ) {}

    public function createExam(User $user, VirtualClassroom $classroom, array $data, array $questions = []): OnlineExam
    {
        $exam = OnlineExam::query()->create([
            'virtual_classroom_id' => $classroom->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'grading_mode' => OnlineExamGradingMode::from($data['grading_mode'] ?? 'automatic'),
            'time_limit_minutes' => $data['time_limit_minutes'] ?? null,
            'max_attempts' => $data['max_attempts'] ?? 1,
            'shuffle_questions' => $data['shuffle_questions'] ?? false,
            'show_results_after' => $data['show_results_after'] ?? true,
            'is_published' => $data['is_published'] ?? true,
            'available_from' => $data['available_from'] ?? now(),
            'available_until' => $data['available_until'] ?? null,
            'created_by_user_id' => $user->id,
        ]);

        foreach ($questions as $i => $q) {
            OnlineExamQuestion::query()->create([
                'online_exam_id' => $exam->id,
                'question_type' => OnlineExamQuestionType::from($q['question_type']),
                'stem' => $q['stem'],
                'options' => $q['options'] ?? null,
                'correct_answer' => $q['correct_answer'] ?? null,
                'points' => $q['points'] ?? 1,
                'sort_order' => $q['sort_order'] ?? ($i + 1),
                'topic' => $q['topic'] ?? null,
            ]);
        }

        $this->lms->syncCalendarForExam($exam);

        $this->audit->log(
            AuditAction::Create,
            AuditModule::Lms,
            $user,
            OnlineExam::class,
            $exam->id,
            'Creación de evaluación online',
            null,
            ['title' => $exam->title],
            AuditResult::Success,
        );

        $recipients = User::query()
            ->whereHas('student.enrollments', function ($query) use ($classroom): void {
                $query->where('academic_year_id', $classroom->academic_year_id)
                    ->where('section_id', $classroom->section_id)
                    ->where('status', EnrollmentStatus::Matriculado->value);
            })
            ->get();

        $this->notifications->notifyMany(
            users: $recipients,
            title: 'Nueva evaluación programada',
            message: $exam->title,
            category: NotificationCategory::Lms,
            priority: NotificationPriority::High,
            actionUrl: route('student.classrooms.show', $classroom, absolute: false),
            actionLabel: 'Ver evaluación',
            mailTemplate: 'exam-reminder',
            meta: ['online_exam_id' => $exam->id]
        );

        return $exam->fresh('questions');
    }

    public function canStart(Student $student, OnlineExam $exam): bool
    {
        $count = $exam->attempts()
            ->where('student_id', $student->id)
            ->where('status', OnlineExamAttemptStatus::Completed)
            ->count();

        return $count < $exam->max_attempts;
    }

    public function startAttempt(Student $student, User $user, OnlineExam $exam): OnlineExamAttempt
    {
        $attemptNumber = $exam->attempts()->where('student_id', $student->id)->count() + 1;

        $expiresAt = null;
        if ($exam->time_limit_minutes !== null) {
            $expiresAt = now()->addMinutes($exam->time_limit_minutes);
        }

        $attempt = OnlineExamAttempt::query()->create([
            'online_exam_id' => $exam->id,
            'student_id' => $student->id,
            'user_id' => $user->id,
            'attempt_number' => $attemptNumber,
            'status' => OnlineExamAttemptStatus::InProgress,
            'answers' => [],
            'started_at' => now(),
            'expires_at' => $expiresAt,
        ]);

        $this->audit->log(
            AuditAction::Assessment,
            AuditModule::Lms,
            $user,
            OnlineExamAttempt::class,
            $attempt->id,
            'Inicio de evaluación online',
            null,
            ['exam_id' => $exam->id],
            AuditResult::Success,
        );

        return $attempt;
    }

    /**
     * @param  array<int, mixed>  $answers  question_id => answer
     */
    public function completeAttempt(OnlineExamAttempt $attempt, array $answers): OnlineExamAttempt
    {
        $attempt->loadMissing('onlineExam.questions');
        $exam = $attempt->onlineExam;

        $questions = $exam->shuffle_questions
            ? $exam->questions->shuffle()
            : $exam->questions;

        $score = $this->scoreAnswers($questions, $answers, $exam->grading_mode);

        $attempt->fill([
            'answers' => $answers,
            'score_percent' => $score,
            'status' => OnlineExamAttemptStatus::Completed,
            'completed_at' => now(),
        ]);
        $attempt->save();

        $this->adaptive->onOnlineExamCompleted($attempt);
        $this->gradeSync->syncOnlineExamAttempt($attempt);

        $student = $attempt->student;
        if ($student !== null && $score >= 70) {
            $this->gamification->awardXp(
                $student,
                ExperienceSource::ExamApproved,
                70,
                'Examen aprobado',
                $exam
            );
            if ($score >= 90) {
                $this->gamification->awardXp(
                    $student,
                    ExperienceSource::ExamOutstanding,
                    100,
                    'Examen destacado',
                    $exam
                );
            }
        }

        $this->audit->log(
            AuditAction::Assessment,
            AuditModule::Lms,
            $attempt->user_id ? User::query()->find($attempt->user_id) : null,
            OnlineExamAttempt::class,
            $attempt->id,
            'Evaluación online finalizada',
            null,
            ['score_percent' => $score],
            AuditResult::Success,
        );

        return $attempt;
    }

    /**
     * @param  Collection<int, OnlineExamQuestion>  $questions
     * @param  array<int, mixed>  $answers
     */
    private function scoreAnswers(Collection $questions, array $answers, OnlineExamGradingMode $mode): float
    {
        if ($mode === OnlineExamGradingMode::Manual) {
            return 0;
        }

        $earned = 0;
        $total = 0;

        foreach ($questions as $q) {
            $points = (int) $q->points;
            $total += $points;
            $given = $answers[$q->id] ?? null;

            if ($given === null) {
                continue;
            }

            if ($this->isCorrect($q, $given)) {
                $earned += $points;
            } elseif ($mode === OnlineExamGradingMode::Mixed && $q->question_type === OnlineExamQuestionType::ShortAnswer) {
                $earned += (int) round($points * 0.5);
            }
        }

        if ($total === 0) {
            return 0;
        }

        return round(($earned / $total) * 100, 2);
    }

    private function isCorrect(OnlineExamQuestion $q, mixed $given): bool
    {
        $correct = $q->correct_answer;

        return match ($q->question_type) {
            OnlineExamQuestionType::MultipleChoice, OnlineExamQuestionType::TrueFalse => (string) $given === (string) ($correct['value'] ?? $correct),
            OnlineExamQuestionType::ShortAnswer => is_array($correct)
                ? in_array(mb_strtolower(trim((string) $given)), array_map('mb_strtolower', (array) ($correct['values'] ?? [])), true)
                : mb_strtolower(trim((string) $given)) === mb_strtolower(trim((string) $correct)),
            OnlineExamQuestionType::Essay => false,
        };
    }
}
