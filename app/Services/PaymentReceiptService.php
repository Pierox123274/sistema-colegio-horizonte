<?php

namespace App\Services;

use App\Models\Payment;
use Carbon\CarbonInterface;

class PaymentReceiptService
{
    /**
     * @return array<string, mixed>
     */
    public function buildReceiptData(Payment $payment): array
    {
        $payment->loadMissing([
            'student',
            'guardian',
            'paymentConcept',
            'createdByUser:id,name',
        ]);

        $paidAt = $payment->paid_at;
        if (! $paidAt instanceof CarbonInterface) {
            $paidAt = now();
        }

        $institution = [
            'name' => (string) config('institution.name', 'I.E.P. Horizonte'),
            'identifier' => (string) config('institution.identifier', 'RUC DEMO 20123456789'),
            'address' => (string) config('institution.address', 'Av. Demo 123, Lima'),
            'message' => (string) config('institution.receipt_message', 'Gracias por su pago. Conserve este comprobante.'),
            'show_qr_demo' => (bool) config('institution.show_qr_demo', true),
        ];

        $studentName = $payment->student?->first_name.' '.$payment->student?->last_name;
        $studentName = trim($studentName) !== '' ? trim($studentName) : 'No definido';

        $guardianName = null;
        if ($payment->guardian !== null) {
            $guardianName = trim($payment->guardian->first_name.' '.$payment->guardian->last_name);
        }

        $concept = $payment->paymentConcept !== null
            ? trim($payment->paymentConcept->code.' - '.$payment->paymentConcept->name)
            : 'No definido';

        $registeredBy = $payment->createdByUser?->name;
        if ($registeredBy === null || trim($registeredBy) === '') {
            $registeredBy = 'Usuario no identificado';
        }

        return [
            'payment' => $payment,
            'receipt_number' => $this->receiptNumberFromPayment($payment),
            'generated_at' => now(),
            'paid_at' => $paidAt,
            'paid_at_human' => $paidAt->format('d/m/Y H:i:s'),
            'student_name' => $studentName,
            'guardian_name' => $guardianName,
            'concept' => $concept,
            'method' => (string) $payment->payment_method?->value,
            'amount' => number_format((float) $payment->amount, 2, '.', ''),
            'registered_by' => $registeredBy,
            'institution' => $institution,
            'qr_demo_payload' => 'REC|'.$this->receiptNumberFromPayment($payment).'|'.$payment->payment_code.'|'.$paidAt->format('YmdHis'),
        ];
    }

    private function receiptNumberFromPayment(Payment $payment): string
    {
        $date = $payment->paid_at?->format('Ymd') ?? now()->format('Ymd');

        return 'REC-'.$date.'-'.str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT);
    }
}
