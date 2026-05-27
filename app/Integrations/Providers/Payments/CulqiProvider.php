<?php

namespace App\Integrations\Providers\Payments;

use App\Integrations\Contracts\PaymentGatewayInterface;
use App\Integrations\DTO\PaymentCheckoutDTO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class CulqiProvider implements PaymentGatewayInterface
{
    public function name(): string
    {
        return 'culqi';
    }

    public function isConfigured(): bool
    {
        return config('payments_gateway.culqi.enabled')
            && config('payments_gateway.culqi.secret_key');
    }

    public function createCheckoutSession(array $payload): PaymentCheckoutDTO
    {
        if (! $this->isConfigured()) {
            return (new NullPaymentGateway)->createCheckoutSession($payload);
        }

        $reference = (string) ($payload['reference'] ?? Str::uuid());

        Log::channel('integrations')->info('culqi_checkout_stub', ['reference' => $reference]);

        return new PaymentCheckoutDTO(
            externalReference: $reference,
            amount: (float) ($payload['amount'] ?? 0),
            currency: (string) ($payload['currency'] ?? 'PEN'),
            description: (string) ($payload['description'] ?? 'Pago'),
            paymentIntentId: 'culqi_'.Str::lower(Str::random(10)),
            metadata: ['provider' => $this->name()],
            success: true,
        );
    }

    public function parseWebhookPayload(array $payload): array
    {
        return [
            'status' => $payload['type'] ?? 'unknown',
            'external_id' => $payload['id'] ?? null,
        ];
    }
}
