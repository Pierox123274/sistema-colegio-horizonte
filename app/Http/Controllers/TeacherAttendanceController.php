<?php

namespace App\Http\Controllers;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Student;
use App\Services\TeacherContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAttendanceController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Attendance::class);

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

        $studentQuery = Student::query()->orderBy('last_name')->orderBy('first_name');
        if ($sectionScope !== null && $sectionScope !== []) {
            $studentQuery->whereHas('enrollments', function ($q) use ($sectionScope): void {
                $year = AcademicYear::query()->where('is_active', true)->first();
                if ($year !== null) {
                    $q->where('academic_year_id', $year->id)
                        ->where('status', EnrollmentStatus::Matriculado->value)
                        ->whereIn('section_id', $sectionScope);
                }
            });
        } elseif ($sectionScope !== null && $sectionScope === []) {
            $studentQuery->whereRaw('1 = 0');
        }

        $catalogStudents = $studentQuery
            ->limit(500)
            ->get(['id', 'first_name', 'last_name', 'code'])
            ->map(fn (Student $s): array => [
                'value' => (string) $s->id,
                'label' => trim($s->last_name.', '.$s->first_name.' ('.$s->code.')'),
            ])
            ->values()
            ->all();

        $recentQuery = Attendance::query()->with(['student:id,code,first_name,last_name', 'section:id,name']);
        if ($sectionScope !== null && $sectionScope !== []) {
            $recentQuery->whereIn('section_id', $sectionScope);
        } elseif ($sectionScope !== null && $sectionScope === []) {
            $recentQuery->whereRaw('1 = 0');
        }

        $firstSectionId = ($sectionScope !== null && $sectionScope !== []) ? $sectionScope[0] : null;
        $year = AcademicYear::query()->where('is_active', true)->first();
        $today = now()->toDateString();

        $links = [
            'register' => route('intranet.attendance.create', absolute: false),
            'history' => route('intranet.attendance.index', absolute: false),
            'reports' => route('intranet.attendance.reports.index', absolute: false),
        ];

        if ($firstSectionId !== null) {
            $links['register'] = route('intranet.attendance.section-date', [
                'date' => $today,
                'section' => $firstSectionId,
            ], false).($year ? '?academic_year_id='.$year->id : '');
            $links['history'] = route('intranet.attendance.reports.index', [
                'section_id' => $firstSectionId,
            ], false);
            $links['reports'] = route('intranet.attendance.reports.index', [
                'section_id' => $firstSectionId,
            ], false);
        }

        return Inertia::render('Teacher/Attendance/Index', [
            'filters' => [
                'student_id' => (string) $request->query('student_id', ''),
            ],
            'catalog' => [
                'students' => $catalogStudents,
            ],
            'recent_attendances' => $recentQuery
                ->orderByDesc('attendance_date')
                ->orderByDesc('id')
                ->limit(25)
                ->get(),
            'links' => $links,
            'has_teaching_assignments' => $sectionScope === null || $sectionScope !== [],
            'teacher_portal_scoped' => $sectionScope !== null,
        ]);
    }
}
