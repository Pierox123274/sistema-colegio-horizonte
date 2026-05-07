<?php

namespace Tests\Feature\Intranet;

use App\Enums\GuardianRelationshipType;
use App\Enums\IntranetRole;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validStudentPayload(string $code = 'EST-100001', ?string $documentNumber = '40123456'): array
    {
        return [
            'code' => $code,
            'first_name' => 'Ana',
            'last_name' => 'Pérez',
            'document_type' => 'dni',
            'document_number' => $documentNumber,
            'birth_date' => '2015-06-01',
            'gender' => 'femenino',
            'educational_level' => 'primaria',
            'grade' => '3.º',
            'section' => 'A',
            'status' => 'activo',
            'address' => 'Av. Los Rosales 123',
            'phone' => '999888777',
            'email' => 'ana.perez@example.test',
            'medical_observations' => null,
        ];
    }

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_administrador_puede_listar_estudiantes(): void
    {
        Student::factory()->count(2)->create();

        $user = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($user)->get(route('intranet.students.index'));

        $response->assertOk();
        $response->assertSee('Intranet/Students/Index', false);
    }

    public function test_secretaria_puede_crear_estudiante(): void
    {
        $user = $this->userWithRole(IntranetRole::Secretaria);

        $response = $this->actingAs($user)->post(
            route('intranet.students.store'),
            $this->validStudentPayload(),
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('students', ['code' => 'EST-100001']);
    }

    public function test_docente_puede_ver_estudiantes_pero_no_crear(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $index = $this->actingAs($docente)->get(route('intranet.students.index'));
        $index->assertOk();

        $create = $this->actingAs($docente)->get(route('intranet.students.create'));
        $create->assertForbidden();

        $store = $this->actingAs($docente)->post(
            route('intranet.students.store'),
            $this->validStudentPayload(code: 'EST-DOC-POST', documentNumber: '49999901'),
        );
        $store->assertForbidden();
    }

    public function test_estudiante_no_puede_acceder_a_gestion_de_estudiantes(): void
    {
        $user = $this->userWithRole(IntranetRole::Estudiante);

        $response = $this->actingAs($user)->get(route('intranet.students.index'));

        $response->assertForbidden();
    }

    public function test_apoderado_no_puede_acceder_a_gestion_de_estudiantes(): void
    {
        $user = $this->userWithRole(IntranetRole::Apoderado);

        $response = $this->actingAs($user)->get(route('intranet.students.index'));

        $response->assertForbidden();
    }

    public function test_validacion_falla_con_datos_invalidos(): void
    {
        $user = $this->userWithRole(IntranetRole::Secretaria);

        $response = $this->actingAs($user)->post(route('intranet.students.store'), [
            'code' => '',
            'first_name' => '',
            'last_name' => 'X',
            'document_type' => 'dni',
            'document_number' => null,
            'birth_date' => 'not-a-date',
            'gender' => 'masculino',
            'educational_level' => 'primaria',
            'grade' => '99.º',
            'section' => '',
            'status' => 'activo',
        ]);

        $response->assertSessionHasErrors(['code', 'first_name', 'birth_date', 'grade']);
    }

    public function test_no_permite_codigo_duplicado(): void
    {
        Student::factory()->create(['code' => 'EST-DUP-1']);

        $user = $this->userWithRole(IntranetRole::Secretaria);

        $payload = $this->validStudentPayload(code: 'EST-DUP-1', documentNumber: '41111111');

        $response = $this->actingAs($user)->post(route('intranet.students.store'), $payload);

        $response->assertSessionHasErrors('code');
    }

    public function test_no_permite_documento_duplicado(): void
    {
        Student::factory()->create(['document_number' => '42222222']);

        $user = $this->userWithRole(IntranetRole::Secretaria);

        $payload = $this->validStudentPayload(code: 'EST-NEW-1', documentNumber: '42222222');

        $response = $this->actingAs($user)->post(route('intranet.students.store'), $payload);

        $response->assertSessionHasErrors('document_number');
    }

    public function test_puede_actualizar_estudiante(): void
    {
        $student = Student::factory()->create([
            'first_name' => 'Luis',
            'code' => 'EST-UPD-1',
        ]);

        $user = $this->userWithRole(IntranetRole::Secretaria);

        $payload = $this->validStudentPayload(code: 'EST-UPD-1', documentNumber: '43333333');
        $payload['first_name'] = 'Luis Alberto';

        $response = $this->actingAs($user)->put(
            route('intranet.students.update', $student),
            $payload,
        );

        $response->assertRedirect(route('intranet.students.show', $student, absolute: false));
        $this->assertSame(
            'Luis Alberto',
            $student->fresh()->first_name,
        );
    }

    public function test_puede_ver_detalle(): void
    {
        $student = Student::factory()->create();

        $user = $this->userWithRole(IntranetRole::Docente);

        $response = $this->actingAs($user)->get(route('intranet.students.show', $student));

        $response->assertOk();
        $response->assertSee('Intranet/Students/Show', false);
    }

    public function test_detalle_estudiante_incluye_apoderados_vinculados(): void
    {
        $student = Student::factory()->create();
        $guardian = Guardian::factory()->create([
            'first_name' => 'María',
            'last_name' => 'DetalleApoderado',
            'phone' => '987111222',
        ]);
        $guardian->students()->attach($student->id, [
            'relationship' => GuardianRelationshipType::Madre->value,
            'is_primary' => true,
            'is_financial_responsible' => true,
            'emergency_priority' => 1,
            'observations' => 'Nota pivote test',
        ]);

        $user = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($user)->get(route('intranet.students.show', $student));

        $response->assertOk();

        $props = $this->inertiaPropsFromResponse($response);
        $this->assertArrayHasKey('guardian_links', $props);
        $this->assertCount(1, $props['guardian_links']);
        $link = $props['guardian_links'][0];
        $this->assertSame('María DetalleApoderado', $link['full_name']);
        $this->assertSame('madre', $link['relationship']);
        $this->assertSame('987111222', $link['phone']);
        $this->assertTrue($link['is_primary']);
        $this->assertTrue($link['is_financial_responsible']);
        $this->assertSame(1, $link['emergency_priority']);
        $this->assertSame('Nota pivote test', $link['observations']);
    }

    /**
     * @return array<string, mixed>
     */
    private function inertiaPropsFromResponse(TestResponse $response): array
    {
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertMatchesRegularExpression('/<div[^>]*id="app"[^>]*data-page="/', $content);

        if (! preg_match('/<div[^>]*id="app"[^>]*data-page="([^"]+)"/', $content, $matches)) {
            $this->fail('No se encontró data-page en la respuesta Inertia.');
        }

        $jsonString = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        /** @var array{props?: array<string, mixed>} $payload */
        $payload = json_decode($jsonString, true, flags: JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('props', $payload);

        return $payload['props'];
    }
}
