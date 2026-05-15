<?php

namespace App\Http\Controllers;

use App\Services\TeacherContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAssignmentsController extends Controller
{
    public function __construct(
        private readonly TeacherContextService $teacherContext
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $scoped = $user !== null && $this->teacherContext->isDocentePortalScoped($user);

        $overview = $scoped && $user !== null
            ? $this->teacherContext->assignmentsOverviewFor($user)
            : ['academic_year' => null, 'sections' => [], 'course_assignments' => []];

        $hasAssignments = $overview['sections'] !== [] || $overview['course_assignments'] !== [];

        $tab = (string) $request->query('tab', 'resumen');
        $allowedTabs = ['resumen', 'secciones', 'cursos', 'estudiantes'];
        if (! in_array($tab, $allowedTabs, true)) {
            $tab = 'resumen';
        }

        return Inertia::render('Teacher/Assignments/Index', [
            'overview' => $overview,
            'assignments' => $scoped && $user !== null
                ? $this->teacherContext->assignmentsTableFor($user)
                : [],
            'active_tab' => $tab,
            'has_teaching_assignments' => $hasAssignments,
            'teacher_portal_scoped' => $scoped,
            'empty_message' => $this->teacherContext->emptyAssignmentsMessage(),
        ]);
    }
}
