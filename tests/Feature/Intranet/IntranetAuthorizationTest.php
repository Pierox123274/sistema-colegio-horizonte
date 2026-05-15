<?php

namespace Tests\Feature\Intranet;

use App\Enums\IntranetRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class IntranetAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_intranet_dashboard(): void
    {
        $response = $this->get('/intranet/dashboard');

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_authenticated_user_without_intranet_role_cannot_access_dashboard(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([]);

        $response = $this->actingAs($user)->get('/intranet/dashboard');

        $response->assertForbidden();
    }

    public function test_authenticated_user_without_role_cannot_access_profile(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertForbidden();
    }

    #[DataProvider('intranetRoleProvider')]
    public function test_user_with_assigned_role_can_access_intranet_dashboard(string $role): void
    {
        $user = User::factory()->create();
        $user->syncRoles([$role]);

        $response = $this->actingAs($user)->get('/intranet/dashboard');

        $response->assertOk();
    }

    public function test_docente_sin_administrador_es_redirigido_desde_intranet_dashboard_al_portal_docente(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Docente->value]);

        $this->actingAs($user)
            ->get('/intranet/dashboard')
            ->assertRedirect(route('teacher.dashboard', absolute: false));
    }

    public function test_docente_con_administrador_puede_ver_intranet_dashboard(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([
            IntranetRole::Docente->value,
            IntranetRole::Administrador->value,
        ]);

        $this->actingAs($user)->get('/intranet/dashboard')->assertOk();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function intranetRoleProvider(): array
    {
        return [
            'administrador' => [IntranetRole::Administrador->value],
            'secretaria' => [IntranetRole::Secretaria->value],
            'estudiante' => [IntranetRole::Estudiante->value],
            'apoderado' => [IntranetRole::Apoderado->value],
        ];
    }
}
