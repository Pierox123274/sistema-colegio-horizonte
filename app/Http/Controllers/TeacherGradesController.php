<?php

namespace App\Http\Controllers;

use App\Enums\EnrollmentStatus;
use App\Http\Requests\Intranet\StoreGradeBatchRequest;
use App\Models\AcademicYear;
use App\Models\Evaluation;
use App\Models\GradeRecord;
use App\Models\Student;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Services\AcademicGradeService;
use App\Services\TeacherContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherGradesController extends Controller
{
    public function __construct(
        private readonly AcademicGradeService $academicGradeService,
        private readonly TeacherContextService $teacherContext
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', GradeRecord::class);

        $user = $request->user();
        $sectionScope = $this->resolveSectionScope($user);

        $recordsQuery = GradeRecord::query()
            ->with([
                'student:id,code,first_name,last_name',
                'evaluation:id,title,period,subject_id,section_id',
                'evaluation.subject:id,name',
                'evaluation.section:id,name',
            ])
            ->orderByDesc('id');

        if ($sectionScope !== null && $sectionScope !== []) {
            $recordsQuery->whereHas('evaluation', fn ($q) => $q->whereIn('section_id', $sectionScope));
        } elseif ($sectionScope !== null && $sectionScope === []) {
            $recordsQuery->whereRaw('1 = 0');
        }

        if ($sectionScope !== null && $request->filled('section_id')) {
            $sectionFilter = (int) $request->query('section_id');
            if ($sectionScope === [] || ! in_array($sectionFilter, $sectionScope, true)) {
                $recordsQuery->whereRaw('1 = 0');
            } else {
                $recordsQuery->whereHas('evaluation', fn ($q) => $q->where('section_id', $sectionFilter));
            }
        }

        if ($sectionScope !== null && $request->filled('subject_id')) {
            $subjectFilter = (int) $request->query('subject_id');
            $recordsQuery->whereHas('evaluation', fn ($q) => $q->where('subject_id', $subjectFilter));
        }

        $filterCatalog = ['sections' => [], 'subjects' => []];
        if ($sectionScope !== null && $user !== null) {
            $filterCatalog = [
                'sections' => $this->teacherContext->sectionFilterOptionsFor($user),
                'subjects' => $this->teacherContext->subjectFilterOptionsFor($user),
            ];
        }

        return Inertia::render('Teacher/Grades/Index', [
            'recent_records' => $recordsQuery->limit(25)->get(),
            'filters' => [
                'section_id' => (string) $request->query('section_id', ''),
                'subject_id' => (string) $request->query('subject_id', ''),
            ],
            'catalog' => $filterCatalog,
            'links' => [
                'records' => route('teacher.grades.records', absolute: false),
                'reports' => route('teacher.reports.index', absolute: false),
            ],
            'has_teaching_assignments' => $sectionScope === null || $sectionScope !== [],
            'teacher_portal_scoped' => $sectionScope !== null,
            'empty_message' => $this->teacherContext->emptyAssignmentsMessage(),
        ]);
    }

    public function records(Request $request): Response
    {
        $this->authorize('viewAny', GradeRecord::class);

        $user = $request->user();
        $sectionScope = $this->resolveSectionScope($user);

        $evaluation = null;
        if ($request->filled('evaluation_id')) {
            $evaluation = Evaluation::query()
                ->with(['subject:id,name', 'section:id,name'])
                ->find((int) $request->query('evaluation_id'));

            if ($evaluation !== null && $user !== null && $sectionScope !== null) {
                if ($sectionScope === [] || ! in_array($evaluation->section_id, $sectionScope, true)) {
                    abort(403, 'La evaluación no pertenece a sus secciones asignadas.');
                }
            }
        }

        if ($sectionScope !== null && $sectionScope !== [] && ! $request->filled('section_id')) {
            $request->merge(['section_id' => (string) $sectionScope[0]]);
        }

        return Inertia::render('Teacher/Grades/Records', [
            'grades' => $this->academicGradeService->paginate($request),
            'filters' => [
                'academic_year_id' => (string) $request->query('academic_year_id', ''),
                'educational_level_id' => (string) $request->query('educational_level_id', ''),
                'grade_id' => (string) $request->query('grade_id', ''),
                'section_id' => (string) $request->query('section_id', ''),
                'subject_id' => (string) $request->query('subject_id', ''),
                'period' => (string) $request->query('period', ''),
                'evaluation_id' => (string) $request->query('evaluation_id', ''),
                'student_id' => (string) $request->query('student_id', ''),
            ],
            'catalog' => $this->catalogForTeacher($user, $sectionScope),
            'batch' => $evaluation ? $this->academicGradeService->batchContext($evaluation) : null,
            'selected_evaluation' => $evaluation ? [
                'id' => $evaluation->id,
                'label' => $evaluation->title.' - '.$evaluation->subject?->name.' - '.$evaluation->section?->name.' ('.$evaluation->period.')',
            ] : null,
            'has_teaching_assignments' => $sectionScope === null || $sectionScope !== [],
            'teacher_portal_scoped' => $sectionScope !== null,
        ]);
    }

    public function store(StoreGradeBatchRequest $request): RedirectResponse
    {
        $this->authorize('create', GradeRecord::class);

        $user = $request->user();
        $evaluation = Evaluation::query()->findOrFail((int) $request->input('evaluation_id'));
        if ($user !== null) {
            $this->teacherContext->assertTeacherCanAccessSection($user, (int) $evaluation->section_id);
        }

        $this->academicGradeService->registerBatch($request->validated(), (int) $request->user()->id);

        return redirect()
            ->route('teacher.grades.records', [
                'evaluation_id' => (string) $request->input('evaluation_id'),
            ])
            ->with('success', 'Notas registradas correctamente.');
    }

    /**
     * @return list<int>|null
     */
    private function resolveSectionScope(?User $user): ?array
    {
        if ($user === null || ! $this->teacherContext->isDocentePortalScoped($user)) {
            return null;
        }

        return $this->teacherContext->activeSectionIdsFor($user);
    }

    /**
     * @param  list<int>|null  $sectionScope
     * @return array<string, mixed>
     */
    private function catalogForTeacher(?User $user, ?array $sectionScope): array
    {
        $catalog = $this->academicGradeService->catalog();

        if ($sectionScope === null || $user === null) {
            return $catalog;
        }

        if ($sectionScope === []) {
            return array_merge($catalog, [
                'sections' => [],
                'evaluations' => [],
                'students' => [],
            ]);
        }

        $catalog['sections'] = $this->teacherContext->sectionFilterOptionsFor($user);

        $subjectIds = TeacherAssignment::query()
            ->where('user_id', $user->id)
            ->where('academic_year_id', $this->teacherContext->activeAcademicYear()?->id)
            ->where('is_active', true)
            ->whereNotNull('subject_id')
            ->pluck('subject_id')
            ->unique()
            ->all();

        $catalog['subjects'] = array_values(array_filter(
            $catalog['subjects'] ?? [],
            fn (array $s): bool => in_array((int) $s['value'], $subjectIds, true)
        ));

        if ($catalog['subjects'] === [] && $subjectIds !== []) {
            $catalog['subjects'] = $this->teacherContext->subjectFilterOptionsFor($user);
        }

        $evaluationsQuery = Evaluation::query()
            ->with(['subject:id,name,code', 'section:id,name'])
            ->where('is_active', true)
            ->whereIn('section_id', $sectionScope)
            ->orderByDesc('evaluated_at');

        if ($subjectIds !== []) {
            $evaluationsQuery->whereIn('subject_id', $subjectIds);
        }

        $evaluations = $evaluationsQuery->get(['id', 'subject_id', 'section_id', 'title', 'period', 'evaluated_at']);

        $catalog['evaluations'] = $evaluations->map(fn (Evaluation $e): array => [
            'value' => (string) $e->id,
            'label' => $e->title.' - '.$e->subject?->name.' - '.$e->section?->name.' ('.$e->period.')',
        ])->values()->all();

        $catalog['periods'] = $evaluations->pluck('period')->unique()->sort()->values()->map(fn (string $period): array => [
            'value' => $period,
            'label' => $period,
        ])->all();

        $year = AcademicYear::query()->where('is_active', true)->first();
        if ($year !== null) {
            $studentIds = Student::query()
                ->whereHas('enrollments', function ($q) use ($year, $sectionScope): void {
                    $q->where('academic_year_id', $year->id)
                        ->where('status', EnrollmentStatus::Matriculado->value)
                        ->whereIn('section_id', $sectionScope);
                })
                ->orderBy('last_name')
                ->limit(500)
                ->get(['id', 'first_name', 'last_name', 'code']);

            $catalog['students'] = $studentIds->map(fn (Student $s): array => [
                'value' => (string) $s->id,
                'label' => trim($s->last_name.', '.$s->first_name.' ('.$s->code.')'),
            ])->values()->all();
        }

        return $catalog;
    }
}
