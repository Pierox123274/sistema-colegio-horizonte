<?php

namespace Tests\Unit\Integrations;

use App\Integrations\Services\InstitutionMailService;
use App\Jobs\RetryInstitutionEmailJob;
use App\Models\IntegrationEmailLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class InstitutionMailServiceTest extends TestCase
{
    use RefreshDatabase;

    private InstitutionMailService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(InstitutionMailService::class);
    }

    public function test_health_returns_mailer_status(): void
    {
        Config::set('mail.default', 'log');
        Config::set('integrations.mail.preview_enabled', true);

        IntegrationEmailLog::query()->create([
            'mailable_class' => Mailable::class,
            'recipient_hash' => hash('sha256', 'a@b.com'),
            'status' => 'queued',
            'attempts' => 0,
            'mailer' => 'log',
        ]);
        IntegrationEmailLog::query()->create([
            'mailable_class' => Mailable::class,
            'recipient_hash' => hash('sha256', 'c@d.com'),
            'status' => 'failed',
            'attempts' => 1,
            'mailer' => 'log',
        ]);

        $health = $this->service->health();

        $this->assertSame('log', $health['mailer']);
        $this->assertFalse($health['configured']);
        $this->assertSame(1, $health['queue_pending']);
        $this->assertSame(1, $health['failed']);
        $this->assertTrue($health['preview_enabled']);
    }

    public function test_send_logged_queues_mail_and_creates_delivery_log(): void
    {
        Config::set('integrations.mail.delivery_log_enabled', true);
        Mail::fake();

        $mailable = new class extends Mailable
        {
            public function build(): self
            {
                return $this->html('Prueba institucional');
            }
        };

        $this->service->sendLogged($mailable, 'familia@demo.com');

        $this->assertDatabaseHas('integration_email_logs', [
            'status' => 'queued',
            'attempts' => 1,
        ]);
        Mail::assertQueued($mailable::class);
    }

    public function test_send_logged_marks_failed_and_dispatches_retry_on_error(): void
    {
        Config::set('integrations.mail.delivery_log_enabled', true);
        Queue::fake();

        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('queue')->once()->andThrow(new \RuntimeException('SMTP no disponible'));

        $mailable = new class extends Mailable
        {
            public function build(): self
            {
                return $this->html('Fallo');
            }
        };

        try {
            $this->service->sendLogged($mailable, 'error@demo.com');
            $this->fail('Se esperaba excepción al encolar correo.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('SMTP no disponible', $exception->getMessage());
        }

        $this->assertDatabaseHas('integration_email_logs', [
            'status' => 'failed',
        ]);
        Queue::assertPushed(RetryInstitutionEmailJob::class);
    }

    public function test_mark_sent_updates_log(): void
    {
        $log = IntegrationEmailLog::query()->create([
            'mailable_class' => Mailable::class,
            'recipient_hash' => hash('sha256', 'x'),
            'status' => 'queued',
            'attempts' => 1,
            'mailer' => 'log',
        ]);

        $this->service->markSent($log);

        $log->refresh();
        $this->assertSame('sent', $log->status);
        $this->assertNotNull($log->sent_at);
    }

    public function test_mark_failed_retries_until_max_attempts(): void
    {
        Config::set('integrations.mail.max_retries', 3);
        Queue::fake();

        $log = IntegrationEmailLog::query()->create([
            'mailable_class' => Mailable::class,
            'recipient_hash' => hash('sha256', 'y'),
            'status' => 'queued',
            'attempts' => 1,
            'mailer' => 'log',
        ]);

        $this->service->markFailed($log, 'Timeout');

        $log->refresh();
        $this->assertSame('retrying', $log->status);
        $this->assertSame(2, $log->attempts);
        Queue::assertPushed(RetryInstitutionEmailJob::class);

        $this->service->markFailed($log->fresh(), 'Timeout otra vez');
        $log->refresh();
        $this->assertSame('failed', $log->status);
        $this->assertSame(3, $log->attempts);
    }
}
