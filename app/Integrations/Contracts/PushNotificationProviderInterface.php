<?php

namespace App\Integrations\Contracts;

interface PushNotificationProviderInterface
{
    public function name(): string;

    public function isConfigured(): bool;

    /**
     * @param  list<string>  $deviceTokens
     * @param  array<string, mixed>  $data
     */
    public function sendToDevices(array $deviceTokens, string $title, string $body, array $data = []): bool;
}
