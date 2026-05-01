<?php

namespace Tests\Feature\Intranet;

use App\Enums\IntranetRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntranetDashboardUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_intranet_dashboard_renders_inertia_component(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Administrador]);

        $response = $this->actingAs($user)->get('/intranet/dashboard');

        $response->assertOk();
        $response->assertSee('Intranet/Dashboard', false);
    }
}
