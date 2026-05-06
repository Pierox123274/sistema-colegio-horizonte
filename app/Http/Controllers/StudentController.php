<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Enums\EducationalLevel;
use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Http\Requests\Intranet\StoreStudentRequest;
use App\Http\Requests\Intranet\UpdateStudentRequest;
use App\Models\Student;
use App\Services\StudentService;
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

        return Inertia::render('Intranet/Students/Index', [
            'students' => $this->studentService->paginateForIndex($request),
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

        return Inertia::render('Intranet/Students/Show', [
            'student' => $student,
            'permissions' => [
                'manage' => $request->user()?->can('update', $student) ?? false,
            ],
        ]);
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
