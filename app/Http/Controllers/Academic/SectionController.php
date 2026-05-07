<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\StoreSectionRequest;
use App\Http\Requests\Intranet\UpdateSectionRequest;
use App\Models\Grade;
use App\Models\Section;
use App\Services\SectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SectionController extends Controller
{
    public function __construct(
        private readonly SectionService $service
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Section::class);

        return Inertia::render('Intranet/Academic/Sections/Index', [
            'sections' => $this->service->paginateForIndex($request),
            'stats' => $this->service->indexStats(),
            'filters' => [
                'search' => $request->query('search', ''),
                'grade_id' => $request->query('grade_id', ''),
                'is_active' => $request->query('is_active', ''),
            ],
            'catalog' => [
                'grades' => $this->gradesForFilter(),
            ],
            'permissions' => [
                'manage' => $request->user()?->can('create', Section::class) ?? false,
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Section::class);

        return Inertia::render('Intranet/Academic/Sections/Create', [
            'catalog' => [
                'grades' => $this->gradesForFilter(),
            ],
        ]);
    }

    public function store(StoreSectionRequest $request): RedirectResponse
    {
        $section = $this->service->create($request->validated());

        return redirect()
            ->route('intranet.academic.sections.show', $section)
            ->with('success', 'Sección creada correctamente.');
    }

    public function show(Request $request, Section $section): Response
    {
        $this->authorize('view', $section);

        $section->load([
            'grade.educationalLevel:id,code,name',
            'classrooms' => fn ($q) => $q->orderBy('code')->limit(50),
        ]);
        $section->loadCount('classrooms');

        return Inertia::render('Intranet/Academic/Sections/Show', [
            'section' => $section,
            'permissions' => [
                'manage' => $request->user()?->can('update', $section) ?? false,
            ],
        ]);
    }

    public function edit(Request $request, Section $section): Response
    {
        $this->authorize('update', $section);

        return Inertia::render('Intranet/Academic/Sections/Edit', [
            'section' => $section,
            'catalog' => [
                'grades' => $this->gradesForFilter(),
            ],
        ]);
    }

    public function update(UpdateSectionRequest $request, Section $section): RedirectResponse
    {
        $this->service->update($section, $request->validated());

        return redirect()
            ->route('intranet.academic.sections.show', $section)
            ->with('success', 'Sección actualizada.');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $this->authorize('delete', $section);

        if ($section->classrooms()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar la sección mientras tenga aulas asociadas.');
        }

        $this->service->delete($section);

        return redirect()
            ->route('intranet.academic.sections.index')
            ->with('success', 'Sección eliminada.');
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function gradesForFilter(): array
    {
        return Grade::query()
            ->with('educationalLevel:id,code,name')
            ->orderBy('educational_level_id')
            ->orderBy('order')
            ->get()
            ->map(function (Grade $g): array {
                $el = $g->educationalLevel;

                return [
                    'value' => (string) $g->id,
                    'label' => ($el ? $el->code.' · ' : '').$g->name.' ('.$g->code.')',
                ];
            })
            ->all();
    }
}
