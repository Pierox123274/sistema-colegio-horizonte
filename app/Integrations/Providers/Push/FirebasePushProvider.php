<?php

namespace App\Integrations\Providers\Push;

use App\Integrations\Contracts\PushNotificationProviderInterface;
use Illuminate\Support\Facades\Log;

final class FirebasePushProvider implements PushNotificationProviderInterface
{
    public function name(): string
    {
        return 'firebase';
    }

    public function isConfigured(): bool
    {
        return config('push.firebase.enabled')
            && (config('push.firebase.server_key') || config('push.firebase.credentials_path'));
    }

    public function sendToDevices(array $deviceTokens, string $title, string $body, array $data = []): bool
    {
        if (! $this->isConfigured() || $deviceTokens === []) {
            return (new NullPushProvider)->sendToDevices($deviceTokens, $title, $body, $data);
        }

        Log::channel('integrations')->info('firebase_push_stub', [
            'devices' => count($deviceTokens),
            'title' => $title,
        ]);

        return false;
    }
}
