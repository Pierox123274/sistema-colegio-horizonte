<?php

namespace App\Integrations\Providers\Messaging;

use App\Integrations\Contracts\MessagingProviderInterface;
use Illuminate\Support\Facades\Log;

/** Stub preparado para API oficial de WhatsApp Business (sin cobro en esta fase). */
final class WhatsAppProvider implements MessagingProviderInterface
{
    public function name(): string
    {
        return 'whatsapp';
    }

    public function isConfigured(): bool
    {
        return config('messaging.whatsapp.enabled')
            && config('messaging.whatsapp.api_token')
            && config('messaging.whatsapp.phone_number_id');
    }

    public function sendTemplate(string $toE164, string $templateKey, array $context = []): bool
    {
        if (! $this->isConfigured()) {
            return (new NullMessagingProvider)->sendTemplate($toE164, $templateKey, $context);
        }

        Log::channel('integrations')->info('whatsapp_send_stub', [
            'to_hash' => hash('sha256', $toE164),
            'template' => $templateKey,
        ]);

        return false;
    }
}
