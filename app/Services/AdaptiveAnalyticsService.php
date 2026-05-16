<?php

namespace App\Services;

use App\Enums\DiagnosticAttemptStatus;
use App\Enums\EnrollmentStatus;
use App\Models\DiagnosticAttempt;
use App\Models\Student;
use App\Models\StudentAdaptiveProfile;
use App\Models\User;

/**
 * Métricas agregadas de aprendizaje adaptativo (sin IA).
 */
final class AdaptiveAnalyticsService
{
    public function __construct(
        private readonly TeacherContextService $teacherContext,
    ) {}

    /**
     * @return array{
     *   attempt_count: int,
     *   avg_score: float|null,
     *   by_level: array<string, int>,
     *   weak_topic_hits: array<string, int>
     * }
     */
    public function institutionOverview(): array
    {
        $attemptCount = DiagnosticAttempt::query()->where('status', DiagnosticAttemptStatus::Completed)->count();

        $avg = StudentAdaptiveProfile::query()->whereNotNull('last_diagnostic_score')->avg('last_diagnostic_score');

        $byLevel = StudentAdaptiveProfile::query()
            ->whereNotNull('last_classified_level')
            ->selectRaw('last_classified_level as level, count(*) as c')
            ->groupBy('last_classified_level')
            ->pluck('c', 'level')
            ->all();

        $weak = [];
        $json = DiagnosticAttempt::query()
            ->whereNotNull('weakness_by_topic')
            ->pluck('weakness_by_topic');
        foreach ($json as $row) {
            if (! is_array($row)) {
                continue;
            }
            foreach ($row as $topic => $n) {
                $weak[(string) $topic] = ($weak[(string) $topic] ?? 0) + (int) $n;
            }
        }

        return [
            'attempt_count' => $attemptCount,
            'avg_score' => $avg !== null ? round((float) $avg, 2) : null,
            'by_level' => array_map('intval', $byLevel),
            'weak_topic_hits' => $weak,
        ];
    }

    /**
     * Estudiantes con nivel básico o sin datos en secciones del docente.
     *
     * @return list<array<string, mixed>>
     */
    public function teacherLowLevelStudents(User $teacher): array
    {
        $sectionIds = $this->teacherContext->activeSectionIdsFor($teacher);
        if ($sectionIds === []) {
            return [];
        }

        $studentIds = Student::query()
            ->whereHas('enrollments', function ($q) use ($sectionIds): void {
                $q->whereIn('section_id', $sectionIds);
            })
            ->pluck('id')
            ->all();

        if ($studentIds === []) {
            return [];
        }

        return StudentAdaptiveProfile::query()
            ->whereIn('student_id', $studentIds)
            ->where(function ($q): void {
                $q->where('last_classified_level', 'basic')
                    ->orWhereNull('last_classified_level');
            })
            ->with('student:id,first_name,last_name,code')
            ->orderBy('last_diagnostic_score')
            ->limit(50)
            ->get()
            ->map(fn (StudentAdaptiveProfile $p) => [
                'student_id' => $p->student_id,
                'full_name' => $p->student?->fullName(),
                'code' => $p->student?->code,
                'level' => $p->last_classified_level,
                'score' => $p->last_diagnostic_score,
            ])
            ->values()
            ->all();
    }

    /**
     * Temas débiles agregados por intentos en sección del docente.
     *
     * @return array<string, int>
     */
    public function teacherWeakTopics(User $teacher): array
    {
        $sectionIds = $this->teacherContext->activeSectionIdsFor($teacher);
        if ($sectionIds === []) {
            return [];
        }

        $ids = Student::query()
            ->whereHas('enrollments', fn ($q) => $q->whereIn('section_id', $sectionIds))
            ->pluck('id');

        $weak = [];
        $attempts = DiagnosticAttempt::query()
            ->whereIn('student_id', $ids)
            ->whereNotNull('weakness_by_topic')
            ->pluck('weakness_by_topic');

        foreach ($attempts as $row) {
            if (! is_array($row)) {
                continue;
            }
            foreach ($row as $t => $n) {
                $weak[(string) $t] = ($weak[(string) $t] ?? 0) + (int) $n;
            }
        }

        arsort($weak);

        return $weak;
    }

    /**
     * Estudiantes matriculados en secciones del docente sin diagnóstico completado registrado.
     */
    public function teacherStudentsWithoutDiagnosticCount(User $teacher): int
    {
        $sectionIds = $this->teacherContext->activeSectionIdsFor($teacher);
        if ($sectionIds === []) {
            return 0;
        }

        $studentIds = Student::query()
            ->whereHas('enrollments', function ($q) use ($sectionIds): void {
                $q->whereIn('section_id', $sectionIds)
                    ->where('status', EnrollmentStatus::Matriculado->value);
            })
            ->pluck('id');

        if ($studentIds->isEmpty()) {
            return 0;
        }

        $withDiagnostic = StudentAdaptiveProfile::query()
            ->whereIn('student_id', $studentIds)
            ->whereNotNull('last_diagnostic_at')
            ->pluck('student_id');

        return $studentIds->diff($withDiagnostic)->count();
    }
}
