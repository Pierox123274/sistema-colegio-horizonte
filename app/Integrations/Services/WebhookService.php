<?php

namespace App\Integrations\Services;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Integrations\Exceptions\IntegrationException;
use App\Models\IntegrationWebhookLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class WebhookService
{
    public function __construct(
        private readonly IntegrationRegistry $registry,
        private readonly AuditService $audit,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function receive(string $provider, Request $request): array
    {
        if (! config('integrations.webhooks.enabled')) {
            return ['status' => 'disabled'];
        }

        $payload = $request->all();
        $signatureValid = $this->validateSignature($provider, $request);
        $summary = $this->sanitizePayload($payload);

        $log = IntegrationWebhookLog::query()->create([
            'provider' => $provider,
            'event_type' => (string) ($payload['type'] ?? $payload['event'] ?? 'unknown'),
            'status' => $signatureValid ? 'received' : 'invalid_signature',
            'signature_valid' => $signatureValid ? 'yes' : 'no',
            'payload_summary' => $summary,
        ]);

        if (! $signatureValid && $this->secretConfigured($provider)) {
            $log->update(['status' => 'failed', 'error_message' => 'Invalid signature']);

            throw IntegrationException::invalidSignature($provider);
        }

        $parsed = match ($provider) {
            'mercadopago', 'mercado_pago', 'payments' => $this->registry->payments()->parseWebhookPayload($payload),
            'calendar', 'google_calendar' => ['status' => 'acknowledged'],
            default => ['status' => 'ignored'],
        };

        $log->update([
            'status' => 'processed',
            'processed_at' => now(),
            'payload_summary' => array_merge($summary, ['parsed' => $parsed]),
        ]);

        Log::channel('integrations')->info('webhook_processed', [
            'provider' => $provider,
            'log_id' => $log->id,
        ]);

        return ['status' => 'ok', 'log_id' => $log->id, 'parsed' => $parsed];
    }

    public function replay(IntegrationWebhookLog $log, ?User $actor = null): bool
    {
        if ($log->replay_count >= (int) config('integrations.webhooks.max_replay_attempts', 3)) {
            return false;
        }

        $log->increment('replay_count');
        $log->update(['status' => 'replayed', 'processed_at' => now()]);

        if ($actor !== null) {
            $this->audit->log(
                AuditAction::Update,
                AuditModule::Integrations,
                $actor,
                IntegrationWebhookLog::class,
                $log->id,
                'Replay webhook',
                null,
                ['provider' => $log->provider],
                AuditResult::Success,
            );
        }

        return true;
    }

    private function validateSignature(string $provider, Request $request): bool
    {
        $secret = match ($provider) {
            'mercadopago', 'mercado_pago', 'payments' => config('integrations.webhooks.payment_secret')
                ?? config('payments_gateway.mercadopago.webhook_secret'),
            'calendar', 'google_calendar' => config('integrations.webhooks.calendar_secret'),
            default => null,
        };

        if ($secret === null || $secret === '') {
            return true;
        }

        $header = (string) config('integrations.webhooks.signature_header', 'X-Webhook-Signature');
        $received = (string) $request->header($header);
        if ($received === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $request->getContent(), (string) $secret);

        return hash_equals($expected, $received);
    }

    private function secretConfigured(string $provider): bool
    {
        return match ($provider) {
            'mercadopago', 'payments' => (bool) (config('integrations.webhooks.payment_secret')
                ?? config('payments_gateway.mercadopago.webhook_secret')),
            'calendar' => (bool) config('integrations.webhooks.calendar_secret'),
            default => false,
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function sanitizePayload(array $payload): array
    {
        $keys = array_slice(array_keys($payload), 0, 12);

        return array_intersect_key($payload, array_flip($keys));
    }
}
