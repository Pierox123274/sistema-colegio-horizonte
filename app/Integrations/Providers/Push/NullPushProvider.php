<?php

namespace App\Integrations\Providers\Push;

use App\Integrations\Contracts\PushNotificationProviderInterface;

final class NullPushProvider implements PushNotificationProviderInterface
{
    public function name(): string
    {
        return 'null';
    }

    public function isConfigured(): bool
    {
        return false;
    }

    public function sendToDevices(array $deviceTokens, string $title, string $body, array $data = []): bool
    {
        return false;
    }
}
