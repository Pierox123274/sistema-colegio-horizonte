<?php

namespace Tests\Feature\Security;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\IntranetRole;
use App\Models\AuditLog;
use App\Models\LoginAttempt;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AuditSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_login_records_attempt_and_audit_log(): void
    {
        $user = $this->userWithRole(IntranetRole::Administrador);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect();

        $this->assertDatabaseHas('login_attempts', [
            'email' => $user->email,
            'successful' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => AuditAction::Login->value,
            'module' => AuditModule::Auth->value,
        ]);
    }

    public function test_failed_login_is_locked_after_max_attempts(): void
    {
        $user = $this->userWithRole(IntranetRole::Administrador);
        $max = config('security.login_max_attempts', 5);

        for ($i = 0; $i < $max; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGreaterThanOrEqual(
            $max,
            LoginAttempt::query()->where('email', $user->email)->where('successful', false)->count()
        );
    }

    public function test_logout_invalidates_tracked_session(): void
    {
        $user = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => AuditAction::Logout->value,
        ]);
    }

    public function test_admin_can_view_audit_logs(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        AuditLog::factory()->create(['user_id' => $admin->id]);

        $this->actingAs($admin)
            ->get(route('intranet.security.audit-logs.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Security/AuditLogs')
                ->has('logs.data', 1)
                ->has('timeline'));
    }

    public function test_teacher_only_sees_own_audit_activity(): void
    {
        $teacher = $this->userWithRole(IntranetRole::Docente);
        $other = $this->userWithRole(IntranetRole::Administrador);

        AuditLog::factory()->create([
            'user_id' => $teacher->id,
            'description' => 'Actividad docente',
        ]);
        AuditLog::factory()->create([
            'user_id' => $other->id,
            'description' => 'Actividad admin',
        ]);

        $this->actingAs($teacher)
            ->get(route('intranet.security.audit-logs.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('logs.data', 1)
                ->where('logs.data.0.description', 'Actividad docente'));
    }

    public function test_student_cannot_access_security_module(): void
    {
        $student = $this->userWithRole(IntranetRole::Estudiante);

        $this->actingAs($student)
            ->get(route('intranet.security.audit-logs.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_active_sessions(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        UserSession::factory()->create([
            'user_id' => $admin->id,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('intranet.security.sessions.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Security/Sessions')
                ->has('sessions.data', 1));
    }

    public function test_analytics_export_generates_audit_log(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)
            ->get(route('intranet.reports.analytics.export.csv', ['type' => 'academic']))
            ->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => AuditAction::Export->value,
        ]);
    }

    public function test_secretaria_can_view_access_monitor_but_not_sessions(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $this->actingAs($secretaria)
            ->get(route('intranet.security.access-monitor.index'))
            ->assertOk();

        $this->actingAs($secretaria)
            ->get(route('intranet.security.sessions.index'))
            ->assertForbidden();
    }
}
