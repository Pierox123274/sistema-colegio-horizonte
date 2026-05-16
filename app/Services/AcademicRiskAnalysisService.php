<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\GradeRecord;
use App\Models\Student;
use App\Models\User;

/**
 * Análisis heurístico de riesgo académico (reglas locales + datos agregados).
 * Complementa respuestas de IA sin sustituir criterio docente ni directivo.
 */
final class AcademicRiskAnalysisService
{
    public function __construct(
        private readonly StudentContextService $studentContext,
        private readonly TeacherContextService $teacherContext,
    ) {}

    /**
     * @return array{
     *   score: int,
     *   level: string,
     *   average: float|null,
     *   attendance_pct: float|null,
     *   grade_samples: int,
     *   attendance_records: int,
     *   flags: list<string>
     * }
     */
    public function studentRisk(Student $student): array
    {
        $grades = GradeRecord::query()->where('student_id', $student->id)->get(['score']);
        $avg = $grades->isEmpty() ? null : round((float) $grades->avg('score'), 2);

        $attRows = Attendance::query()->where('student_id', $student->id)->get();
        $m = $this->studentContext->attendanceMetrics($attRows);
        $pct = (float) ($m['attendance_percentage'] ?? 0);

        $flags = [];
        if ($avg !== null && $avg < 11) {
            $flags[] = 'promedio_bajo';
        }
        if ($pct < 75 && ($m['total'] ?? 0) >= 3) {
            $flags[] = 'asistencia_baja';
        }
        if (($m['absence_count'] ?? 0) >= 5) {
            $flags[] = 'ausentismo_relevante';
        }

        $score = $this->computeScore($avg, $pct, $flags);

        return [
            'score' => $score,
            'level' => $this->levelFromScore($score),
            'average' => $avg,
            'attendance_pct' => ($m['total'] ?? 0) > 0 ? $pct : null,
            'grade_samples' => $grades->count(),
            'attendance_records' => (int) ($m['total'] ?? 0),
            'flags' => $flags,
        ];
    }

    /**
     * Estudiantes en secciones del docente (año activo) con métricas de riesgo.
     *
     * @return list<array<string, mixed>>
     */
    public function studentsAtRiskForTeacher(User $teacher): array
    {
        $year = AcademicYear::query()->where('is_active', true)->first();
        if ($year === null) {
            return [];
        }

        $sectionIds = $this->teacherContext->activeSectionIdsFor($teacher);
        if ($sectionIds === []) {
            return [];
        }

        $studentIds = Enrollment::query()
            ->where('academic_year_id', $year->id)
            ->whereIn('section_id', $sectionIds)
            ->where('status', EnrollmentStatus::Matriculado->value)
            ->pluck('student_id')
            ->unique()
            ->values();

        $students = Student::query()
            ->whereIn('id', $studentIds)
            ->with([
                'enrollments' => fn ($q) => $q
                    ->where('academic_year_id', $year->id)
                    ->whereIn('section_id', $sectionIds)
                    ->with('section:id,name'),
            ])
            ->orderBy('last_name')
            ->get();

        $out = [];
        foreach ($students as $student) {
            $risk = $this->studentRisk($student);
            $enrollment = $student->enrollments->first();
            $out[] = [
                'student' => [
                    'id' => $student->id,
                    'code' => $student->code,
                    'full_name' => $student->fullName(),
                ],
                'section' => $enrollment?->section?->only(['id', 'name']),
                'risk' => $risk,
            ];
        }

        usort($out, fn (array $a, array $b): int => ($b['risk']['score'] ?? 0) <=> ($a['risk']['score'] ?? 0));

        return $out;
    }

    /**
     * @return array{
     *   total_students: int,
     *   by_level: array{high: int, medium: int, low: int},
     *   high_risk_samples: list<array<string, mixed>>
     * }
     */
    public function institutionOverview(): array
    {
        $year = AcademicYear::query()->where('is_active', true)->first();
        if ($year === null) {
            return [
                'total_students' => 0,
                'by_level' => ['high' => 0, 'medium' => 0, 'low' => 0],
                'high_risk_samples' => [],
            ];
        }

        $studentIds = Enrollment::query()
            ->where('academic_year_id', $year->id)
            ->where('status', EnrollmentStatus::Matriculado->value)
            ->distinct()
            ->pluck('student_id');

        $by = ['high' => 0, 'medium' => 0, 'low' => 0];
        $highSamples = [];

        foreach ($studentIds as $sid) {
            $student = Student::query()->find($sid);
            if ($student === null) {
                continue;
            }
            $risk = $this->studentRisk($student);
            match ($risk['level']) {
                'alto' => $by['high']++,
                'medio' => $by['medium']++,
                default => $by['low']++,
            };

            if ($risk['level'] === 'alto' && count($highSamples) < 12) {
                $highSamples[] = [
                    'student' => [
                        'id' => $student->id,
                        'code' => $student->code,
                        'full_name' => $student->fullName(),
                    ],
                    'risk' => $risk,
                ];
            }
        }

        return [
            'total_students' => (int) $studentIds->count(),
            'by_level' => $by,
            'high_risk_samples' => $highSamples,
        ];
    }

    private function computeScore(?float $avg, float $attPct, array $flags): int
    {
        $score = 10;

        if ($avg !== null) {
            if ($avg < 10) {
                $score += 40;
            } elseif ($avg < 11) {
                $score += 28;
            } elseif ($avg < 13) {
                $score += 15;
            }
        }

        if ($attPct < 60) {
            $score += 35;
        } elseif ($attPct < 75) {
            $score += 22;
        } elseif ($attPct < 85) {
            $score += 10;
        }

        foreach ($flags as $f) {
            if ($f === 'ausentismo_relevante') {
                $score += 12;
            }
        }

        return min(100, $score);
    }

    private function levelFromScore(int $score): string
    {
        if ($score >= 70) {
            return 'alto';
        }
        if ($score >= 45) {
            return 'medio';
        }

        return 'bajo';
    }
}
