<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\AcademicYear;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\GradeRecord;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AcademicGradeService
{
    public function paginate(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = GradeRecord::query()
            ->with([
                'student:id,code,first_name,last_name',
                'evaluation:id,subject_id,title,period,evaluated_at,section_id',
                'evaluation.subject:id,name,code',
                'evaluation.section:id,name',
                'recordedBy:id,name',
            ])
            ->orderByDesc('id');

        if ($request->filled('academic_year_id')) {
            $query->whereHas('evaluation', fn ($q) => $q->where('academic_year_id', (int) $request->query('academic_year_id')));
        }
        if ($request->filled('section_id')) {
            $query->whereHas('evaluation', fn ($q) => $q->where('section_id', (int) $request->query('section_id')));
        }
        if ($request->filled('subject_id')) {
            $query->whereHas('evaluation', fn ($q) => $q->where('subject_id', (int) $request->query('subject_id')));
        }
        if ($request->filled('evaluation_id')) {
            $query->where('evaluation_id', (int) $request->query('evaluation_id'));
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', (int) $request->query('student_id'));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string,mixed>  $data
     */
    public function registerBatch(array $data, int $userId): void
    {
        DB::transaction(function () use ($data, $userId): void {
            foreach ($data['entries'] as $entry) {
                $record = GradeRecord::query()
                    ->where('evaluation_id', (int) $data['evaluation_id'])
                    ->where('student_id', (int) $entry['student_id'])
                    ->first();

                if ($record) {
                    $record->update([
                        'score' => $entry['score'],
                        'observations' => $entry['observations'] ?: null,
                        'recorded_by_user_id' => $userId,
                    ]);

                    continue;
                }

                GradeRecord::query()->create([
                    'evaluation_id' => (int) $data['evaluation_id'],
                    'student_id' => (int) $entry['student_id'],
                    'score' => $entry['score'],
                    'observations' => $entry['observations'] ?: null,
                    'recorded_by_user_id' => $userId,
                ]);
            }
        });
    }

    /**
     * @return array{students:list<array<string,mixed>>,records:array<int,array<string,mixed>>}
     */
    public function batchContext(Evaluation $evaluation): array
    {
        $enrollments = Enrollment::query()
            ->with(['student:id,code,first_name,last_name,document_number'])
            ->where('academic_year_id', $evaluation->academic_year_id)
            ->where('section_id', $evaluation->section_id)
            ->whereIn('status', $this->activeEnrollmentStatuses())
            ->orderBy('id')
            ->get();

        $students = $enrollments->map(fn (Enrollment $enrollment): array => [
            'id' => $enrollment->student->id,
            'code' => $enrollment->student->code,
            'first_name' => $enrollment->student->first_name,
            'last_name' => $enrollment->student->last_name,
            'document_number' => $enrollment->student->document_number,
        ])->values()->all();

        $records = GradeRecord::query()
            ->where('evaluation_id', $evaluation->id)
            ->get()
            ->keyBy('student_id')
            ->map(fn (GradeRecord $record): array => [
                'score' => (float) $record->score,
                'observations' => $record->observations,
            ])->all();

        return [
            'students' => $students,
            'records' => $records,
        ];
    }

    public function studentHistory(Student $student, int $perPage = 25): LengthAwarePaginator
    {
        return GradeRecord::query()
            ->with([
                'evaluation:id,subject_id,section_id,title,period,evaluated_at',
                'evaluation.subject:id,name,code',
                'evaluation.section:id,name',
            ])
            ->where('student_id', $student->id)
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string,string>  $filters
     */
    public function reportQuery(array $filters)
    {
        $query = GradeRecord::query()
            ->with([
                'student:id,code,first_name,last_name',
                'evaluation:id,subject_id,academic_year_id,educational_level_id,grade_id,section_id,title,period,evaluated_at',
                'evaluation.subject:id,name,code',
                'evaluation.academicYear:id,name,year',
                'evaluation.educationalLevel:id,name',
                'evaluation.grade:id,name',
                'evaluation.section:id,name',
                'recordedBy:id,name',
            ]);

        if (! empty($filters['academic_year_id'])) {
            $query->whereHas('evaluation', fn ($q) => $q->where('academic_year_id', (int) $filters['academic_year_id']));
        }
        if (! empty($filters['section_id'])) {
            $query->whereHas('evaluation', fn ($q) => $q->where('section_id', (int) $filters['section_id']));
        }
        if (! empty($filters['subject_id'])) {
            $query->whereHas('evaluation', fn ($q) => $q->where('subject_id', (int) $filters['subject_id']));
        }
        if (! empty($filters['educational_level_id'])) {
            $query->whereHas('evaluation', fn ($q) => $q->where('educational_level_id', (int) $filters['educational_level_id']));
        }
        if (! empty($filters['grade_id'])) {
            $query->whereHas('evaluation', fn ($q) => $q->where('grade_id', (int) $filters['grade_id']));
        }
        if (! empty($filters['period'])) {
            $query->whereHas('evaluation', fn ($q) => $q->where('period', $filters['period']));
        }
        if (! empty($filters['evaluation_id'])) {
            $query->where('evaluation_id', (int) $filters['evaluation_id']);
        }
        if (! empty($filters['student_id'])) {
            $query->where('student_id', (int) $filters['student_id']);
        }

        return $query->orderBy('evaluation_id')->orderBy('student_id');
    }

    /**
     * @param  Collection<int, GradeRecord>  $rows
     * @return array<string,mixed>
     */
    public function metrics(Collection $rows): array
    {
        $courseAverage = round((float) $rows->avg('score'), 2);
        $periodAverages = $rows
            ->groupBy(fn (GradeRecord $record) => $record->evaluation?->period ?? 'Sin periodo')
            ->map(fn (Collection $items): float => round((float) $items->avg('score'), 2))
            ->all();
        $generalAverage = round((float) $rows->groupBy('student_id')->map(fn (Collection $items): float => (float) $items->avg('score'))->avg(), 2);

        $ranking = $rows
            ->groupBy('student_id')
            ->map(function (Collection $items): array {
                $student = $items->first()?->student;

                return [
                    'student' => trim(($student?->last_name ?? '').', '.($student?->first_name ?? '')),
                    'code' => $student?->code ?? '',
                    'average' => round((float) $items->avg('score'), 2),
                ];
            })
            ->sortByDesc('average')
            ->take(10)
            ->values()
            ->all();

        $riskStudents = $rows
            ->groupBy('student_id')
            ->filter(fn (Collection $items): bool => (float) $items->avg('score') < 11)
            ->map(function (Collection $items): array {
                $student = $items->first()?->student;

                return [
                    'student' => trim(($student?->last_name ?? '').', '.($student?->first_name ?? '')),
                    'code' => $student?->code ?? '',
                    'average' => round((float) $items->avg('score'), 2),
                ];
            })
            ->values()
            ->all();

        return [
            'total_records' => $rows->count(),
            'course_average' => $courseAverage,
            'general_average' => $generalAverage ?: 0,
            'period_averages' => $periodAverages,
            'ranking' => $ranking,
            'risk_students' => $riskStudents,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function catalog(): array
    {
        $academicYears = AcademicYear::query()->orderByDesc('year')->get(['id', 'name', 'year', 'is_active']);
        $sections = Section::query()->orderBy('name')->get(['id', 'name']);
        $levels = EducationalLevel::query()->orderBy('name')->get(['id', 'name']);
        $grades = Grade::query()->orderBy('name')->get(['id', 'name']);
        $subjects = Subject::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);
        $evaluations = Evaluation::query()
            ->with(['subject:id,name,code', 'section:id,name'])
            ->where('is_active', true)
            ->orderByDesc('evaluated_at')
            ->get(['id', 'subject_id', 'section_id', 'title', 'period', 'evaluated_at']);
        $students = Student::query()->orderBy('last_name')->limit(500)->get(['id', 'first_name', 'last_name', 'code']);

        return [
            'academic_years' => $academicYears->map(fn (AcademicYear $y): array => [
                'value' => (string) $y->id,
                'label' => $y->name.' ('.$y->year.')'.($y->is_active ? ' - Activo' : ''),
            ])->values()->all(),
            'sections' => $sections->map(fn (Section $s): array => ['value' => (string) $s->id, 'label' => $s->name])->values()->all(),
            'levels' => $levels->map(fn (EducationalLevel $l): array => ['value' => (string) $l->id, 'label' => $l->name])->values()->all(),
            'grades' => $grades->map(fn (Grade $g): array => ['value' => (string) $g->id, 'label' => $g->name])->values()->all(),
            'subjects' => $subjects->map(fn (Subject $s): array => ['value' => (string) $s->id, 'label' => $s->name.' ('.$s->code.')'])->values()->all(),
            'evaluations' => $evaluations->map(fn (Evaluation $e): array => [
                'value' => (string) $e->id,
                'label' => $e->title.' - '.$e->subject?->name.' - '.$e->section?->name.' ('.$e->period.')',
            ])->values()->all(),
            'periods' => $evaluations->pluck('period')->unique()->sort()->values()->map(fn (string $period): array => [
                'value' => $period,
                'label' => $period,
            ])->all(),
            'students' => $students->map(fn (Student $s): array => [
                'value' => (string) $s->id,
                'label' => trim($s->last_name.', '.$s->first_name.' ('.$s->code.')'),
            ])->values()->all(),
        ];
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
