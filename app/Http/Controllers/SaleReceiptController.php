<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\SaleReceiptService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SaleReceiptController extends Controller
{
    public function __construct(
        private readonly SaleReceiptService $saleReceiptService
    ) {}

    public function show(Request $request, Sale $sale): View
    {
        $this->authorize('view', $sale);
        $receipt = $this->saleReceiptService->buildReceiptData($sale);

        return view('intranet.sales.receipt', ['receipt' => $receipt]);
    }

    public function pdf(Request $request, Sale $sale): SymfonyResponse
    {
        $this->authorize('view', $sale);
        $receipt = $this->saleReceiptService->buildReceiptData($sale);
        $pdf = Pdf::loadView('intranet.sales.receipt-pdf', ['receipt' => $receipt]);

        return $pdf->download($receipt['receipt_number'].'.pdf');
    }

    public function ticket(Request $request, Sale $sale): View
    {
        $this->authorize('view', $sale);
        $receipt = $this->saleReceiptService->buildReceiptData($sale);

        return view('intranet.sales.receipt-ticket', ['receipt' => $receipt]);
    }
}
