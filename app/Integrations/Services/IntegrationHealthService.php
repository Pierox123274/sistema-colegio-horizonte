<?php

namespace App\Integrations\Services;

use App\Models\IntegrationEmailLog;
use App\Models\IntegrationWebhookLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class IntegrationHealthService
{
    public function __construct(
        private readonly IntegrationRegistry $registry,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function snapshot(): array
    {
        $cards = $this->registry->providerCards();
        $failedWebhooks = Schema::hasTable('integration_webhook_logs')
            ? (int) IntegrationWebhookLog::query()->where('status', 'failed')->count()
            : 0;
        $pendingEmails = Schema::hasTable('integration_email_logs')
            ? (int) IntegrationEmailLog::query()->whereIn('status', ['queued', 'retrying'])->count()
            : 0;
        $failedEmails = Schema::hasTable('integration_email_logs')
            ? (int) IntegrationEmailLog::query()->where('status', 'failed')->count()
            : 0;

        $queuePending = Schema::hasTable('jobs') ? (int) DB::table('jobs')->count() : 0;

        $checks = [
            'smtp' => $this->checkSmtp(),
            'queue' => $this->checkQueue($queuePending),
            'calendar' => $this->checkProvider('calendar', $this->registry->calendar()->isConfigured()),
            'payments' => $this->checkProvider('payments', $this->registry->payments()->isConfigured()),
            'messaging' => $this->checkProvider('messaging', $this->registry->messaging()->isConfigured()),
            'push' => $this->checkProvider('push', $this->registry->push()->isConfigured()),
            'storage_external' => $this->checkStorage(),
            'webhooks' => [
                'label' => 'Webhooks',
                'status' => $failedWebhooks > 5 ? 'warning' : 'ok',
                'value' => $failedWebhooks,
                'message' => "{$failedWebhooks} webhooks fallidos registrados.",
            ],
            'email_delivery' => [
                'label' => 'Email delivery',
                'status' => $failedEmails > 0 ? 'warning' : 'ok',
                'value' => ['pending' => $pendingEmails, 'failed' => $failedEmails],
                'message' => "Pendientes: {$pendingEmails}, fallidos: {$failedEmails}.",
            ],
        ];

        $statuses = collect($checks)->pluck('status');
        $overall = $statuses->contains('critical')
            ? 'critical'
            : ($statuses->contains('warning') ? 'warning' : 'ok');

        return [
            'status' => $overall,
            'providers' => $cards,
            'checks' => $checks,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkSmtp(): array
    {
        $configured = collect($this->registry->providerCards())
            ->firstWhere('key', 'mail')['configured'] ?? false;

        return [
            'label' => 'SMTP',
            'status' => $configured ? 'ok' : 'warning',
            'value' => config('mail.default'),
            'message' => $configured ? 'SMTP listo.' : 'Usar mailer log o configurar SMTP real.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkQueue(int $pending): array
    {
        $driver = (string) config('queue.default');

        return [
            'label' => 'Queue',
            'status' => ($driver === 'sync' || $pending > 500) ? 'warning' : 'ok',
            'value' => ['driver' => $driver, 'pending' => $pending],
            'message' => $driver === 'sync'
                ? 'Cola en sync; usar database/redis en producción.'
                : "Jobs pendientes: {$pending}.",
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkProvider(string $name, bool $configured): array
    {
        $enabled = (bool) config("integrations.{$name}.enabled", false);

        return [
            'label' => ucfirst($name),
            'status' => (! $enabled || $configured) ? 'ok' : 'warning',
            'value' => $enabled ? 'enabled' : 'disabled',
            'message' => ! $enabled
                ? 'Módulo deshabilitado por configuración.'
                : ($configured ? 'Proveedor configurado.' : 'Proveedor habilitado pero sin credenciales.'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkStorage(): array
    {
        $disk = (string) config('filesystems.default');
        $external = config('integrations.storage.external_enabled');

        return [
            'label' => 'Storage',
            'status' => $external && $disk === 'local' ? 'warning' : 'ok',
            'value' => $disk,
            'message' => $external
                ? "Disco activo: {$disk}. Para S3 use FILESYSTEM_DISK=s3."
                : 'Almacenamiento local activo.',
        ];
    }
}
