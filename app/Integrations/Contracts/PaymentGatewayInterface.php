<?php

namespace App\Integrations\Contracts;

use App\Integrations\DTO\PaymentCheckoutDTO;

interface PaymentGatewayInterface
{
    public function name(): string;

    public function isConfigured(): bool;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createCheckoutSession(array $payload): PaymentCheckoutDTO;

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function parseWebhookPayload(array $payload): array;
}
