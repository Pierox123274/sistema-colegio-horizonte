<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentReceiptService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class PaymentReceiptController extends Controller
{
    public function __construct(
        private readonly PaymentReceiptService $paymentReceiptService
    ) {}

    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        return view('intranet.payments.receipt', [
            'receipt' => $this->paymentReceiptService->buildReceiptData($payment),
        ]);
    }

    public function pdf(Payment $payment): Response
    {
        $this->authorize('view', $payment);

        $receipt = $this->paymentReceiptService->buildReceiptData($payment);
        $filename = 'comprobante-'.$receipt['receipt_number'].'.pdf';

        return Pdf::loadView('intranet.payments.receipt-pdf', [
            'receipt' => $receipt,
        ])->download($filename);
    }

    public function ticket(Payment $payment): View
    {
        $this->authorize('view', $payment);

        return view('intranet.payments.receipt-ticket', [
            'receipt' => $this->paymentReceiptService->buildReceiptData($payment),
        ]);
    }
}
