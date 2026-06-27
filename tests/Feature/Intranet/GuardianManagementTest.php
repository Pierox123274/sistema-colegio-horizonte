<?php

namespace Tests\Feature\Intranet;

use App\Enums\GuardianRelationshipType;
use App\Enums\IntranetRole;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use App\Support\SensitiveDataHasher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GuardianManagementTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    /**
     * @return array<string, mixed>
     */
    private function guardianPayload(?string $documentNumber = '45123456'): array
    {
        return [
            'first_name' => 'Carlos',
            'last_name' => 'Ramírez',
            'document_type' => 'dni',
            'document_number' => $documentNumber,
            'relationship_type' => GuardianRelationshipType::Padre->value,
            'phone' => '987654321',
            'secondary_phone' => null,
            'email' => 'carlos@example.test',
            'occupation' => 'Ingeniero',
            'address' => 'Jr. Los Olivos 200',
            'workplace' => 'ACME',
            'is_emergency_contact' => true,
            'students' => [],
        ];
    }

    public function test_administrador_crea_apoderado(): void
    {
        $user = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($user)->post(
            route('intranet.guardians.store'),
            $this->guardianPayload(),
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('guardians', [
            'first_name' => 'Carlos',
            'document_number_hash' => SensitiveDataHasher::hashDocument('45123456'),
        ]);
        $this->assertSame('45123456', Guardian::query()->where('first_name', 'Carlos')->value('document_number'));
    }

    public function test_secretaria_crea_apoderado(): void
    {
        $user = $this->userWithRole(IntranetRole::Secretaria);

        $response = $this->actingAs($user)->post(
            route('intranet.guardians.store'),
            $this->guardianPayload(documentNumber: '45123457'),
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('guardians', [
            'document_number_hash' => SensitiveDataHasher::hashDocument('45123457'),
        ]);
    }

    public function test_docente_solo_visualiza(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $index = $this->actingAs($docente)->get(route('intranet.guardians.index'));
        $index->assertOk();

        $create = $this->actingAs($docente)->get(route('intranet.guardians.create'));
        $create->assertForbidden();

        $store = $this->actingAs($docente)->post(
            route('intranet.guardians.store'),
            $this->guardianPayload(documentNumber: '45123458'),
        );
        $store->assertForbidden();
    }

    public function test_estudiante_no_accede(): void
    {
        $user = $this->userWithRole(IntranetRole::Estudiante);

        $response = $this->actingAs($user)->get(route('intranet.guardians.index'));

        $response->assertForbidden();
    }

    public function test_apoderado_usuario_no_accede_al_crud_administrativo(): void
    {
        $user = $this->userWithRole(IntranetRole::Apoderado);

        $response = $this->actingAs($user)->get(route('intranet.guardians.index'));

        $response->assertForbidden();
    }

    public function test_vinculacion_estudiante_apoderado_funciona(): void
    {
        $student = Student::factory()->create();
        $user = $this->userWithRole(IntranetRole::Secretaria);

        $payload = $this->guardianPayload(documentNumber: '45123459');
        $payload['students'] = [
            [
                'student_id' => $student->id,
                'relationship' => GuardianRelationshipType::Madre->value,
                'is_primary' => true,
                'is_financial_responsible' => false,
                'emergency_priority' => 2,
                'observations' => 'Prueba',
            ],
        ];

        $response = $this->actingAs($user)->post(route('intranet.guardians.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('guardian_student', [
            'student_id' => $student->id,
            'relationship' => GuardianRelationshipType::Madre->value,
        ]);
    }

    public function test_responsable_economico_se_guarda(): void
    {
        $student = Student::factory()->create();
        $user = $this->userWithRole(IntranetRole::Administrador);

        $payload = $this->guardianPayload(documentNumber: '45123460');
        $payload['students'] = [
            [
                'student_id' => $student->id,
                'relationship' => GuardianRelationshipType::Padre->value,
                'is_primary' => false,
                'is_financial_responsible' => true,
                'emergency_priority' => null,
                'observations' => null,
            ],
        ];

        $this->actingAs($user)->post(route('intranet.guardians.store'), $payload);

        $row = DB::table('guardian_student')
            ->where('student_id', $student->id)
            ->where('is_financial_responsible', true)
            ->first();

        $this->assertNotNull($row);
    }

    public function test_validaciones_funcionan(): void
    {
        $user = $this->userWithRole(IntranetRole::Secretaria);

        $response = $this->actingAs($user)->post(route('intranet.guardians.store'), [
            'first_name' => '',
            'last_name' => 'X',
            'document_type' => 'dni',
            'document_number' => null,
            'relationship_type' => 'invalid',
            'phone' => '',
            'email' => 'no-email',
        ]);

        $response->assertSessionHasErrors([
            'first_name',
            'relationship_type',
            'phone',
            'email',
        ]);
    }

    public function test_documento_duplicado_falla(): void
    {
        Guardian::factory()->create(['document_number' => '45123461']);

        $user = $this->userWithRole(IntranetRole::Secretaria);

        $response = $this->actingAs($user)->post(
            route('intranet.guardians.store'),
            $this->guardianPayload(documentNumber: '45123461'),
        );

        $response->assertSessionHasErrors('document_number');
    }
}
