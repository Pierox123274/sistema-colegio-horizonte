<?php

namespace App\Http\Controllers;

use App\Enums\EducationalLevel;
use App\Enums\IntranetRole;
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
        private readonly StudentService $studentService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Student::class);

        $user = $request->user();
        $sectionScope = null;
        $docenteSolo = false;
        if ($user !== null
            && $user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasAnyRole([
                IntranetRole::Administrador->value,
                IntranetRole::Secretaria->value,
            ])
        ) {
            $docenteSolo = true;
            $sectionScope = app(TeacherContextService::class)->activeSectionIdsFor($user);
        }

        return Inertia::render('Teacher/Students/Index', [
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
            'links' => [
                'intranet_index' => route('intranet.students.index', absolute: false),
            ],
            'has_teaching_assignments' => ! $docenteSolo || $sectionScope !== [],
            'teacher_portal_scoped' => $docenteSolo,
        ]);
    }
}
