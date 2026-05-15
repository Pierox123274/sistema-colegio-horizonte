<?php

namespace App\Http\Controllers;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\GradeRecord;
use App\Models\Subject;
use App\Services\TeacherContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherDashboardController extends Controller
{
    public function __construct(
        private readonly TeacherContextService $teacherContext
    ) {}

    public function index(Request $request): Response
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

        $docenteSolo = $user !== null
            && $user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasAnyRole([
                IntranetRole::Administrador->value,
                IntranetRole::Secretaria->value,
            ]);

        if ($docenteSolo && $user !== null) {
            $sectionIds = $this->teacherContext->activeSectionIdsFor($user);
            $stats = $this->teacherContext->dashboardStats($user, $sectionIds);
            $assignmentsPayload = $this->teacherContext->activeAssignmentsFor($user)->map(function ($a): array {
                return [
                    'id' => $a->id,
                    'section' => $a->section?->name,
                    'grade' => $a->section?->grade?->name,
                    'level' => $a->section?->grade?->educationalLevel?->name,
                    'subject' => $a->subject?->name,
                    'is_tutor' => $a->is_tutor,
                ];
            })->values()->all();
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

        $firstSectionId = $sectionIds[0] ?? null;
        $yearId = $activeYear?->id;
        $today = now()->toDateString();

        $quickLinks = [
            'attendance' => route('intranet.attendance.index', absolute: false),
            'attendance_register' => route('intranet.attendance.create', absolute: false),
            'grades' => route('intranet.academic.grades.records.index', absolute: false),
            'grades_reports' => route('intranet.academic.grades.reports.index', absolute: false),
        ];

        if ($firstSectionId !== null) {
            $quickLinks['attendance_register'] = route('intranet.attendance.section-date', [
                'date' => $today,
                'section' => $firstSectionId,
            ], false).($yearId ? '?academic_year_id='.$yearId : '');
            $quickLinks['attendance'] = route('intranet.attendance.reports.index', [
                'section_id' => $firstSectionId,
            ], false);
            $quickLinks['grades'] = route('intranet.academic.grades.records.index', [
                'section_id' => $firstSectionId,
            ], false);
            $quickLinks['grades_reports'] = route('intranet.academic.grades.reports.index', [
                'section_id' => $firstSectionId,
            ], false);
        }

        return Inertia::render('Teacher/Dashboard', [
            'academic_year' => $activeYear?->only(['id', 'name', 'year', 'is_active']),
            'stats' => $stats,
            'assignments' => $assignmentsPayload,
            'has_teaching_assignments' => $assignmentsPayload !== [],
            'teacher_portal_scoped' => $docenteSolo,
            'links' => $quickLinks,
        ]);
    }
}
