<?php

namespace Tests\Feature\Intranet;

use App\Enums\IntranetRole;
use App\Models\EducationalLevel;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicStructureTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_administrador_crea_nivel_educativo(): void
    {
        $user = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($user)->post(route('intranet.academic.levels.store'), [
            'code' => 'TST-NIV',
            'name' => 'Nivel prueba',
            'description' => 'Demo',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('intranet.academic.levels.index'));
        $this->assertDatabaseHas('educational_levels', ['code' => 'TST-NIV']);
    }

    public function test_administrador_crea_grado(): void
    {
        $level = EducationalLevel::factory()->create();

        $user = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($user)->post(route('intranet.academic.grades.store'), [
            'educational_level_id' => $level->id,
            'code' => 'TST-G1',
            'name' => 'Grado demo',
            'order' => 1,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('grades', [
            'code' => 'TST-G1',
            'educational_level_id' => $level->id,
        ]);
    }

    public function test_secretaria_puede_listar_y_ver_niveles_pero_no_crear(): void
    {
        $level = EducationalLevel::factory()->create();

        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $this->actingAs($secretaria)
            ->get(route('intranet.academic.levels.index'))
            ->assertOk()
            ->assertSee('Intranet/Academic/Levels/Index', false);

        $this->actingAs($secretaria)
            ->get(route('intranet.academic.levels.show', $level))
            ->assertOk();

        $this->actingAs($secretaria)
            ->post(route('intranet.academic.levels.store'), [
                'code' => 'NO-AUTH',
                'name' => 'X',
                'is_active' => true,
            ])
            ->assertForbidden();
    }

    public function test_docente_puede_listar_y_ver_sin_editar(): void
    {
        $level = EducationalLevel::factory()->create();

        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('intranet.academic.levels.index'))
            ->assertOk();

        $this->actingAs($docente)
            ->get(route('intranet.academic.levels.show', $level))
            ->assertOk();

        $this->actingAs($docente)
            ->get(route('intranet.academic.levels.create'))
            ->assertForbidden();
    }

    public function test_estudiante_no_accede_a_niveles(): void
    {
        $user = $this->userWithRole(IntranetRole::Estudiante);

        $this->actingAs($user)
            ->get(route('intranet.academic.levels.index'))
            ->assertForbidden();
    }

    public function test_apoderado_no_accede_a_niveles(): void
    {
        $user = $this->userWithRole(IntranetRole::Apoderado);

        $this->actingAs($user)
            ->get(route('intranet.academic.levels.index'))
            ->assertForbidden();
    }

    public function test_grado_pertenece_a_nivel_en_base_de_datos(): void
    {
        $level = EducationalLevel::factory()->create();

        $grade = Grade::factory()->create([
            'educational_level_id' => $level->id,
            'order' => 1,
        ]);

        $this->assertSame($level->id, $grade->educationalLevel->id);
    }

    public function test_validacion_codigo_duplicado_en_nivel_educativo(): void
    {
        EducationalLevel::factory()->create(['code' => 'DUP-N']);

        $user = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($user)->post(route('intranet.academic.levels.store'), [
            'code' => 'DUP-N',
            'name' => 'Otro',
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_validacion_capacidad_seccion_debe_ser_positiva(): void
    {
        $grade = Grade::factory()->create();

        $user = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($user)->post(route('intranet.academic.sections.store'), [
            'grade_id' => $grade->id,
            'code' => 'Z',
            'name' => 'Sección Z',
            'capacity' => 0,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('capacity');
    }
}
