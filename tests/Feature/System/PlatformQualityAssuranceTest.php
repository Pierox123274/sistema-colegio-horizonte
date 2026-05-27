<?php

namespace Tests\Feature\System;

use App\Enums\IntranetRole;
use App\Models\Student;
use App\Models\User;
use App\Support\IntranetNavigation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Fase 29 — QA integral: permisos, rutas clave, portales y módulos críticos.
 */
class PlatformQualityAssuranceTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role]);

        return $user;
    }

    private function admin(): User
    {
        return $this->userWithRole(IntranetRole::Administrador->value);
    }

    public function test_public_home_is_accessible(): void
    {
        $this->get(route('public.home'))
            ->assertOk();
    }

    public function test_guest_is_redirected_from_protected_routes(): void
    {
        $routes = [
            'dashboard',
            'notifications.index',
            'teacher.dashboard',
            'student.dashboard',
            'intranet.cms.dashboard',
        ];

        foreach ($routes as $name) {
            $this->get(route($name))
                ->assertRedirect(route('login', absolute: false));
        }
    }

    public function test_admin_can_access_core_intranet_modules(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Dashboard')
                ->has('notificationCenter'));

        $this->actingAs($admin)->get(route('intranet.students.index'))->assertOk();
        $this->actingAs($admin)->get(route('intranet.payments.index'))->assertOk();
        $this->actingAs($admin)->get(route('intranet.system.health.index'))->assertOk();
        $this->actingAs($admin)->get(route('intranet.cms.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('intranet.lms.overview'))->assertOk();
        $this->actingAs($admin)->get(route('intranet.gamification.index'))->assertOk();
        $this->actingAs($admin)->get(route('intranet.security.audit-logs.index'))->assertOk();
        $this->actingAs($admin)->get(route('notifications.index'))->assertOk();
    }

    public function test_secretaria_has_operational_access_but_not_admin_only_modules(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria->value);

        $this->actingAs($secretaria)->get(route('intranet.students.index'))->assertOk();
        $this->actingAs($secretaria)->get(route('intranet.enrollments.index'))->assertOk();
        $this->actingAs($secretaria)->get(route('intranet.payments.index'))->assertOk();
        $this->actingAs($secretaria)->get(route('intranet.cms.dashboard'))->assertOk();
        $this->actingAs($secretaria)->get(route('notifications.index'))->assertOk();

        $this->actingAs($secretaria)->get(route('intranet.system.health.index'))->assertForbidden();
        $this->actingAs($secretaria)->get(route('intranet.gamification.index'))->assertForbidden();
        $this->actingAs($secretaria)->get(route('intranet.lms.overview'))->assertForbidden();
    }

    public function test_docente_sin_administrador_is_redirected_from_intranet_dashboard(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente->value);

        $this->actingAs($docente)
            ->get(route('dashboard'))
            ->assertRedirect(route('teacher.dashboard', absolute: false));

        $this->actingAs($docente)->get(route('teacher.dashboard'))->assertOk();
        $this->actingAs($docente)->get(route('teacher.adaptive-learning.index'))
            ->assertRedirect(route('teacher.pedagogical-panel.index', absolute: false));
    }

    public function test_estudiante_sin_administrador_is_redirected_to_student_portal(): void
    {
        $estudiante = $this->userWithRole(IntranetRole::Estudiante->value);
        Student::factory()->create(['user_id' => $estudiante->id]);

        $this->actingAs($estudiante)
            ->get(route('dashboard'))
            ->assertRedirect(route('student.dashboard', absolute: false));

        $this->actingAs($estudiante)->get(route('student.dashboard'))->assertOk();
        $this->actingAs($estudiante)->get(route('student.gamification.index'))->assertOk();
        $this->actingAs($estudiante)->get(route('student.classrooms.index'))->assertOk();
        $this->actingAs($estudiante)->get(route('student.ai-tutor.index'))->assertOk();
    }

    public function test_intranet_navigation_includes_notifications_link(): void
    {
        $admin = $this->admin();
        $labels = collect(IntranetNavigation::items($admin))->pluck('label');

        $this->assertTrue(
            $labels->contains('Notificaciones'),
            'El menú lateral debe incluir Notificaciones (Fase 29).'
        );
        $this->assertFalse(
            $labels->contains('Configuración'),
            'No debe mostrarse un ítem fantasma de Configuración deshabilitado.'
        );
    }

    public function test_notification_settings_are_available_for_intranet_roles(): void
    {
        foreach ([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ] as $role) {
            $user = $this->userWithRole($role);

            $this->actingAs($user)
                ->get(route('settings.notifications.edit'))
                ->assertOk();
        }
    }

    public function test_student_cannot_access_intranet_payments(): void
    {
        $estudiante = $this->userWithRole(IntranetRole::Estudiante->value);
        Student::factory()->create(['user_id' => $estudiante->id]);

        $this->actingAs($estudiante)
            ->get(route('intranet.payments.index'))
            ->assertForbidden();
    }
}
