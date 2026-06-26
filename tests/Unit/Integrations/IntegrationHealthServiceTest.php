<?php

namespace Tests\Unit\Integrations;

use App\Integrations\Services\IntegrationHealthService;
use App\Models\IntegrationEmailLog;
use App\Models\IntegrationWebhookLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class IntegrationHealthServiceTest extends TestCase
{
    use RefreshDatabase;

    private IntegrationHealthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(IntegrationHealthService::class);
    }

    public function test_snapshot_reports_ok_when_integrations_are_disabled(): void
    {
        Config::set('queue.default', 'database');
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.example.com');
        Config::set('integrations.calendar.enabled', false);
        Config::set('integrations.messaging.enabled', false);
        Config::set('integrations.payments.enabled', false);
        Config::set('integrations.push.enabled', false);
        Config::set('integrations.storage.external_enabled', false);

        $snapshot = $this->service->snapshot();

        $this->assertSame('ok', $snapshot['status']);
        $this->assertArrayHasKey('providers', $snapshot);
        $this->assertSame('ok', $snapshot['checks']['payments']['status']);
        $this->assertSame('ok', $snapshot['checks']['queue']['status']);
    }

    public function test_snapshot_warns_on_sync_queue_and_failed_webhooks(): void
    {
        Config::set('queue.default', 'sync');

        for ($i = 0; $i < 6; $i++) {
            IntegrationWebhookLog::query()->create([
                'provider' => 'payments',
                'event_type' => 'payment.updated',
                'status' => 'failed',
            ]);
        }

        IntegrationEmailLog::query()->create([
            'mailable_class' => 'Demo',
            'recipient_hash' => hash('sha256', 'a'),
            'status' => 'failed',
            'attempts' => 1,
            'mailer' => 'log',
        ]);

        $snapshot = $this->service->snapshot();

        $this->assertSame('warning', $snapshot['status']);
        $this->assertSame('warning', $snapshot['checks']['queue']['status']);
        $this->assertSame('warning', $snapshot['checks']['webhooks']['status']);
        $this->assertSame('warning', $snapshot['checks']['email_delivery']['status']);
    }

    public function test_snapshot_warns_when_enabled_provider_is_not_configured(): void
    {
        Config::set('queue.default', 'database');
        Config::set('integrations.messaging.enabled', true);

        $snapshot = $this->service->snapshot();

        $this->assertSame('warning', $snapshot['checks']['messaging']['status']);
        $this->assertStringContainsString('sin credenciales', $snapshot['checks']['messaging']['message']);
    }

    public function test_snapshot_warns_on_external_storage_with_local_disk(): void
    {
        Config::set('queue.default', 'database');
        Config::set('integrations.storage.external_enabled', true);
        Config::set('filesystems.default', 'local');

        $snapshot = $this->service->snapshot();

        $this->assertSame('warning', $snapshot['checks']['storage_external']['status']);
    }
}
