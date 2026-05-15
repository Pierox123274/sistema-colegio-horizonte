<?php

namespace Tests\Feature\Auth;

use App\Enums\IntranetRole;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Secretaria->value]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_estudiante_login_redirige_al_portal_estudiante(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Estudiante->value]);
        Student::factory()->create(['user_id' => $user->id]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('student.dashboard', absolute: false));
    }

    public function test_docente_login_redirige_al_portal_docente(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Docente->value]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('teacher.dashboard', absolute: false));
    }

    public function test_administrador_login_redirige_a_intranet_dashboard(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Administrador->value]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_usuario_docente_y_administrador_redirige_a_intranet_dashboard(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([
            IntranetRole::Docente->value,
            IntranetRole::Administrador->value,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_guest_is_redirected_from_intranet_dashboard(): void
    {
        $this->get('/intranet/dashboard')->assertRedirect(
            route('login', absolute: false),
        );
    }
}
