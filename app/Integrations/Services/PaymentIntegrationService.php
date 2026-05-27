<?php

namespace App\Integrations\Services;

use App\Integrations\DTO\PaymentCheckoutDTO;
use App\Models\Payment;
use Illuminate\Support\Str;

/**
 * Puente entre módulo de pagos/pensiones y pasarelas externas (sin cobro real obligatorio).
 */
final class PaymentIntegrationService
{
    public function __construct(
        private readonly IntegrationRegistry $registry,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function prepareCheckout(Payment $payment): array
    {
        $gateway = $this->registry->payments();

        $dto = $gateway->createCheckoutSession([
            'reference' => 'pay_'.$payment->id.'_'.Str::lower(Str::random(6)),
            'amount' => (float) $payment->amount,
            'currency' => 'PEN',
            'description' => 'Pago institucional #'.$payment->id,
            'metadata' => [
                'payment_id' => $payment->id,
                'student_id' => $payment->student_id,
            ],
        ]);

        return [
            'gateway' => $gateway->name(),
            'configured' => $gateway->isConfigured(),
            'checkout' => $this->dtoToArray($dto),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function dtoToArray(PaymentCheckoutDTO $dto): array
    {
        return [
            'external_reference' => $dto->externalReference,
            'amount' => $dto->amount,
            'currency' => $dto->currency,
            'description' => $dto->description,
            'checkout_url' => $dto->checkoutUrl,
            'payment_intent_id' => $dto->paymentIntentId,
            'success' => $dto->success,
            'error_code' => $dto->errorCode,
        ];
    }
}
