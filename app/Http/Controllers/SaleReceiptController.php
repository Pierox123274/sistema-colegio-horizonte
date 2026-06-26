<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\SaleReceiptService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SaleReceiptController extends Controller
{
    public function __construct(
        private readonly SaleReceiptService $saleReceiptService
    ) {}

    public function show(Sale $sale): View
    {
        return view('intranet.sales.receipt', [
            'receipt' => $this->authorizedReceipt($sale),
        ]);
    }

    public function pdf(Sale $sale): SymfonyResponse
    {
        $receipt = $this->authorizedReceipt($sale);
        $pdf = Pdf::loadView('intranet.sales.receipt-pdf', ['receipt' => $receipt]);

        return $pdf->download($receipt['receipt_number'].'.pdf');
    }

    public function ticket(Sale $sale): View
    {
        return view('intranet.sales.receipt-ticket', [
            'receipt' => $this->authorizedReceipt($sale),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function authorizedReceipt(Sale $sale): array
    {
        $this->authorize('view', $sale);

        return $this->saleReceiptService->buildReceiptData($sale);
    }
}
