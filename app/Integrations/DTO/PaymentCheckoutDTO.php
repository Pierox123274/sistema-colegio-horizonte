<?php

namespace App\Integrations\DTO;

final readonly class PaymentCheckoutDTO
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $externalReference,
        public float $amount,
        public string $currency,
        public string $description,
        public ?string $checkoutUrl = null,
        public ?string $paymentIntentId = null,
        public array $metadata = [],
        public bool $success = false,
        public ?string $errorCode = null,
    ) {}
}
