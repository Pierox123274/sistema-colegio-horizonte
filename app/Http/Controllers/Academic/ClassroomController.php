<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\StoreClassroomRequest;
use App\Http\Requests\Intranet\UpdateClassroomRequest;
use App\Models\Classroom;
use App\Models\Section;
use App\Services\ClassroomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClassroomController extends Controller
{
    public function __construct(
        private readonly ClassroomService $service
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Classroom::class);

        return Inertia::render('Intranet/Academic/Classrooms/Index', [
            'classrooms' => $this->service->paginateForIndex($request),
            'stats' => $this->service->indexStats(),
            'filters' => [
                'search' => $request->query('search', ''),
                'section_id' => $request->query('section_id', ''),
                'is_active' => $request->query('is_active', ''),
            ],
            'catalog' => [
                'sections' => $this->sectionsForFilter(),
            ],
            'permissions' => [
                'manage' => $request->user()?->can('create', Classroom::class) ?? false,
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Classroom::class);

        return Inertia::render('Intranet/Academic/Classrooms/Create', [
            'catalog' => [
                'sections' => $this->sectionsForFilter(),
            ],
        ]);
    }

    public function store(StoreClassroomRequest $request): RedirectResponse
    {
        $classroom = $this->service->create($request->validated());

        return redirect()
            ->route('intranet.academic.classrooms.show', $classroom)
            ->with('success', 'Aula creada correctamente.');
    }

    public function show(Request $request, Classroom $classroom): Response
    {
        $this->authorize('view', $classroom);

        $classroom->load(['section.grade.educationalLevel:id,code,name']);

        return Inertia::render('Intranet/Academic/Classrooms/Show', [
            'classroom' => $classroom,
            'permissions' => [
                'manage' => $request->user()?->can('update', $classroom) ?? false,
            ],
        ]);
    }

    public function edit(Request $request, Classroom $classroom): Response
    {
        $this->authorize('update', $classroom);

        return Inertia::render('Intranet/Academic/Classrooms/Edit', [
            'classroom' => $classroom,
            'catalog' => [
                'sections' => $this->sectionsForFilter(),
            ],
        ]);
    }

    public function update(UpdateClassroomRequest $request, Classroom $classroom): RedirectResponse
    {
        $this->service->update($classroom, $request->validated());

        return redirect()
            ->route('intranet.academic.classrooms.show', $classroom)
            ->with('success', 'Aula actualizada.');
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        $this->authorize('delete', $classroom);

        $this->service->delete($classroom);

        return redirect()
            ->route('intranet.academic.classrooms.index')
            ->with('success', 'Aula eliminada.');
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function sectionsForFilter(): array
    {
        return Section::query()
            ->with(['grade.educationalLevel:id,code,name'])
            ->orderBy('grade_id')
            ->orderBy('code')
            ->get()
            ->map(function (Section $s): array {
                $g = $s->grade;
                $el = $g?->educationalLevel;

                return [
                    'value' => (string) $s->id,
                    'label' => ($el ? $el->code.' · ' : '').($g ? $g->name.' · ' : '').'Sec. '.$s->code,
                ];
            })
            ->all();
    }
}
