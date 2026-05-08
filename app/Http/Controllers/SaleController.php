<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Http\Requests\Intranet\StoreSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Student;
use App\Services\SaleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Sale::class);

        return Inertia::render('Intranet/Sales/Sales/Index', [
            'sales' => $this->saleService->paginateForIndex($request),
            'filters' => [
                'search' => $request->query('search', ''),
                'status' => $request->query('status', ''),
                'date_from' => $request->query('date_from', ''),
                'date_to' => $request->query('date_to', ''),
                'day' => $request->query('day', ''),
                'payment_method' => $request->query('payment_method', ''),
                'cashier_id' => $request->query('cashier_id', ''),
            ],
            'catalog' => [
                'statuses' => [
                    ['value' => 'registrada', 'label' => 'Registrada'],
                    ['value' => 'anulada', 'label' => 'Anulada'],
                ],
                'methods' => PaymentMethod::options(),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Sale::class);

        $products = Product::query()
            ->where('is_active', true)
            ->where('current_stock', '>', 0)
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'size', 'current_stock', 'sale_price'])
            ->map(fn (Product $product): array => [
                'value' => (string) $product->id,
                'label' => $product->code.' - '.$product->name.' ('.$product->size.')',
                'stock' => (string) $product->current_stock,
                'price' => (string) $product->sale_price,
            ])
            ->values()
            ->all();

        return Inertia::render('Intranet/Sales/Sales/Create', [
            'catalog' => [
                'products' => $products,
                'methods' => PaymentMethod::options(),
            ],
        ]);
    }

    public function searchStudents(Request $request): JsonResponse
    {
        $this->authorize('create', Sale::class);
        $request->validate(['q' => ['nullable', 'string', 'max:120']]);
        $q = trim((string) $request->query('q', ''));

        return response()->json([
            'students' => $this->saleService->searchStudents($q),
        ]);
    }

    public function studentPreview(Student $student, Request $request): JsonResponse
    {
        $this->authorize('create', Sale::class);

        return response()->json([
            'preview' => $this->saleService->studentPreview($student),
        ]);
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $sale = $this->saleService->create($request->validated(), (int) $request->user()->id);

        return redirect()->route('intranet.sales.sales.show', $sale)->with('success', 'Venta registrada.');
    }

    public function show(Request $request, Sale $sale): Response
    {
        $this->authorize('view', $sale);
        $sale->load([
            'student:id,first_name,last_name,code',
            'guardian:id,first_name,last_name',
            'items.product:id,code,name,size,unit',
            'cashRegister:id,business_date,opening_balance,closing_balance,status',
            'createdByUser:id,name',
            'canceledByUser:id,name',
        ]);

        return Inertia::render('Intranet/Sales/Sales/Show', [
            'sale' => $sale,
        ]);
    }

    public function cancel(Request $request, Sale $sale): RedirectResponse
    {
        $this->authorize('cancel', $sale);
        $this->saleService->cancel($sale, (int) $request->user()->id);

        return redirect()->route('intranet.sales.sales.show', $sale)->with('success', 'Venta anulada y stock devuelto.');
    }

    public function exportPdf(Request $request): SymfonyResponse
    {
        $this->authorize('viewAny', Sale::class);
        $filters = $this->extractReportFilters($request);
        $sales = $this->saleService->reportQuery($filters)->get();
        $summary = $this->saleService->reportSummary($sales);
        $pdf = Pdf::loadView('intranet.sales.report-pdf', [
            'sales' => $sales,
            'summary' => $summary,
            'filters' => $filters,
        ]);

        return $pdf->download('reporte-ventas-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Sale::class);
        $filters = $this->extractReportFilters($request);
        $sales = $this->saleService->reportQuery($filters)->get();
        $summary = $this->saleService->reportSummary($sales);

        $filename = 'reporte-ventas-'.now()->format('Ymd-His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        $callback = function () use ($sales, $summary): void {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Fecha', 'Codigo venta', 'Estudiante/Cliente', 'Apoderado', 'Metodo de pago', 'Estado', 'Total', 'Usuario/Cajero']);
            foreach ($sales as $sale) {
                fputcsv($out, [
                    optional($sale->sold_at)?->format('Y-m-d H:i:s'),
                    $sale->sale_code,
                    $sale->student ? trim($sale->student->first_name.' '.$sale->student->last_name.' ('.$sale->student->code.')') : 'Venta al publico',
                    $sale->guardian ? trim($sale->guardian->first_name.' '.$sale->guardian->last_name) : '',
                    $sale->payment_method,
                    $sale->status,
                    number_format((float) $sale->total, 2, '.', ''),
                    $sale->createdByUser?->name ?? '',
                ]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Total ventas registradas', $summary['total_sold']]);
            fputcsv($out, ['Total ventas anuladas', $summary['total_canceled']]);
            fputcsv($out, ['Total neto', $summary['net_total']]);
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * @return array<string, string>
     */
    private function extractReportFilters(Request $request): array
    {
        return [
            'date_from' => (string) $request->query('date_from', ''),
            'date_to' => (string) $request->query('date_to', ''),
            'day' => (string) $request->query('day', ''),
            'payment_method' => (string) $request->query('payment_method', ''),
            'status' => (string) $request->query('status', ''),
            'cashier_id' => (string) $request->query('cashier_id', ''),
        ];
    }
}
