<?php

namespace Tests\Feature\Intranet;

use App\Enums\IntranetRole;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AcademicYearManagementTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_administrador_lista_anos_academicos(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        AcademicYear::factory()->count(2)->create();

        $this->actingAs($admin)
            ->get(route('intranet.academic-years.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/AcademicYears/Index')
                ->has('years.data', 2)
                ->where('permissions.manage', true));
    }

    public function test_administrador_accede_formulario_crear(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)
            ->get(route('intranet.academic-years.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Intranet/AcademicYears/Create'));
    }

    public function test_administrador_registra_ano_academico(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)
            ->post(route('intranet.academic-years.store'), [
                'name' => 'Año escolar 2030',
                'year' => 2030,
                'starts_at' => '2030-03-01',
                'ends_at' => '2030-12-15',
                'is_active' => true,
            ])
            ->assertRedirect(route('intranet.academic-years.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('academic_years', [
            'year' => 2030,
            'name' => 'Año escolar 2030',
            'is_active' => true,
        ]);
    }

    public function test_administrador_edita_ano_academico(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $year = AcademicYear::factory()->create(['year' => 2031]);

        $this->actingAs($admin)
            ->get(route('intranet.academic-years.edit', $year))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/AcademicYears/Edit')
                ->where('year.id', $year->id));

        $this->actingAs($admin)
            ->put(route('intranet.academic-years.update', $year), [
                'name' => 'Año actualizado',
                'year' => 2031,
                'starts_at' => '2031-03-01',
                'ends_at' => '2031-12-20',
                'is_active' => false,
            ])
            ->assertRedirect(route('intranet.academic-years.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'name' => 'Año actualizado',
            'is_active' => false,
        ]);
    }

    public function test_secretaria_puede_gestionar_anos_academicos(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $this->actingAs($secretaria)
            ->get(route('intranet.academic-years.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('permissions.manage', true));

        $this->actingAs($secretaria)
            ->get(route('intranet.academic-years.create'))
            ->assertOk();
    }

    public function test_docente_no_accede_anos_academicos(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('intranet.academic-years.index'))
            ->assertForbidden();
    }
}
