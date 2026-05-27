<?php

namespace Tests\Feature\Integrations;

use App\Enums\IntranetRole;
use App\Integrations\DTO\CalendarEventDTO;
use App\Integrations\Providers\Calendar\GoogleCalendarProvider;
use App\Integrations\Providers\Payments\NullPaymentGateway;
use App\Integrations\Services\IntegrationHealthService;
use App\Integrations\Services\IntegrationRegistry;
use App\Models\IntegrationWebhookLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ExternalIntegrationsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Administrador->value]);

        return $user;
    }

    public function test_admin_can_view_integrations_panel(): void
    {
        $this->actingAs($this->admin())
            ->get(route('intranet.integrations.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Integrations/Index')
                ->has('providers')
                ->has('health'));
    }

    public function test_secretaria_cannot_view_integrations(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Secretaria->value]);

        $this->actingAs($user)
            ->get(route('intranet.integrations.index'))
            ->assertForbidden();
    }

    public function test_calendar_null_fallback_builds_add_url(): void
    {
        Config::set('integrations.calendar.enabled', false);
        $registry = app(IntegrationRegistry::class);
        $provider = $registry->calendar();

        $this->assertFalse($provider->isConfigured());

        $event = new CalendarEventDTO(
            title: 'Clase de prueba',
            description: 'Desc',
            startsAt: now()->addDay(),
            endsAt: now()->addDay()->addHour(),
        );

        $url = $provider->buildAddToCalendarUrl($event);
        $this->assertStringContainsString('calendar.google.com', $url);
    }

    public function test_google_calendar_export_when_enabled(): void
    {
        Config::set('integrations.enabled', true);
        Config::set('integrations.calendar.enabled', true);
        Config::set('calendar.google.enabled', true);
        Config::set('calendar.google.client_id', 'test-client');
        Config::set('calendar.google.client_secret', 'test-secret');

        $provider = new GoogleCalendarProvider;
        $this->assertTrue($provider->isConfigured());

        $result = $provider->exportEvent(new CalendarEventDTO(
            title: 'Reunión',
            description: null,
            startsAt: now(),
            endsAt: now()->addHour(),
        ));

        $this->assertTrue($result->success);
        $this->assertNotNull($result->addToCalendarUrl);
    }

    public function test_payment_gateway_null_when_disabled(): void
    {
        Config::set('integrations.payments.enabled', false);
        $gateway = app(IntegrationRegistry::class)->payments();

        $this->assertInstanceOf(NullPaymentGateway::class, $gateway);
        $dto = $gateway->createCheckoutSession(['amount' => 100, 'reference' => 'x']);
        $this->assertFalse($dto->success);
    }

    public function test_webhook_receives_and_logs(): void
    {
        Config::set('integrations.webhooks.enabled', true);

        $response = $this->postJson(route('webhooks.payments'), [
            'type' => 'payment.updated',
            'id' => 'evt_123',
        ]);

        $response->assertOk();
        $this->assertGreaterThan(0, IntegrationWebhookLog::query()->count());
    }

    public function test_webhook_rejects_invalid_signature_when_secret_set(): void
    {
        Config::set('integrations.webhooks.enabled', true);
        Config::set('integrations.webhooks.payment_secret', 'secret-test');

        $this->postJson(route('webhooks.payments'), ['id' => '1'])
            ->assertStatus(400);
    }

    public function test_webhook_with_valid_signature(): void
    {
        Config::set('integrations.webhooks.enabled', true);
        Config::set('integrations.webhooks.payment_secret', 'secret-test');

        $payload = json_encode(['id' => 'pay_1', 'status' => 'approved']);
        $signature = hash_hmac('sha256', $payload, 'secret-test');

        $this->call(
            'POST',
            route('webhooks.payments'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-Webhook-Signature' => $signature,
            ],
            $payload,
        )->assertOk();
    }

    public function test_integration_health_snapshot(): void
    {
        $health = app(IntegrationHealthService::class)->snapshot();

        $this->assertArrayHasKey('providers', $health);
        $this->assertArrayHasKey('checks', $health);
        $this->assertArrayHasKey('status', $health);
    }

    public function test_system_health_includes_integrations(): void
    {
        $this->actingAs($this->admin())
            ->get(route('intranet.system.health.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->has('health.integrations'));
    }
}
