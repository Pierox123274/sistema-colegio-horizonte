<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Http\Requests\Intranet\StoreAttendanceBatchRequest;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\EducationalLevel;
use App\Models\Section;
use App\Models\Student;
use App\Services\AttendanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Attendance::class);

        return Inertia::render('Intranet/Attendance/Index', [
            'filters' => [
                'student_id' => (string) $request->query('student_id', ''),
            ],
            'catalog' => [
                'students' => Student::query()
                    ->orderBy('last_name')
                    ->orderBy('first_name')
                    ->limit(500)
                    ->get(['id', 'first_name', 'last_name', 'code'])
                    ->map(fn (Student $s): array => [
                        'value' => (string) $s->id,
                        'label' => trim($s->last_name.', '.$s->first_name.' ('.$s->code.')'),
                    ])
                    ->values()
                    ->all(),
            ],
            'recent_attendances' => Attendance::query()
                ->with(['student:id,code,first_name,last_name', 'section:id,name'])
                ->orderByDesc('attendance_date')
                ->orderByDesc('id')
                ->limit(30)
                ->get(),
        ]);
    }

    public function reports(Request $request): Response
    {
        $this->authorize('viewAny', Attendance::class);

        $filters = $this->extractFilters($request);
        $rows = $this->attendanceService->reportQuery($filters)->get();

        return Inertia::render('Intranet/Attendance/Reports', [
            'attendances' => $this->attendanceService->paginateForIndex($request),
            'filters' => $filters + ['search' => (string) $request->query('search', '')],
            'catalog' => [
                'statuses' => AttendanceStatus::options(),
                'sections' => Section::query()->orderBy('name')->get(['id', 'name'])
                    ->map(fn (Section $s): array => ['value' => (string) $s->id, 'label' => $s->name])->values()->all(),
                'students' => Student::query()->orderBy('last_name')->limit(200)->get(['id', 'first_name', 'last_name', 'code'])
                    ->map(fn (Student $s): array => ['value' => (string) $s->id, 'label' => trim($s->last_name.', '.$s->first_name.' ('.$s->code.')')])->values()->all(),
            ],
            'metrics' => $this->attendanceService->metrics($rows),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Attendance::class);

        return Inertia::render('Intranet/Attendance/Create', [
            'catalog' => $this->buildCatalog(),
            'batch' => null,
        ]);
    }

    public function sectionDate(Request $request, string $date, Section $section): Response
    {
        $this->authorize('create', Attendance::class);
        $section->load('grade');
        $year = null;
        if ($request->filled('academic_year_id')) {
            $year = AcademicYear::query()->find((int) $request->query('academic_year_id'));
        }

        $batch = $this->attendanceService->batchContext($date, $section, $year);

        return Inertia::render('Intranet/Attendance/Create', [
            'catalog' => $this->buildCatalog(),
            'batch' => $batch,
        ]);
    }

    public function store(StoreAttendanceBatchRequest $request): RedirectResponse
    {
        $this->attendanceService->registerBatch($request->validated(), (int) $request->user()->id);

        return redirect()
            ->route('intranet.attendance.index')
            ->with('success', 'Asistencia registrada/actualizada correctamente.');
    }

    public function studentHistory(Student $student): Response
    {
        $this->authorize('viewAny', Attendance::class);

        $rows = Attendance::query()->where('student_id', $student->id)->get();

        return Inertia::render('Intranet/Attendance/StudentHistory', [
            'student' => $student->only(['id', 'code', 'first_name', 'last_name', 'document_number']),
            'history' => $this->attendanceService->studentHistory($student),
            'metrics' => $this->attendanceService->metrics($rows),
        ]);
    }

    public function exportPdf(Request $request): SymfonyResponse
    {
        $this->authorize('viewAny', Attendance::class);
        $filters = $this->extractFilters($request);
        $rows = $this->attendanceService->reportQuery($filters)->get();
        $metrics = $this->attendanceService->metrics($rows);

        $pdf = Pdf::loadView('intranet.attendance.report-pdf', [
            'rows' => $rows,
            'metrics' => $metrics,
            'filters' => $filters,
        ]);

        return $pdf->download('reporte-asistencia-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Attendance::class);
        $filters = $this->extractFilters($request);
        $rows = $this->attendanceService->reportQuery($filters)->get();
        $metrics = $this->attendanceService->metrics($rows);

        $filename = $this->attendanceCsvFilename($filters);

        return response()->streamDownload(function () use ($rows, $metrics): void {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Fecha', 'Estudiante', 'Código', 'Nivel', 'Grado', 'Sección', 'Estado', 'Observación', 'Registrado por'], ';');
            foreach ($rows as $row) {
                fputcsv($out, [
                    optional($row->attendance_date)->format('Y-m-d'),
                    trim($row->student?->first_name.' '.$row->student?->last_name),
                    $row->student?->code ?? '',
                    $row->educationalLevel?->name ?? '',
                    $row->grade?->name ?? '',
                    $row->section?->name ?? '',
                    $row->status->value,
                    $row->observation ?? '',
                    $row->recordedBy?->name ?? '',
                ], ';');
            }
            fputcsv($out, [], ';');
            fputcsv($out, ['Total registros', $metrics['total']], ';');
            fputcsv($out, ['% asistencia', $metrics['attendance_percentage']], ';');
            fputcsv($out, ['Tardanzas', $metrics['late_count']], ';');
            fputcsv($out, ['Faltas', $metrics['absence_count']], ';');
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<string,string>
     */
    private function extractFilters(Request $request): array
    {
        return [
            'date' => (string) $request->query('date', ''),
            'date_from' => (string) $request->query('date_from', ''),
            'date_to' => (string) $request->query('date_to', ''),
            'section_id' => (string) $request->query('section_id', ''),
            'student_id' => (string) $request->query('student_id', ''),
            'status' => (string) $request->query('status', ''),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function buildCatalog(): array
    {
        $academicYears = AcademicYear::query()->orderByDesc('year')->get(['id', 'name', 'year', 'is_active']);
        $levels = EducationalLevel::query()->with(['grades.sections'])->where('is_active', true)->orderBy('name')->get();

        return [
            'statuses' => AttendanceStatus::options(),
            'academic_years' => $academicYears->map(fn (AcademicYear $y): array => [
                'value' => (string) $y->id,
                'label' => $y->name.' ('.$y->year.')'.($y->is_active ? ' - Activo' : ''),
                'is_active' => $y->is_active,
            ])->values()->all(),
            'levels' => $levels->map(fn (EducationalLevel $level): array => [
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

    /**
     * @param  array<string,string>  $filters
     */
    private function attendanceCsvFilename(array $filters): string
    {
        if (! empty($filters['date'])) {
            return 'asistencia_'.$filters['date'].'.csv';
        }

        if (! empty($filters['date_from']) || ! empty($filters['date_to'])) {
            $from = $filters['date_from'] ?: 'inicio';
            $to = $filters['date_to'] ?: 'hoy';

            return 'asistencia_'.$from.'_a_'.$to.'.csv';
        }

        return 'asistencia_'.now()->format('Y-m-d').'.csv';
    }
}
