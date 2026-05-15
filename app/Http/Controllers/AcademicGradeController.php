<?php

namespace App\Http\Controllers;

use App\Http\Requests\Intranet\StoreGradeBatchRequest;
use App\Models\Evaluation;
use App\Models\GradeRecord;
use App\Models\Student;
use App\Services\AcademicGradeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AcademicGradeController extends Controller
{
    public function __construct(
        private readonly AcademicGradeService $academicGradeService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', GradeRecord::class);

        $evaluation = null;
        if ($request->filled('evaluation_id')) {
            $evaluation = Evaluation::query()->with(['subject:id,name', 'section:id,name'])->find((int) $request->query('evaluation_id'));
        }

        return Inertia::render('Intranet/Academic/Grades/RecordsIndex', [
            'grades' => $this->academicGradeService->paginate($request),
            'filters' => [
                'academic_year_id' => (string) $request->query('academic_year_id', ''),
                'educational_level_id' => (string) $request->query('educational_level_id', ''),
                'grade_id' => (string) $request->query('grade_id', ''),
                'section_id' => (string) $request->query('section_id', ''),
                'subject_id' => (string) $request->query('subject_id', ''),
                'period' => (string) $request->query('period', ''),
                'evaluation_id' => (string) $request->query('evaluation_id', ''),
                'student_id' => (string) $request->query('student_id', ''),
            ],
            'catalog' => $this->academicGradeService->catalog(),
            'batch' => $evaluation ? $this->academicGradeService->batchContext($evaluation) : null,
            'selected_evaluation' => $evaluation ? [
                'id' => $evaluation->id,
                'label' => $evaluation->title.' - '.$evaluation->subject?->name.' - '.$evaluation->section?->name.' ('.$evaluation->period.')',
            ] : null,
        ]);
    }

    public function store(StoreGradeBatchRequest $request): RedirectResponse
    {
        $this->authorize('create', GradeRecord::class);
        $this->academicGradeService->registerBatch($request->validated(), (int) $request->user()->id);

        return redirect()->route('intranet.academic.grades.records.index', [
            'evaluation_id' => (string) $request->input('evaluation_id'),
        ])->with('success', 'Notas registradas/actualizadas correctamente.');
    }

    public function studentHistory(Student $student): Response
    {
        $this->authorize('viewAny', GradeRecord::class);

        $rows = GradeRecord::query()
            ->with(['evaluation.subject', 'evaluation.section'])
            ->where('student_id', $student->id)
            ->get();

        return Inertia::render('Intranet/Academic/Grades/StudentHistory', [
            'student' => $student->only(['id', 'code', 'first_name', 'last_name', 'document_number']),
            'history' => $this->academicGradeService->studentHistory($student),
            'metrics' => $this->academicGradeService->metrics($rows),
        ]);
    }

    public function historyIndex(Request $request): Response
    {
        $this->authorize('viewAny', GradeRecord::class);

        return Inertia::render('Intranet/Academic/Grades/HistoryIndex', [
            'filters' => [
                'student_id' => (string) $request->query('student_id', ''),
            ],
            'catalog' => $this->academicGradeService->catalog(),
            'recent_records' => GradeRecord::query()
                ->with([
                    'student:id,code,first_name,last_name',
                    'evaluation:id,title,period,subject_id',
                    'evaluation.subject:id,name',
                ])
                ->orderByDesc('id')
                ->limit(40)
                ->get(),
        ]);
    }

    public function reportsIndex(Request $request): Response
    {
        $this->authorize('viewAny', GradeRecord::class);
        $filters = $this->extractFilters($request);
        $rows = $this->academicGradeService->reportQuery($filters)->get();

        return Inertia::render('Intranet/Academic/Grades/ReportsIndex', [
            'filters' => $filters,
            'catalog' => $this->academicGradeService->catalog(),
            'records' => $this->academicGradeService->paginate($request),
            'metrics' => $this->academicGradeService->metrics($rows),
        ]);
    }

    public function exportPdf(Request $request): SymfonyResponse
    {
        $this->authorize('viewAny', GradeRecord::class);
        $filters = $this->extractFilters($request);
        $rows = $this->academicGradeService->reportQuery($filters)->get();
        $metrics = $this->academicGradeService->metrics($rows);

        $pdf = Pdf::loadView('intranet.academic.grades-report-pdf', [
            'rows' => $rows,
            'filters' => $filters,
            'metrics' => $metrics,
        ]);

        return $pdf->download('reporte-notas-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', GradeRecord::class);
        $filters = $this->extractFilters($request);
        $rows = $this->academicGradeService->reportQuery($filters)->get();
        $metrics = $this->academicGradeService->metrics($rows);

        return response()->streamDownload(function () use ($rows, $metrics): void {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Fecha', 'Estudiante', 'Código', 'Curso', 'Evaluación', 'Periodo', 'Sección', 'Nota', 'Registrado por'], ';');

            foreach ($rows as $row) {
                fputcsv($out, [
                    optional($row->evaluation?->evaluated_at)->format('Y-m-d'),
                    trim(($row->student?->first_name ?? '').' '.($row->student?->last_name ?? '')),
                    $row->student?->code ?? '',
                    $row->evaluation?->subject?->name ?? '',
                    $row->evaluation?->title ?? '',
                    $row->evaluation?->period ?? '',
                    $row->evaluation?->section?->name ?? '',
                    (string) $row->score,
                    $row->recordedBy?->name ?? '',
                ], ';');
            }

            fputcsv($out, [], ';');
            fputcsv($out, ['Total registros', $metrics['total_records']], ';');
            fputcsv($out, ['Promedio del curso', $metrics['course_average']], ';');
            fputcsv($out, ['Promedio general', $metrics['general_average']], ';');
            fclose($out);
        }, 'notas_'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<string,string>
     */
    private function extractFilters(Request $request): array
    {
        return [
            'academic_year_id' => (string) $request->query('academic_year_id', ''),
            'educational_level_id' => (string) $request->query('educational_level_id', ''),
            'grade_id' => (string) $request->query('grade_id', ''),
            'section_id' => (string) $request->query('section_id', ''),
            'subject_id' => (string) $request->query('subject_id', ''),
            'period' => (string) $request->query('period', ''),
            'evaluation_id' => (string) $request->query('evaluation_id', ''),
            'student_id' => (string) $request->query('student_id', ''),
        ];
    }
}
