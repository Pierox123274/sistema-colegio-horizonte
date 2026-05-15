<?php

namespace App\Http\Controllers;

use App\Enums\EducationalLevel;
use App\Enums\StudentStatus;
use App\Models\Student;
use App\Services\StudentService;
use App\Services\TeacherContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherStudentsController extends Controller
{
    public function __construct(
        private readonly StudentService $studentService,
        private readonly TeacherContextService $teacherContext
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Student::class);

        $user = $request->user();
        $sectionScope = null;
        $docenteSolo = $user !== null && $this->teacherContext->isDocentePortalScoped($user);
        if ($docenteSolo && $user !== null) {
            $sectionScope = $this->teacherContext->activeSectionIdsFor($user);
        }

        if ($docenteSolo && $user !== null && $request->filled('section_id')) {
            $this->teacherContext->assertTeacherCanAccessSection($user, (int) $request->query('section_id'));
        }

        $groupedStudents = [];
        if ($docenteSolo && $user !== null) {
            $groupedStudents = $this->teacherContext->studentsGroupedBySection(
                $user,
                $request->query('search') ? (string) $request->query('search') : null,
                $request->filled('section_id') ? (int) $request->query('section_id') : null,
            );
        }

        return Inertia::render('Teacher/Students/Index', [
            'students' => $this->studentService->paginateForIndex($request, 15, $sectionScope),
            'grouped_students' => $groupedStudents,
            'filters' => [
                'search' => $request->query('search', ''),
                'educational_level' => $request->query('educational_level', ''),
                'status' => $request->query('status', ''),
                'section_id' => (string) $request->query('section_id', ''),
            ],
            'catalog' => [
                'educational_levels' => EducationalLevel::options(),
                'statuses' => StudentStatus::options(),
                'sections' => $docenteSolo && $user !== null
                    ? $this->teacherContext->sectionFilterOptionsFor($user)
                    : [],
            ],
            'has_teaching_assignments' => ! $docenteSolo || $sectionScope !== [],
            'teacher_portal_scoped' => $docenteSolo,
            'empty_message' => $this->teacherContext->emptyAssignmentsMessage(),
        ]);
    }

    public function show(Request $request, Student $student): Response
    {
        $this->authorize('view', $student);

        $student->load([
            'guardians' => fn ($q) => $q->orderBy('guardians.last_name')->orderBy('guardians.first_name'),
        ]);

        return Inertia::render('Teacher/Students/Show', [
            'student' => $student,
            'guardian_links' => $student->guardians->map(function ($guardian): array {
                $pivot = $guardian->pivot;

                return [
                    'full_name' => $guardian->fullName(),
                    'relationship' => $pivot->getAttribute('relationship'),
                    'phone' => $guardian->phone,
                    'is_primary' => (bool) $pivot->is_primary,
                ];
            })->values()->all(),
        ]);
    }
}
