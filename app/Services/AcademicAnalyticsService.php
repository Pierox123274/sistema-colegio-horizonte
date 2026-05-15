<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\GradeRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class AcademicAnalyticsService
{
    public function __construct(
        private readonly AcademicGradeService $grades
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<int>|null  $sectionIds
     * @return array<string, mixed>
     */
    public function summary(array $filters, ?array $sectionIds = null): array
    {
        $yearId = $this->resolveAcademicYearId($filters);
        $rows = $this->gradeRecordsQuery($filters, $sectionIds)->get();
        $metrics = $this->grades->metrics($rows);

        return [
            'total_students' => $this->enrolledStudentsCount($yearId, $sectionIds),
            'attendance_average' => $this->attendanceAveragePercent($filters, $sectionIds),
            'risk_students_count' => count($metrics['risk_students']),
            'institutional_average' => $metrics['general_average'],
            'grade_records_count' => $metrics['total_records'],
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<int>|null  $sectionIds
     * @return list<array{label: string, average: float, records: int}>
     */
    public function performanceBySection(array $filters, ?array $sectionIds = null): array
    {
        $query = $this->gradeRecordsQuery($filters, $sectionIds)
            ->join('evaluations', 'evaluations.id', '=', 'grade_records.evaluation_id')
            ->join('sections', 'sections.id', '=', 'evaluations.section_id')
            ->select('sections.id', 'sections.name', DB::raw('AVG(grade_records.score) as avg_score'), DB::raw('COUNT(*) as records_count'))
            ->groupBy('sections.id', 'sections.name')
            ->orderByDesc('avg_score')
            ->limit(12);

        return $query->get()->map(fn ($row): array => [
            'label' => (string) $row->name,
            'average' => round((float) $row->avg_score, 2),
            'records' => (int) $row->records_count,
        ])->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<int>|null  $sectionIds
     * @return list<array<string, mixed>>
     */
    public function topStudents(array $filters, ?array $sectionIds = null, int $limit = 5): array
    {
        $rows = $this->gradeRecordsQuery($filters, $sectionIds)->get();
        $metrics = $this->grades->metrics($rows);

        return array_slice($metrics['ranking'], 0, $limit);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<int>|null  $sectionIds
     * @return list<array<string, mixed>>
     */
    public function riskStudents(array $filters, ?array $sectionIds = null, int $limit = 8): array
    {
        $rows = $this->gradeRecordsQuery($filters, $sectionIds)->get();
        $metrics = $this->grades->metrics($rows);

        return array_slice($metrics['risk_students'], 0, $limit);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<int>|null  $sectionIds
     * @return list<array{label: string, value: int}>
     */
    public function attendanceTrend(array $filters, ?array $sectionIds = null): array
    {
        $query = $this->attendanceQuery($filters, $sectionIds);

        return $query
            ->select(DB::raw('DATE(attendance_date) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->limit(14)
            ->get()
            ->map(fn ($row): array => [
                'label' => (string) $row->day,
                'value' => (int) $row->total,
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<int>|null  $sectionIds
     * @return list<array{student: string, code: string, absences: int}>
     */
    public function studentsWithMostAbsences(array $filters, ?array $sectionIds = null, int $limit = 8): array
    {
        $query = $this->attendanceQuery($filters, $sectionIds)
            ->where('attendances.status', AttendanceStatus::Falta->value)
            ->join('students', 'students.id', '=', 'attendances.student_id')
            ->select(
                'students.id',
                'students.first_name',
                'students.last_name',
                'students.code',
                DB::raw('COUNT(*) as absences')
            )
            ->groupBy('students.id', 'students.first_name', 'students.last_name', 'students.code')
            ->orderByDesc('absences')
            ->limit($limit);

        return $query->get()->map(fn ($row): array => [
            'student' => trim($row->last_name.', '.$row->first_name),
            'code' => (string) $row->code,
            'absences' => (int) $row->absences,
        ])->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<int>|null  $sectionIds
     * @return list<array{label: string, value: int, color?: string}>
     */
    public function attendanceStatusDistribution(array $filters, ?array $sectionIds = null): array
    {
        $rows = $this->attendanceQuery($filters, $sectionIds)
            ->select('attendances.status', DB::raw('COUNT(*) as total'))
            ->groupBy('attendances.status')
            ->get();

        $labels = collect(AttendanceStatus::options())->keyBy('value');

        return $rows->map(function ($row) use ($labels): array {
            $statusValue = $this->enumBackedValue($row->status);

            return [
                'label' => ($labels->get($statusValue) ?? [])['label'] ?? $statusValue,
                'value' => (int) $row->total,
            ];
        })->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<int>|null  $sectionIds
     * @return list<array<string, mixed>>
     */
    public function recentEvaluations(array $filters, ?array $sectionIds = null, int $limit = 6): array
    {
        $yearId = $this->resolveAcademicYearId($filters);

        $query = Evaluation::query()
            ->with(['subject:id,name', 'section:id,name'])
            ->where('is_active', true)
            ->orderByDesc('evaluated_at')
            ->limit($limit);

        if ($yearId !== null) {
            $query->where('academic_year_id', $yearId);
        }

        if ($sectionIds !== null) {
            $query->whereIn('section_id', $sectionIds);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('evaluated_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('evaluated_at', '<=', $filters['date_to']);
        }

        return $query->get()->map(fn (Evaluation $e): array => [
            'id' => $e->id,
            'title' => $e->title,
            'period' => $e->period,
            'subject' => $e->subject?->name,
            'section' => $e->section?->name,
            'evaluated_at' => $e->evaluated_at?->translatedFormat('d/m/Y'),
        ])->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function resolveAcademicYearId(array $filters): ?int
    {
        if (! empty($filters['academic_year_id'])) {
            return (int) $filters['academic_year_id'];
        }

        return AcademicYear::query()->where('is_active', true)->value('id');
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<int>|null  $sectionIds
     * @return Builder<GradeRecord>
     */
    public function gradeRecordsQuery(array $filters, ?array $sectionIds = null): Builder
    {
        $yearId = $this->resolveAcademicYearId($filters);

        $query = GradeRecord::query()->with([
            'student:id,code,first_name,last_name',
            'evaluation:id,subject_id,section_id,title,period,evaluated_at,academic_year_id',
        ]);

        if ($yearId !== null) {
            $query->whereHas('evaluation', fn (Builder $q) => $q->where('academic_year_id', $yearId));
        }

        if ($sectionIds !== null) {
            $query->whereHas('evaluation', fn (Builder $q) => $q->whereIn('section_id', $sectionIds));
        } elseif (! empty($filters['section_id'])) {
            $query->whereHas('evaluation', fn (Builder $q) => $q->where('section_id', (int) $filters['section_id']));
        }

        if (! empty($filters['date_from'])) {
            $query->whereHas('evaluation', fn (Builder $q) => $q->whereDate('evaluated_at', '>=', $filters['date_from']));
        }

        if (! empty($filters['date_to'])) {
            $query->whereHas('evaluation', fn (Builder $q) => $q->whereDate('evaluated_at', '<=', $filters['date_to']));
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<int>|null  $sectionIds
     */
    private function attendanceQuery(array $filters, ?array $sectionIds = null): Builder
    {
        $query = Attendance::query();

        if ($sectionIds !== null) {
            $query->whereIn('section_id', $sectionIds);
        } elseif (! empty($filters['section_id'])) {
            $query->where('section_id', (int) $filters['section_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('attendance_date', '>=', $filters['date_from']);
        } else {
            $query->whereDate('attendance_date', '>=', now()->subDays(30)->toDateString());
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('attendance_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * @param  list<int>|null  $sectionIds
     */
    private function enrolledStudentsCount(?int $yearId, ?array $sectionIds): int
    {
        if ($yearId === null) {
            return 0;
        }

        $query = Enrollment::query()
            ->where('academic_year_id', $yearId)
            ->where('status', EnrollmentStatus::Matriculado->value);

        if ($sectionIds !== null) {
            $query->whereIn('section_id', $sectionIds);
        }

        return $query->distinct('student_id')->count('student_id');
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<int>|null  $sectionIds
     */
    private function attendanceAveragePercent(array $filters, ?array $sectionIds): float
    {
        $total = $this->attendanceQuery($filters, $sectionIds)->count();
        if ($total === 0) {
            return 0;
        }

        $positive = $this->attendanceQuery($filters, $sectionIds)
            ->whereIn('status', [
                AttendanceStatus::Presente->value,
                AttendanceStatus::Tarde->value,
                AttendanceStatus::Justificado->value,
            ])
            ->count();

        return round(($positive / $total) * 100, 1);
    }

    private function enumBackedValue(mixed $value): string
    {
        if ($value instanceof AttendanceStatus) {
            return $value->value;
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        return (string) $value;
    }
}
