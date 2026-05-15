<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\GradeRecord;
use Inertia\Inertia;
use Inertia\Response;

class TeacherReportsController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Attendance::class);
        $this->authorize('viewAny', GradeRecord::class);

        return Inertia::render('Teacher/Reports/Index', [
            'links' => [
                'attendance_reports' => route('intranet.attendance.reports.index', absolute: false),
                'attendance_pdf' => route('intranet.attendance.reports.export.pdf', absolute: false),
                'attendance_excel' => route('intranet.attendance.reports.export.excel', absolute: false),
                'grades_reports' => route('intranet.academic.grades.reports.index', absolute: false),
                'grades_pdf' => route('intranet.academic.grades.reports.export.pdf', absolute: false),
                'grades_excel' => route('intranet.academic.grades.reports.export.excel', absolute: false),
            ],
        ]);
    }
}
