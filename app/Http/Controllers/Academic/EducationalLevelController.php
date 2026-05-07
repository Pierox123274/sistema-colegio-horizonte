<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\StoreEducationalLevelRequest;
use App\Http\Requests\Intranet\UpdateEducationalLevelRequest;
use App\Models\EducationalLevel;
use App\Services\EducationalLevelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EducationalLevelController extends Controller
{
    public function __construct(
        private readonly EducationalLevelService $service
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', EducationalLevel::class);

        return Inertia::render('Intranet/Academic/Levels/Index', [
            'levels' => $this->service->paginateForIndex($request),
            'stats' => $this->service->indexStats(),
            'filters' => [
                'search' => $request->query('search', ''),
                'is_active' => $request->query('is_active', ''),
            ],
            'permissions' => [
                'manage' => $request->user()?->can('create', EducationalLevel::class) ?? false,
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', EducationalLevel::class);

        return Inertia::render('Intranet/Academic/Levels/Create');
    }

    public function store(StoreEducationalLevelRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('intranet.academic.levels.index')
            ->with('success', 'Nivel educativo creado correctamente.');
    }

    public function show(Request $request, EducationalLevel $educationalLevel): Response
    {
        $this->authorize('view', $educationalLevel);

        $educationalLevel->loadCount('grades');
        $educationalLevel->load([
            'grades' => fn ($q) => $q->orderBy('order'),
        ]);

        return Inertia::render('Intranet/Academic/Levels/Show', [
            'level' => $educationalLevel,
            'permissions' => [
                'manage' => $request->user()?->can('update', $educationalLevel) ?? false,
            ],
        ]);
    }

    public function edit(Request $request, EducationalLevel $educationalLevel): Response
    {
        $this->authorize('update', $educationalLevel);

        return Inertia::render('Intranet/Academic/Levels/Edit', [
            'level' => $educationalLevel,
        ]);
    }

    public function update(UpdateEducationalLevelRequest $request, EducationalLevel $educationalLevel): RedirectResponse
    {
        $this->service->update($educationalLevel, $request->validated());

        return redirect()
            ->route('intranet.academic.levels.show', $educationalLevel)
            ->with('success', 'Nivel educativo actualizado.');
    }

    public function destroy(EducationalLevel $educationalLevel): RedirectResponse
    {
        $this->authorize('delete', $educationalLevel);

        if ($educationalLevel->grades()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar el nivel mientras tenga grados asociados.');
        }

        $this->service->delete($educationalLevel);

        return redirect()
            ->route('intranet.academic.levels.index')
            ->with('success', 'Nivel educativo eliminado.');
    }
}
