<?php

namespace App\Http\Controllers;

use App\Enums\EnrollmentStatus;
use App\Http\Requests\Intranet\StoreEnrollmentRequest;
use App\Http\Requests\Intranet\UpdateEnrollmentRequest;
use App\Models\AcademicYear;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Student;
use App\Services\EnrollmentService;
use App\Support\EnrollmentFormCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EnrollmentController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollmentService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Enrollment::class);

        return Inertia::render('Intranet/Enrollments/Index', [
            'enrollments' => $this->enrollmentService->paginateForIndex($request),
            'stats' => $this->enrollmentService->indexStats(),
            'filters' => [
                'search' => $request->query('search', ''),
                'academic_year_id' => $request->query('academic_year_id', ''),
                'educational_level_id' => $request->query('educational_level_id', ''),
                'grade_id' => $request->query('grade_id', ''),
                'status' => $request->query('status', ''),
            ],
            'catalog' => [
                'academic_years' => AcademicYear::query()
                    ->orderByDesc('year')
                    ->get(['id', 'name', 'year'])
                    ->map(fn ($y) => [
                        'value' => (string) $y->id,
                        'label' => $y->name.' ('.$y->year.')',
                    ])
                    ->values()
                    ->all(),
                'levels' => EducationalLevel::query()
                    ->where('is_active', true)
                    ->orderBy('code')
                    ->get(['id', 'code', 'name'])
                    ->map(fn ($l) => [
                        'value' => (string) $l->id,
                        'label' => $l->code.' — '.$l->name,
                    ])
                    ->values()
                    ->all(),
                'grades' => Grade::query()
                    ->where('is_active', true)
                    ->orderBy('educational_level_id')
                    ->orderBy('order')
                    ->get(['id', 'code', 'name', 'educational_level_id'])
                    ->map(fn ($g) => [
                        'value' => (string) $g->id,
                        'label' => $g->code.' — '.$g->name,
                        'educational_level_id' => $g->educational_level_id,
                    ])
                    ->values()
                    ->all(),
                'statuses' => EnrollmentStatus::options(),
            ],
            'permissions' => [
                'manage' => $request->user()?->can('create', Enrollment::class) ?? false,
            ],
        ]);
    }

    public function searchStudents(Request $request): JsonResponse
    {
        $this->authorize('create', Enrollment::class);

        $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $q = trim((string) $request->query('q', ''));

        return response()->json([
            'students' => $this->enrollmentService->searchStudentsForEnrollment($q),
        ]);
    }

    public function studentPreview(Student $student): JsonResponse
    {
        $this->authorize('create', Enrollment::class);

        return response()->json([
            'preview' => $this->enrollmentService->studentPreviewForEnrollment($student),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Enrollment::class);

        return Inertia::render('Intranet/Enrollments/Create', [
            'catalog' => EnrollmentFormCatalog::build(),
        ]);
    }

    public function store(StoreEnrollmentRequest $request): RedirectResponse
    {
        $enrollment = $this->enrollmentService->create($request->validated());

        return redirect()
            ->route('intranet.enrollments.show', $enrollment)
            ->with('success', 'Matrícula registrada correctamente.');
    }

    public function show(Request $request, Enrollment $enrollment): Response
    {
        $this->authorize('view', $enrollment);

        $enrollment->load([
            'student',
            'guardian',
            'academicYear',
            'educationalLevel',
            'grade',
            'section',
            'classroom',
        ]);

        return Inertia::render('Intranet/Enrollments/Show', [
            'enrollment' => $enrollment,
            'permissions' => [
                'manage' => $request->user()?->can('update', $enrollment) ?? false,
            ],
        ]);
    }

    public function edit(Request $request, Enrollment $enrollment): Response
    {
        $this->authorize('update', $enrollment);

        $enrollment->load([
            'student:id,code,first_name,last_name,document_number,document_type',
            'student.guardians:id,first_name,last_name',
            'guardian:id,first_name,last_name',
        ]);

        return Inertia::render('Intranet/Enrollments/Edit', [
            'enrollment' => $enrollment,
            'catalog' => EnrollmentFormCatalog::build(),
            'student_preview' => $enrollment->student !== null
                ? $this->enrollmentService->studentPreviewForEnrollment($enrollment->student)
                : null,
        ]);
    }

    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment): RedirectResponse
    {
        $this->enrollmentService->update($enrollment, $request->validated());

        return redirect()
            ->route('intranet.enrollments.show', $enrollment)
            ->with('success', 'Matrícula actualizada.');
    }
}
