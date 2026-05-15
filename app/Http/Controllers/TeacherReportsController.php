<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\GradeRecord;
use App\Services\TeacherContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherReportsController extends Controller
{
    public function __construct(
        private readonly TeacherContextService $teacherContext
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Attendance::class);
        $this->authorize('viewAny', GradeRecord::class);

        $user = $request->user();
        $sectionIds = [];
        if ($user !== null && $this->teacherContext->isDocentePortalScoped($user)) {
            $sectionIds = $this->teacherContext->activeSectionIdsFor($user);
        }

        $query = [];
        if ($sectionIds !== []) {
            $query['section_id'] = (string) $sectionIds[0];
        }

        $scoped = $user !== null && $this->teacherContext->isDocentePortalScoped($user);

        return Inertia::render('Teacher/Reports/Index', [
            'links' => [
                'attendance_pdf' => route('teacher.reports.attendance.pdf', $query, false),
                'attendance_excel' => route('teacher.reports.attendance.excel', $query, false),
                'grades_pdf' => route('teacher.reports.grades.pdf', $query, false),
                'grades_excel' => route('teacher.reports.grades.excel', $query, false),
            ],
            'has_teaching_assignments' => ! $scoped || $sectionIds !== [],
            'teacher_portal_scoped' => $scoped,
        ]);
    }

    public function attendancePdf(Request $request)
    {
        return app(AttendanceController::class)->exportPdf($request);
    }

    public function attendanceExcel(Request $request)
    {
        return app(AttendanceController::class)->exportExcel($request);
    }

    public function gradesPdf(Request $request)
    {
        return app(AcademicGradeController::class)->exportPdf($request);
    }

    public function gradesExcel(Request $request)
    {
        return app(AcademicGradeController::class)->exportExcel($request);
    }
}
