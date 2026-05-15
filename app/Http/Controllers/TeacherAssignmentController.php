<?php

namespace App\Http\Controllers;

use App\Enums\IntranetRole;
use App\Http\Requests\Intranet\StoreTeacherAssignmentRequest;
use App\Http\Requests\Intranet\UpdateTeacherAssignmentRequest;
use App\Models\AcademicYear;
use App\Models\EducationalLevel;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAssignmentController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', TeacherAssignment::class);

        $query = TeacherAssignment::query()
            ->with(['user:id,name,email', 'academicYear:id,name,year', 'educationalLevel:id,name', 'grade:id,name', 'section:id,name', 'subject:id,name'])
            ->orderByDesc('id');

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->query('user_id'));
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', (int) $request->query('academic_year_id'));
        }

        return Inertia::render('Intranet/Admin/TeacherAssignments/Index', [
            'assignments' => $query->paginate(20)->withQueryString(),
            'filters' => [
                'user_id' => (string) $request->query('user_id', ''),
                'academic_year_id' => (string) $request->query('academic_year_id', ''),
            ],
            'catalog' => [
                'teachers' => User::query()
                    ->whereHas('roles', fn ($q) => $q->where('name', IntranetRole::Docente->value))
                    ->orderBy('name')
                    ->get(['id', 'name', 'email'])
                    ->map(fn (User $u): array => [
                        'value' => (string) $u->id,
                        'label' => $u->name.' ('.$u->email.')',
                    ])
                    ->values()
                    ->all(),
                'academic_years' => AcademicYear::query()->orderByDesc('year')->get(['id', 'name', 'year'])
                    ->map(fn (AcademicYear $y): array => [
                        'value' => (string) $y->id,
                        'label' => $y->name.' ('.$y->year.')',
                    ])
                    ->values()
                    ->all(),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', TeacherAssignment::class);

        return Inertia::render('Intranet/Admin/TeacherAssignments/Create', [
            'catalog' => $this->formCatalog(),
        ]);
    }

    public function store(StoreTeacherAssignmentRequest $request)
    {
        TeacherAssignment::query()->create($request->validated());

        return redirect()
            ->route('intranet.admin.teacher-assignments.index')
            ->with('success', 'Asignación docente registrada.');
    }

    public function edit(TeacherAssignment $assignment): Response
    {
        $this->authorize('update', $assignment);

        $assignment->load(['user:id,name,email']);

        return Inertia::render('Intranet/Admin/TeacherAssignments/Edit', [
            'assignment' => $assignment,
            'catalog' => $this->formCatalog(),
        ]);
    }

    public function update(UpdateTeacherAssignmentRequest $request, TeacherAssignment $assignment)
    {
        $assignment->update($request->validated());

        return redirect()
            ->route('intranet.admin.teacher-assignments.index')
            ->with('success', 'Asignación docente actualizada.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formCatalog(): array
    {
        $levels = EducationalLevel::query()->with(['grades.sections'])->where('is_active', true)->orderBy('name')->get();

        return [
            'teachers' => User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', IntranetRole::Docente->value))
                ->orderBy('name')
                ->get(['id', 'name', 'email'])
                ->map(fn (User $u): array => [
                    'value' => (string) $u->id,
                    'label' => $u->name.' ('.$u->email.')',
                ])
                ->values()
                ->all(),
            'academic_years' => AcademicYear::query()->orderByDesc('year')->get(['id', 'name', 'year', 'is_active'])
                ->map(fn (AcademicYear $y): array => [
                    'value' => (string) $y->id,
                    'label' => $y->name.' ('.$y->year.')'.($y->is_active ? ' — Activo' : ''),
                ])
                ->values()
                ->all(),
            'levels' => $levels->map(fn (EducationalLevel $level): array => [
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
            ])->values()->all(),
            'subjects' => Subject::query()->where('is_active', true)->orderBy('name')->get(['id', 'name'])
                ->map(fn (Subject $s): array => [
                    'value' => (string) $s->id,
                    'label' => $s->name,
                ])
                ->values()
                ->all(),
        ];
    }
}
