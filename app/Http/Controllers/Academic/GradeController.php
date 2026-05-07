<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\StoreGradeRequest;
use App\Http\Requests\Intranet\UpdateGradeRequest;
use App\Models\EducationalLevel;
use App\Models\Grade;
use App\Services\GradeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GradeController extends Controller
{
    public function __construct(
        private readonly GradeService $service
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Grade::class);

        return Inertia::render('Intranet/Academic/Grades/Index', [
            'grades' => $this->service->paginateForIndex($request),
            'stats' => $this->service->indexStats(),
            'filters' => [
                'search' => $request->query('search', ''),
                'educational_level_id' => $request->query('educational_level_id', ''),
                'is_active' => $request->query('is_active', ''),
            ],
            'catalog' => [
                'educational_levels' => EducationalLevel::query()
                    ->orderBy('code')
                    ->get(['id', 'code', 'name'])
                    ->map(fn (EducationalLevel $l): array => [
                        'value' => (string) $l->id,
                        'label' => $l->code.' — '.$l->name,
                    ])
                    ->values()
                    ->all(),
            ],
            'permissions' => [
                'manage' => $request->user()?->can('create', Grade::class) ?? false,
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Grade::class);

        return Inertia::render('Intranet/Academic/Grades/Create', [
            'catalog' => $this->levelsCatalog(),
        ]);
    }

    public function store(StoreGradeRequest $request): RedirectResponse
    {
        $grade = $this->service->create($request->validated());

        return redirect()
            ->route('intranet.academic.grades.show', $grade)
            ->with('success', 'Grado creado correctamente.');
    }

    public function show(Request $request, Grade $grade): Response
    {
        $this->authorize('view', $grade);

        $grade->load([
            'educationalLevel:id,code,name',
            'sections' => fn ($q) => $q->orderBy('code')->limit(50),
        ]);
        $grade->loadCount('sections');

        return Inertia::render('Intranet/Academic/Grades/Show', [
            'grade' => $grade,
            'permissions' => [
                'manage' => $request->user()?->can('update', $grade) ?? false,
            ],
        ]);
    }

    public function edit(Request $request, Grade $grade): Response
    {
        $this->authorize('update', $grade);

        return Inertia::render('Intranet/Academic/Grades/Edit', [
            'grade' => $grade,
            'catalog' => $this->levelsCatalog(),
        ]);
    }

    public function update(UpdateGradeRequest $request, Grade $grade): RedirectResponse
    {
        $this->service->update($grade, $request->validated());

        return redirect()
            ->route('intranet.academic.grades.show', $grade)
            ->with('success', 'Grado actualizado.');
    }

    public function destroy(Grade $grade): RedirectResponse
    {
        $this->authorize('delete', $grade);

        if ($grade->sections()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar el grado mientras tenga secciones asociadas.');
        }

        $this->service->delete($grade);

        return redirect()
            ->route('intranet.academic.grades.index')
            ->with('success', 'Grado eliminado.');
    }

    /**
     * @return array{educational_levels: list<array{value: string, label: string}>}
     */
    private function levelsCatalog(): array
    {
        return [
            'educational_levels' => EducationalLevel::query()
                ->orderBy('code')
                ->get(['id', 'code', 'name'])
                ->map(fn (EducationalLevel $l): array => [
                    'value' => (string) $l->id,
                    'label' => $l->code.' — '.$l->name,
                ])
                ->values()
                ->all(),
        ];
    }
}
