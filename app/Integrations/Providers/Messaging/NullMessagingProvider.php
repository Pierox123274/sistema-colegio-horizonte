<?php

namespace App\Integrations\Providers\Messaging;

use App\Integrations\Contracts\MessagingProviderInterface;
use Illuminate\Support\Facades\Log;

final class NullMessagingProvider implements MessagingProviderInterface
{
    public function name(): string
    {
        return 'null';
    }

    public function isConfigured(): bool
    {
        return false;
    }

    public function sendTemplate(string $toE164, string $templateKey, array $context = []): bool
    {
        Log::channel('integrations')->debug('messaging_skipped', [
            'provider' => $this->name(),
            'template' => $templateKey,
        ]);

        return false;
    }
}
