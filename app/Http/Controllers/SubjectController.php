<?php

namespace App\Http\Controllers;

use App\Http\Requests\Intranet\StoreSubjectRequest;
use App\Http\Requests\Intranet\UpdateSubjectRequest;
use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubjectController extends Controller
{
    public function __construct(
        private readonly SubjectService $subjectService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Subject::class);

        return Inertia::render('Intranet/Academic/Subjects/Index', [
            'subjects' => $this->subjectService->paginate($request),
            'filters' => [
                'search' => (string) $request->query('search', ''),
                'is_active' => (string) $request->query('is_active', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Subject::class);

        return Inertia::render('Intranet/Academic/Subjects/Create');
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::query()->create($request->validated());

        return redirect()->route('intranet.academic.subjects.index')
            ->with('success', 'Curso creado correctamente.');
    }

    public function show(Subject $subject): Response
    {
        $this->authorize('view', $subject);

        return Inertia::render('Intranet/Academic/Subjects/Show', [
            'subject' => $subject,
        ]);
    }

    public function edit(Subject $subject): Response
    {
        $this->authorize('update', $subject);

        return Inertia::render('Intranet/Academic/Subjects/Edit', [
            'subject' => $subject,
        ]);
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return redirect()->route('intranet.academic.subjects.index')
            ->with('success', 'Curso actualizado correctamente.');
    }
}
