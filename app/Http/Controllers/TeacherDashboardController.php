<?php

namespace App\Http\Controllers;

use App\Enums\EnrollmentStatus;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\GradeRecord;
use App\Models\Subject;
use App\Services\LMSDashboardService;
use App\Services\TeacherContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherDashboardController extends Controller
{
    public function __construct(
        private readonly TeacherContextService $teacherContext
    ) {}

    public function index(Request $request, LMSDashboardService $lmsDashboard): Response
    {
        $user = $request->user();
        $activeYear = AcademicYear::query()->where('is_active', true)->first();

        $sectionIds = [];
        $assignmentsPayload = [];
        $stats = [
            'enrolled_students' => 0,
            'attendance_records_week' => 0,
            'subjects_count' => 0,
            'evaluations_count' => 0,
            'grade_records_count' => 0,
        ];

        $docenteSolo = $user !== null && $this->teacherContext->isDocentePortalScoped($user);

        $assignmentsOverview = ['academic_year' => null, 'sections' => [], 'course_assignments' => []];

        if ($docenteSolo && $user !== null) {
            $sectionIds = $this->teacherContext->activeSectionIdsFor($user);
            $stats = $this->teacherContext->dashboardStats($user, $sectionIds);
            $assignmentsPayload = $this->teacherContext->assignmentsTableFor($user);
            $assignmentsOverview = $this->teacherContext->assignmentsOverviewFor($user);
        } else {
            $enrolledCount = 0;
            if ($activeYear !== null) {
                $enrolledCount = Enrollment::query()
                    ->where('academic_year_id', $activeYear->id)
                    ->where('status', EnrollmentStatus::Matriculado->value)
                    ->count();
            }

            $stats = [
                'enrolled_students' => $enrolledCount,
                'attendance_records_week' => Attendance::query()
                    ->where('attendance_date', '>=', now()->subDays(7)->toDateString())
                    ->count(),
                'subjects_count' => Subject::query()->count(),
                'evaluations_count' => Evaluation::query()->count(),
                'grade_records_count' => GradeRecord::query()->count(),
            ];
        }

        $lms = $user !== null ? $lmsDashboard->teacherSummary($user) : [];

        return Inertia::render('Teacher/Dashboard', [
            'academic_year' => $activeYear?->only(['id', 'name', 'year', 'is_active']),
            'lms' => $lms,
            'stats' => $stats,
            'assignments' => $assignmentsPayload,
            'assignments_overview' => $assignmentsOverview,
            'has_teaching_assignments' => $assignmentsPayload !== [],
            'teacher_portal_scoped' => $docenteSolo,
            'empty_message' => $this->teacherContext->emptyAssignmentsMessage(),
            'links' => [
                'assignments' => route('teacher.assignments.index', absolute: false),
                'attendance' => route('teacher.attendance.index', absolute: false),
                'attendance_register' => route('teacher.attendance.create', absolute: false),
                'grades' => route('teacher.grades.records', absolute: false),
                'grades_summary' => route('teacher.grades.index', absolute: false),
                'students' => route('teacher.students.index', absolute: false),
                'reports' => route('teacher.reports.index', absolute: false),
                'classrooms' => route('teacher.classrooms.index', absolute: false),
                'calendar' => route('teacher.calendar.index', absolute: false),
            ],
        ]);
    }
}
