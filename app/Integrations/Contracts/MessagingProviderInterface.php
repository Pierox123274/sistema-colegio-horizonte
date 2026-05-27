<?php

namespace App\Integrations\Contracts;

interface MessagingProviderInterface
{
    public function name(): string;

    public function isConfigured(): bool;

    /**
     * @param  array<string, mixed>  $context
     */
    public function sendTemplate(string $toE164, string $templateKey, array $context = []): bool;
}
