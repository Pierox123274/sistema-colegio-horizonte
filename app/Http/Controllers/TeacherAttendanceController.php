<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Http\Requests\Intranet\StoreAttendanceBatchRequest;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\EducationalLevel;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\TeacherContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAttendanceController extends Controller
{
    private const EMPTY_RESULT_SQL = '1 = 0';

    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly TeacherContextService $teacherContext
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Attendance::class);

        $user = $request->user();
        $sectionScope = $this->resolveSectionScope($user);

        $studentQuery = Student::query()->orderBy('last_name')->orderBy('first_name');
        $this->applyEnrollmentScope($studentQuery, $sectionScope);

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
        $this->applySectionScope($recentQuery, $sectionScope);

        if ($sectionScope !== null && $request->filled('section_id')) {
            $sectionFilter = (int) $request->query('section_id');
            if ($sectionScope === [] || ! in_array($sectionFilter, $sectionScope, true)) {
                $recentQuery->whereRaw(self::EMPTY_RESULT_SQL);
            } else {
                $recentQuery->where('section_id', $sectionFilter);
            }
        }

        $sectionOptions = [];
        if ($sectionScope !== null && $user !== null) {
            $sectionOptions = $this->teacherContext->sectionFilterOptionsFor($user);
        }

        return Inertia::render('Teacher/Attendance/Index', [
            'filters' => [
                'student_id' => (string) $request->query('student_id', ''),
                'section_id' => (string) $request->query('section_id', ''),
            ],
            'catalog' => [
                'students' => $catalogStudents,
                'sections' => $sectionOptions,
            ],
            'recent_attendances' => $recentQuery
                ->orderByDesc('attendance_date')
                ->orderByDesc('id')
                ->limit(25)
                ->get(),
            'links' => [
                'register' => route('teacher.attendance.create', absolute: false),
                'index' => route('teacher.attendance.index', absolute: false),
                'reports' => route('teacher.reports.index', absolute: false),
            ],
            'has_teaching_assignments' => $sectionScope === null || $sectionScope !== [],
            'teacher_portal_scoped' => $sectionScope !== null,
            'empty_message' => $this->teacherContext->emptyAssignmentsMessage(),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Attendance::class);

        $user = $request->user();
        $scoped = $user !== null && $this->teacherContext->isDocentePortalScoped($user);

        return Inertia::render('Teacher/Attendance/Register', [
            'catalog' => $this->buildCatalog($user, $scoped),
            'batch' => null,
            'initial' => [
                'section_id' => (string) $request->query('section_id', ''),
            ],
        ]);
    }

    public function sectionDate(Request $request, string $date, Section $section): Response
    {
        $this->authorize('create', Attendance::class);

        $user = $request->user();
        if ($user !== null) {
            $this->teacherContext->assertTeacherCanAccessSection($user, $section->id);
        }

        $section->load('grade');
        $year = null;
        if ($request->filled('academic_year_id')) {
            $year = AcademicYear::query()->find((int) $request->query('academic_year_id'));
        }

        $batch = $this->attendanceService->batchContext($date, $section, $year);

        $scoped = $user !== null && $this->teacherContext->isDocentePortalScoped($user);

        return Inertia::render('Teacher/Attendance/Register', [
            'catalog' => $this->buildCatalog($user, $scoped),
            'batch' => $batch,
        ]);
    }

    public function store(StoreAttendanceBatchRequest $request): RedirectResponse
    {
        $user = $request->user();
        if ($user !== null) {
            $this->teacherContext->assertTeacherCanAccessSection(
                $user,
                (int) $request->input('section_id')
            );
        }

        $this->attendanceService->registerBatch($request->validated(), (int) $request->user()->id);

        return redirect()
            ->route('teacher.attendance.index')
            ->with('success', 'Asistencia registrada correctamente.');
    }

    /**
     * @return list<int>|null
     */
    private function resolveSectionScope(?User $user): ?array
    {
        if ($user === null || ! $this->teacherContext->isDocentePortalScoped($user)) {
            return null;
        }

        return $this->teacherContext->activeSectionIdsFor($user);
    }

    /**
     * @param  list<int>|null  $sectionScope
     */
    private function applySectionScope($query, ?array $sectionScope): void
    {
        if ($sectionScope !== null && $sectionScope !== []) {
            $query->whereIn('section_id', $sectionScope);
        } elseif ($sectionScope !== null && $sectionScope === []) {
            $query->whereRaw(self::EMPTY_RESULT_SQL);
        }
    }

    /**
     * @param  list<int>|null  $sectionScope
     */
    private function applyEnrollmentScope($query, ?array $sectionScope): void
    {
        if ($sectionScope === null) {
            return;
        }

        if ($sectionScope === []) {
            $query->whereRaw(self::EMPTY_RESULT_SQL);

            return;
        }

        $query->whereHas('enrollments', function ($q) use ($sectionScope): void {
            $year = AcademicYear::query()->where('is_active', true)->first();
            if ($year !== null) {
                $q->where('academic_year_id', $year->id)
                    ->where('status', EnrollmentStatus::Matriculado->value)
                    ->whereIn('section_id', $sectionScope);
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCatalog(?User $user, bool $scoped): array
    {
        if ($scoped && $user !== null) {
            $catalog = $this->teacherContext->attendanceCatalogFor($user);

            return [
                'statuses' => AttendanceStatus::options(),
                'academic_years' => $catalog['academic_years'],
                'levels' => $catalog['levels'],
            ];
        }

        $academicYears = AcademicYear::query()->orderByDesc('year')->get(['id', 'name', 'year', 'is_active']);
        $levels = EducationalLevel::query()
            ->with(['grades.sections'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return [
            'statuses' => AttendanceStatus::options(),
            'academic_years' => $academicYears->map(fn (AcademicYear $y): array => [
                'value' => (string) $y->id,
                'label' => $y->name.' ('.$y->year.')'.($y->is_active ? ' — Activo' : ''),
                'is_active' => $y->is_active,
            ])->values()->all(),
            'levels' => $levels->map(fn ($level): array => [
                'id' => $level->id,
                'name' => $level->name,
                'grades' => $level->grades->map(fn ($grade): array => [
                    'id' => $grade->id,
                    'name' => $grade->name,
                    'sections' => $grade->sections->map(fn (Section $section): array => [
                        'id' => $section->id,
                        'name' => $section->name,
                    ])->values()->all(),
                ])->values()->all(),
            ])->values()->all(),
        ];
    }
}
