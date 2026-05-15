<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AnalyticsService;
use App\Support\AnalyticsDashboard;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IntranetAnalyticsReportsController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analytics
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewExecutive', AnalyticsDashboard::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $filters = $this->analytics->normalizeFilters($request->all());

        return Inertia::render('Intranet/Reports/Analytics/Index', [
            'filters' => $filters,
            'catalog' => $this->analytics->filterCatalog(),
            'report_types' => $this->buildReportTypeCards($user),
        ]);
    }

    public function show(Request $request, string $type): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->assertReportTypeAllowed($user, $type);

        $filters = $this->analytics->normalizeFilters($request->all());

        return Inertia::render('Intranet/Reports/Analytics/Show', [
            'filters' => $filters,
            'catalog' => $this->analytics->filterCatalog(),
            'report' => $this->analytics->reportPayload($user, $type, $filters),
        ]);
    }

    public function exportPdf(Request $request, string $type): HttpResponse
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->authorize('export', AnalyticsDashboard::class);
        $this->assertReportTypeAllowed($user, $type);

        $filters = $this->analytics->normalizeFilters($request->all());
        $report = $this->analytics->reportPayload($user, $type, $filters);

        $pdf = Pdf::loadView('intranet.analytics.report-pdf', [
            'report' => $report,
            'filters' => $filters,
            'generated_at' => now()->translatedFormat('d/m/Y H:i'),
        ]);

        return $pdf->download('reporte-'.$type.'-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportCsv(Request $request, string $type): StreamedResponse
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->authorize('export', AnalyticsDashboard::class);
        $this->assertReportTypeAllowed($user, $type);

        $filters = $this->analytics->normalizeFilters($request->all());
        $report = $this->analytics->reportPayload($user, $type, $filters);

        return response()->streamDownload(function () use ($report, $type): void {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Reporte analítico', $report['title'] ?? $type], ';');
            fputcsv($out, [], ';');

            if (isset($report['summary']) && is_array($report['summary'])) {
                fputcsv($out, ['Resumen'], ';');
                foreach ($report['summary'] as $key => $value) {
                    fputcsv($out, [(string) $key, is_scalar($value) ? (string) $value : json_encode($value)], ';');
                }
                fputcsv($out, [], ';');
            }

            foreach (['top_students', 'risk_students', 'most_absences', 'recent_payments', 'low_stock'] as $section) {
                if (empty($report[$section]) || ! is_array($report[$section])) {
                    continue;
                }

                fputcsv($out, [str_replace('_', ' ', ucfirst($section))], ';');
                $first = $report[$section][0] ?? [];
                if (is_array($first)) {
                    fputcsv($out, array_keys($first), ';');
                    foreach ($report[$section] as $row) {
                        fputcsv($out, array_values($row), ';');
                    }
                }
                fputcsv($out, [], ';');
            }

            fclose($out);
        }, 'reporte-'.$type.'-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return list<array{key: string, label: string, description: string}>
     */
    private function buildReportTypeCards(User $user): array
    {
        $labels = [
            'academic' => ['Reporte académico', 'Indicadores generales y rendimiento por sección.'],
            'attendance' => ['Reporte de asistencia', 'Tendencias y faltas por estudiante.'],
            'grades' => ['Reporte de notas', 'Rankings y estudiantes en riesgo.'],
            'financial' => ['Reporte financiero', 'Ingresos, pensiones y morosidad.'],
            'sales' => ['Reporte de ventas', 'Productos más vendidos e ingresos.'],
            'inventory' => ['Reporte de inventario', 'Stock bajo y rotación.'],
        ];

        return collect($this->analytics->allowedReportTypes($user))
            ->map(fn (string $key): array => [
                'key' => $key,
                'label' => $labels[$key][0] ?? $key,
                'description' => $labels[$key][1] ?? '',
            ])
            ->values()
            ->all();
    }

    private function assertReportTypeAllowed(User $user, string $type): void
    {
        if (! in_array($type, $this->analytics->allowedReportTypes($user), true)) {
            abort(403);
        }
    }
}
