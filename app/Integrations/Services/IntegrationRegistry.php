<?php

namespace App\Integrations\Services;

use App\Integrations\Contracts\CalendarProviderInterface;
use App\Integrations\Contracts\MessagingProviderInterface;
use App\Integrations\Contracts\PaymentGatewayInterface;
use App\Integrations\Contracts\PushNotificationProviderInterface;
use App\Integrations\Providers\Calendar\GoogleCalendarProvider;
use App\Integrations\Providers\Calendar\NullCalendarProvider;
use App\Integrations\Providers\Messaging\NullMessagingProvider;
use App\Integrations\Providers\Messaging\WhatsAppProvider;
use App\Integrations\Providers\Payments\CulqiProvider;
use App\Integrations\Providers\Payments\MercadoPagoProvider;
use App\Integrations\Providers\Payments\NullPaymentGateway;
use App\Integrations\Providers\Push\FirebasePushProvider;
use App\Integrations\Providers\Push\NullPushProvider;

final class IntegrationRegistry
{
    public function calendar(): CalendarProviderInterface
    {
        if (! config('integrations.enabled') || ! config('integrations.calendar.enabled')) {
            return new NullCalendarProvider;
        }

        return match (config('integrations.calendar.provider')) {
            'google', 'google_calendar' => new GoogleCalendarProvider,
            default => new NullCalendarProvider,
        };
    }

    public function messaging(): MessagingProviderInterface
    {
        if (! config('integrations.enabled') || ! config('integrations.messaging.enabled')) {
            return new NullMessagingProvider;
        }

        return match (config('integrations.messaging.provider')) {
            'whatsapp' => new WhatsAppProvider,
            default => new NullMessagingProvider,
        };
    }

    public function payments(): PaymentGatewayInterface
    {
        if (! config('integrations.enabled') || ! config('integrations.payments.enabled')) {
            return new NullPaymentGateway;
        }

        return match (config('integrations.payments.provider')) {
            'culqi' => new CulqiProvider,
            'mercadopago', 'mercado_pago' => new MercadoPagoProvider,
            default => new NullPaymentGateway,
        };
    }

    public function push(): PushNotificationProviderInterface
    {
        if (! config('integrations.enabled') || ! config('integrations.push.enabled')) {
            return new NullPushProvider;
        }

        return match (config('integrations.push.provider')) {
            'firebase' => new FirebasePushProvider,
            default => new NullPushProvider,
        };
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function providerCards(): array
    {
        $calendar = $this->calendar();
        $messaging = $this->messaging();
        $payments = $this->payments();
        $push = $this->push();

        return [
            $this->card('calendar', 'Google Calendar', $calendar->name(), $calendar->isConfigured(), 'Sincronización de eventos y enlaces'),
            $this->card('meetings', 'Videoclases (Meet/Zoom/Teams)', 'meetings', $this->meetingsConfigured(), 'Enlaces manuales + API futura'),
            $this->card('mail', 'Correo SMTP', config('mail.default', 'log'), $this->mailConfigured(), 'Notificaciones y resúmenes institucionales'),
            $this->card('messaging', 'WhatsApp', $messaging->name(), $messaging->isConfigured(), 'Comunicados y alertas (preparación)'),
            $this->card('payments', 'Pasarela de pagos', $payments->name(), $payments->isConfigured(), 'Pensiones y cobros online'),
            $this->card('push', 'Push (Firebase)', $push->name(), $push->isConfigured(), 'Notificaciones móviles'),
            $this->card('storage', 'Almacenamiento externo', config('filesystems.default', 'local'), $this->externalStorageConfigured(), 'S3 / R2 / Spaces'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function card(string $key, string $label, string $provider, bool $configured, string $description): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'provider' => $provider,
            'configured' => $configured,
            'description' => $description,
            'status' => $configured ? 'configured' : 'not_configured',
        ];
    }

    private function mailConfigured(): bool
    {
        $mailer = (string) config('mail.default');

        return $mailer !== 'log' && config('mail.mailers.smtp.host') !== '127.0.0.1';
    }

    private function meetingsConfigured(): bool
    {
        return config('meetings.google_meet.enabled')
            || config('meetings.zoom.enabled')
            || config('meetings.teams.enabled');
    }

    private function externalStorageConfigured(): bool
    {
        if (! config('integrations.storage.external_enabled')) {
            return false;
        }

        $disk = (string) config('filesystems.default');

        return in_array($disk, ['s3', 'r2', 'spaces'], true)
            || (config('filesystems.disks.s3.key') && config('filesystems.disks.s3.bucket'));
    }
}
