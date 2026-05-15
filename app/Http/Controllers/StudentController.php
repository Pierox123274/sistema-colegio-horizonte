<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Enums\EducationalLevel;
use App\Enums\Gender;
use App\Enums\IntranetRole;
use App\Enums\StudentStatus;
use App\Http\Requests\Intranet\StoreStudentRequest;
use App\Http\Requests\Intranet\UpdateStudentRequest;
use App\Models\Guardian;
use App\Models\Student;
use App\Services\StudentService;
use App\Services\TeacherContextService;
use App\Support\StudentGradeCatalog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function __construct(
        private readonly StudentService $studentService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Student::class);

        $user = $request->user();
        $sectionScope = null;
        if ($user !== null
            && $user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasAnyRole([
                IntranetRole::Administrador->value,
                IntranetRole::Secretaria->value,
            ])
        ) {
            $sectionScope = app(TeacherContextService::class)->activeSectionIdsFor($user);
        }

        return Inertia::render('Intranet/Students/Index', [
            'students' => $this->studentService->paginateForIndex($request, 15, $sectionScope),
            'filters' => [
                'search' => $request->query('search', ''),
                'educational_level' => $request->query('educational_level', ''),
                'status' => $request->query('status', ''),
            ],
            'catalog' => [
                'educational_levels' => EducationalLevel::options(),
                'statuses' => StudentStatus::options(),
            ],
            'permissions' => [
                'manage' => $request->user()?->can('create', Student::class) ?? false,
            ],
            'teacher_section_scope' => $sectionScope !== null,
            'has_teaching_assignments' => $sectionScope === null || $sectionScope !== [],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Student::class);

        return Inertia::render('Intranet/Students/Create', [
            'catalog' => $this->formCatalog(),
        ]);
    }

    public function store(StoreStudentRequest $request)
    {
        $student = $this->studentService->createStudent($request->validated());

        return redirect()
            ->route('intranet.students.show', $student)
            ->with('success', 'Estudiante registrado correctamente.');
    }

    public function show(Request $request, Student $student): Response
    {
        $this->authorize('view', $student);

        $student->load([
            'guardians' => fn ($q) => $q->orderBy('guardians.last_name')->orderBy('guardians.first_name'),
        ]);

        $guardianLinks = $this->guardianLinksForStudent($student);
        $student->unsetRelation('guardians');

        return Inertia::render('Intranet/Students/Show', [
            'student' => $student,
            'guardian_links' => $guardianLinks,
            'permissions' => [
                'manage' => $request->user()?->can('update', $student) ?? false,
            ],
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function guardianLinksForStudent(Student $student): array
    {
        return $student->guardians->map(function (Guardian $guardian): array {
            $pivot = $guardian->pivot;

            return [
                'id' => $guardian->id,
                'full_name' => $guardian->fullName(),
                'relationship' => $pivot->getAttribute('relationship'),
                'phone' => $guardian->phone,
                'document_number' => $guardian->document_number,
                'email' => $guardian->email,
                'is_primary' => (bool) $pivot->is_primary,
                'is_financial_responsible' => (bool) $pivot->is_financial_responsible,
                'emergency_priority' => $pivot->emergency_priority !== null ? (int) $pivot->emergency_priority : null,
                'observations' => $pivot->observations,
            ];
        })->values()->all();
    }

    public function edit(Request $request, Student $student): Response
    {
        $this->authorize('update', $student);

        return Inertia::render('Intranet/Students/Edit', [
            'student' => $student,
            'catalog' => $this->formCatalog(),
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $this->studentService->updateStudent($student, $request->validated());

        return redirect()
            ->route('intranet.students.show', $student)
            ->with('success', 'Estudiante actualizado correctamente.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formCatalog(): array
    {
        return [
            'document_types' => DocumentType::options(),
            'genders' => Gender::options(),
            'educational_levels' => EducationalLevel::options(),
            'statuses' => StudentStatus::options(),
            'grades_by_level' => StudentGradeCatalog::gradesByLevelMap(),
        ];
    }
}
