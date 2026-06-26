<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\ExperienceSource;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function __construct(
        private readonly GamificationService $gamification,
    ) {}

    public function paginateForIndex(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = Attendance::query()
            ->with([
                'student:id,code,first_name,last_name',
                'section:id,name',
                'grade:id,name',
                'educationalLevel:id,name',
                'recordedBy:id,name',
            ])
            ->orderByDesc('attendance_date')
            ->orderByDesc('id');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($sectionId = $request->query('section_id')) {
            $query->where('section_id', (int) $sectionId);
        }
        if ($studentId = $request->query('student_id')) {
            $query->where('student_id', (int) $studentId);
        }
        if ($date = $request->query('date')) {
            $query->whereDate('attendance_date', $date);
        }
        if ($from = $request->query('date_from')) {
            $query->whereDate('attendance_date', '>=', $from);
        }
        if ($to = $request->query('date_to')) {
            $query->whereDate('attendance_date', '<=', $to);
        }
        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->whereHas('student', fn ($s) => $s
                    ->where('code', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like))
                    ->orWhere('observation', 'like', $like);
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function registerBatch(array $data, int $userId): void
    {
        DB::transaction(function () use ($data, $userId): void {
            $attendanceDate = (string) Carbon::parse($data['attendance_date'])->toDateString();

            foreach ($data['entries'] as $entry) {
                $this->upsertAttendanceEntry($data, $entry, $attendanceDate, $userId);
            }

            $this->awardPresentAttendanceXp($data['entries']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $entry
     */
    private function upsertAttendanceEntry(array $data, array $entry, string $attendanceDate, int $userId): void
    {
        $payload = [
            'academic_year_id' => (int) $data['academic_year_id'],
            'educational_level_id' => (int) $data['educational_level_id'],
            'grade_id' => (int) $data['grade_id'],
            'status' => $entry['status'],
            'observation' => $entry['observation'] ?: null,
            'recorded_by_user_id' => $userId,
        ];

        $existing = Attendance::query()
            ->where('student_id', (int) $entry['student_id'])
            ->where('section_id', (int) $data['section_id'])
            ->whereDate('attendance_date', $attendanceDate)
            ->first();

        if ($existing) {
            $existing->update($payload);

            return;
        }

        Attendance::query()->create([
            ...$payload,
            'student_id' => (int) $entry['student_id'],
            'attendance_date' => $attendanceDate,
            'section_id' => (int) $data['section_id'],
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $entries
     */
    private function awardPresentAttendanceXp(array $entries): void
    {
        foreach ($entries as $entry) {
            if (($entry['status'] ?? '') !== 'presente') {
                continue;
            }

            $student = Student::query()->find((int) $entry['student_id']);
            if ($student === null) {
                continue;
            }

            $this->gamification->awardXp(
                $student,
                ExperienceSource::AttendanceDaily,
                20,
                'Asistencia efectiva registrada'
            );
        }
    }

    /**
     * @return array{students:list<array<string,mixed>>,records:array<int,array<string,mixed>>,context:array<string,mixed>}
     */
    public function batchContext(string $date, Section $section, ?AcademicYear $academicYear = null): array
    {
        $academicYear ??= AcademicYear::query()
            ->where('is_active', true)
            ->first();

        $enrollments = Enrollment::query()
            ->with(['student:id,code,first_name,last_name,document_number'])
            ->where('section_id', $section->id)
            ->when($academicYear, fn ($q) => $q->where('academic_year_id', $academicYear->id))
            ->whereIn('status', $this->activeEnrollmentStatuses())
            ->orderBy('id')
            ->get();

        $students = $enrollments->map(function (Enrollment $enrollment): array {
            return [
                'id' => $enrollment->student->id,
                'code' => $enrollment->student->code,
                'first_name' => $enrollment->student->first_name,
                'last_name' => $enrollment->student->last_name,
                'document_number' => $enrollment->student->document_number,
            ];
        })->values()->all();

        $records = Attendance::query()
            ->whereDate('attendance_date', $date)
            ->where('section_id', $section->id)
            ->get()
            ->keyBy('student_id')
            ->map(fn (Attendance $a): array => [
                'status' => $a->status->value,
                'observation' => $a->observation,
            ])
            ->all();

        return [
            'students' => $students,
            'records' => $records,
            'context' => [
                'attendance_date' => $date,
                'section_id' => $section->id,
                'grade_id' => $section->grade_id,
                'educational_level_id' => $section->grade->educational_level_id,
                'academic_year_id' => $academicYear?->id,
            ],
        ];
    }

    public function studentHistory(Student $student, int $perPage = 30): LengthAwarePaginator
    {
        return Attendance::query()
            ->with(['section:id,name', 'grade:id,name', 'educationalLevel:id,name', 'recordedBy:id,name'])
            ->where('student_id', $student->id)
            ->orderByDesc('attendance_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  Collection<int, Attendance>  $rows
     * @return array<string,mixed>
     */
    public function metrics(Collection $rows): array
    {
        $total = max(1, $rows->count());
        $presentes = $rows->where('status', 'presente')->count();
        $tardes = $rows->where('status', 'tarde')->count();
        $faltas = $rows->where('status', 'falta')->count();
        $justificados = $rows->where('status', 'justificado')->count();

        return [
            'total' => $rows->count(),
            'attendance_percentage' => round((($presentes + $justificados) / $total) * 100, 2),
            'late_count' => $tardes,
            'absence_count' => $faltas,
            'justified_count' => $justificados,
        ];
    }

    /**
     * @param  array<string,string>  $filters
     */
    public function reportQuery(array $filters)
    {
        $query = Attendance::query()
            ->with([
                'student:id,code,first_name,last_name',
                'section:id,name',
                'grade:id,name',
                'educationalLevel:id,name',
                'recordedBy:id,name',
            ]);

        if (! empty($filters['date'])) {
            $query->whereDate('attendance_date', $filters['date']);
        }
        if (! empty($filters['date_from'])) {
            $query->whereDate('attendance_date', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('attendance_date', '<=', $filters['date_to']);
        }
        if (! empty($filters['section_id'])) {
            $query->where('section_id', (int) $filters['section_id']);
        }
        if (! empty($filters['student_id'])) {
            $query->where('student_id', (int) $filters['student_id']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query
            ->orderBy('attendance_date')
            ->orderBy('educational_level_id')
            ->orderBy('grade_id')
            ->orderBy('section_id')
            ->orderBy('student_id');
    }

    /**
     * @return list<string>
     */
    private function activeEnrollmentStatuses(): array
    {
        return array_values(array_filter(
            EnrollmentStatus::values(),
            fn (string $value): bool => EnrollmentStatus::from($value)->blocksConcurrentEnrollment()
        ));
    }
}
