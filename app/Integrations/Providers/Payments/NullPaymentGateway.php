<?php

namespace App\Integrations\Providers\Payments;

use App\Integrations\Contracts\PaymentGatewayInterface;
use App\Integrations\DTO\PaymentCheckoutDTO;

final class NullPaymentGateway implements PaymentGatewayInterface
{
    public function name(): string
    {
        return 'null';
    }

    public function isConfigured(): bool
    {
        return false;
    }

    public function createCheckoutSession(array $payload): PaymentCheckoutDTO
    {
        return new PaymentCheckoutDTO(
            externalReference: (string) ($payload['reference'] ?? 'local'),
            amount: (float) ($payload['amount'] ?? 0),
            currency: (string) ($payload['currency'] ?? 'PEN'),
            description: (string) ($payload['description'] ?? 'Pago institucional'),
            success: false,
            errorCode: 'gateway_disabled',
        );
    }

    public function parseWebhookPayload(array $payload): array
    {
        return [
            'status' => 'ignored',
            'external_id' => $payload['id'] ?? null,
        ];
    }
}
