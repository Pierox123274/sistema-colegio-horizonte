<?php

namespace Tests\Feature\System;

use App\Enums\IntranetRole;
use App\Models\User;
use App\Services\SystemHealthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->syncRoles([IntranetRole::Administrador->value]);

        return $u;
    }

    public function test_admin_health_endpoint_exposes_readiness_checks(): void
    {
        $this->actingAs($this->admin())
            ->get(route('intranet.system.health.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/System/Health')
                ->has('health.checks')
                ->has('health.checks.database')
                ->has('health.checks.app_debug')
                ->has('health.storage.public_storage_linked'));
    }

    public function test_health_service_returns_production_check_flags(): void
    {
        $snapshot = app(SystemHealthService::class)->healthSnapshot();

        $this->assertArrayHasKey('checks', $snapshot);
        $this->assertArrayHasKey('storage_writable', $snapshot['checks']);
        $this->assertArrayHasKey('https', $snapshot['checks']);
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get(route('public.home'));

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy');
    }

    public function test_system_routes_are_protected_for_non_admin_users(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Secretaria->value]);

        $this->actingAs($user)
            ->get(route('intranet.system.health.index'))
            ->assertForbidden();
    }
}
