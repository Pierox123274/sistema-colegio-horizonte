<?php

namespace Tests\Feature\Teacher;

use App\Enums\IntranetRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TeacherPortalTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_docente_entra_al_dashboard(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Dashboard'));
    }

    public function test_administrador_puede_entrar_al_portal_docente(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Dashboard'));
    }

    public function test_secretaria_no_entra_al_portal_docente(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $this->actingAs($secretaria)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_estudiante_no_entra_al_portal_docente(): void
    {
        $estudiante = $this->userWithRole(IntranetRole::Estudiante);

        $this->actingAs($estudiante)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_apoderado_no_entra_al_portal_docente(): void
    {
        $apoderado = $this->userWithRole(IntranetRole::Apoderado);

        $this->actingAs($apoderado)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_docente_ve_asistencia(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.attendance.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Attendance/Index'));
    }

    public function test_docente_ve_notas(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.grades.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Grades/Index'));
    }

    public function test_docente_ve_estudiantes(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.students.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Students/Index'));
    }

    public function test_docente_ve_reportes(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.reports.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Reports/Index'));
    }
}
