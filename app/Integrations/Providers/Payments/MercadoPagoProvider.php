<?php

namespace App\Integrations\Providers\Payments;

use App\Integrations\Contracts\PaymentGatewayInterface;
use App\Integrations\DTO\PaymentCheckoutDTO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/** Preparación Mercado Pago — checkout simulado hasta configurar credenciales reales. */
final class MercadoPagoProvider implements PaymentGatewayInterface
{
    public function name(): string
    {
        return 'mercadopago';
    }

    public function isConfigured(): bool
    {
        return config('payments_gateway.mercadopago.enabled')
            && config('payments_gateway.mercadopago.access_token');
    }

    public function createCheckoutSession(array $payload): PaymentCheckoutDTO
    {
        if (! $this->isConfigured()) {
            return (new NullPaymentGateway)->createCheckoutSession($payload);
        }

        $reference = (string) ($payload['reference'] ?? Str::uuid());
        $intentId = 'mp_intent_'.Str::lower(Str::random(12));

        Log::channel('integrations')->info('mercadopago_checkout_stub', [
            'reference' => $reference,
            'amount' => $payload['amount'] ?? null,
        ]);

        return new PaymentCheckoutDTO(
            externalReference: $reference,
            amount: (float) ($payload['amount'] ?? 0),
            currency: (string) ($payload['currency'] ?? 'PEN'),
            description: (string) ($payload['description'] ?? 'Pago'),
            checkoutUrl: null,
            paymentIntentId: $intentId,
            metadata: ['provider' => $this->name(), 'sandbox' => config('payments_gateway.mercadopago.sandbox')],
            success: true,
        );
    }

    public function parseWebhookPayload(array $payload): array
    {
        return [
            'status' => $payload['status'] ?? $payload['action'] ?? 'unknown',
            'external_id' => $payload['data']['id'] ?? $payload['id'] ?? null,
            'reference' => $payload['external_reference'] ?? null,
        ];
    }
}
