<?php

namespace App\Http\Controllers;

use App\Http\Requests\Intranet\StoreEvaluationRequest;
use App\Http\Requests\Intranet\UpdateEvaluationRequest;
use App\Models\AcademicYear;
use App\Models\EducationalLevel;
use App\Models\Evaluation;
use App\Models\Subject;
use App\Services\EvaluationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EvaluationController extends Controller
{
    public function __construct(
        private readonly EvaluationService $evaluationService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Evaluation::class);

        return Inertia::render('Intranet/Academic/Evaluations/Index', [
            'evaluations' => $this->evaluationService->paginate($request),
            'filters' => [
                'search' => (string) $request->query('search', ''),
                'subject_id' => (string) $request->query('subject_id', ''),
                'section_id' => (string) $request->query('section_id', ''),
                'academic_year_id' => (string) $request->query('academic_year_id', ''),
            ],
            'catalog' => $this->catalog(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Evaluation::class);

        return Inertia::render('Intranet/Academic/Evaluations/Create', [
            'catalog' => $this->catalog(withAcademicTree: true),
        ]);
    }

    public function store(StoreEvaluationRequest $request): RedirectResponse
    {
        Evaluation::query()->create([
            ...$request->validated(),
            'created_by_user_id' => (int) $request->user()->id,
        ]);

        return redirect()->route('intranet.academic.evaluations.index')
            ->with('success', 'Evaluación creada correctamente.');
    }

    public function show(Evaluation $evaluation): Response
    {
        $this->authorize('view', $evaluation);
        $evaluation->load(['subject', 'academicYear', 'educationalLevel', 'grade', 'section', 'createdBy']);

        return Inertia::render('Intranet/Academic/Evaluations/Show', [
            'evaluation' => $evaluation,
        ]);
    }

    public function edit(Evaluation $evaluation): Response
    {
        $this->authorize('update', $evaluation);

        return Inertia::render('Intranet/Academic/Evaluations/Edit', [
            'evaluation' => $evaluation,
            'catalog' => $this->catalog(withAcademicTree: true),
        ]);
    }

    public function update(UpdateEvaluationRequest $request, Evaluation $evaluation): RedirectResponse
    {
        $evaluation->update($request->validated());

        return redirect()->route('intranet.academic.evaluations.index')
            ->with('success', 'Evaluación actualizada correctamente.');
    }

    /**
     * @return array<string,mixed>
     */
    private function catalog(bool $withAcademicTree = false): array
    {
        $catalog = [
            'subjects' => Subject::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'code'])
                ->map(fn (Subject $subject): array => [
                    'value' => (string) $subject->id,
                    'label' => $subject->name.' ('.$subject->code.')',
                ])->values()->all(),
            'academic_years' => AcademicYear::query()->orderByDesc('year')->get(['id', 'name', 'year', 'is_active'])
                ->map(fn (AcademicYear $year): array => [
                    'value' => (string) $year->id,
                    'label' => $year->name.' ('.$year->year.')'.($year->is_active ? ' - Activo' : ''),
                ])->values()->all(),
        ];

        if (! $withAcademicTree) {
            return $catalog;
        }

        $levels = EducationalLevel::query()
            ->where('is_active', true)
            ->with(['grades.sections'])
            ->orderBy('name')
            ->get();

        $catalog['levels'] = $levels->map(fn (EducationalLevel $level): array => [
            'id' => $level->id,
            'name' => $level->name,
            'grades' => $level->grades->map(fn ($grade): array => [
                'id' => $grade->id,
                'name' => $grade->name,
                'sections' => $grade->sections->map(fn ($section): array => [
                    'id' => $section->id,
                    'name' => $section->name,
                ])->values()->all(),
            ])->values()->all(),
        ])->values()->all();

        return $catalog;
    }
}
