<?php

namespace Tests\Feature\Intranet;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AttendanceManagementTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    /**
     * @return array<string,mixed>
     */
    private function batchPayload(Student $student, AcademicYear $year, EducationalLevel $level, Grade $grade, Section $section): array
    {
        return [
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'attendance_date' => now()->toDateString(),
            'entries' => [
                [
                    'student_id' => $student->id,
                    'status' => 'presente',
                    'observation' => 'Puntual',
                ],
            ],
        ];
    }

    public function test_administrador_registra_asistencia(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $year = AcademicYear::factory()->create([
            'is_active' => true,
            'starts_at' => now()->startOfYear()->toDateString(),
            'ends_at' => now()->endOfYear()->toDateString(),
        ]);
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $student = Student::factory()->create();
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $this->actingAs($admin)
            ->post(route('intranet.attendance.store'), $this->batchPayload($student, $year, $level, $grade, $section))
            ->assertRedirect(route('intranet.attendance.index'));

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'section_id' => $section->id,
            'status' => 'presente',
        ]);
    }

    public function test_docente_registra_asistencia(): void
    {
        $teacher = $this->userWithRole(IntranetRole::Docente);
        $year = AcademicYear::factory()->create([
            'is_active' => true,
            'starts_at' => now()->startOfYear()->toDateString(),
            'ends_at' => now()->endOfYear()->toDateString(),
        ]);
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $student = Student::factory()->create();
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $this->actingAs($teacher)
            ->post(route('intranet.attendance.store'), $this->batchPayload($student, $year, $level, $grade, $section))
            ->assertRedirect(route('intranet.attendance.index'));
    }

    public function test_secretaria_consulta(): void
    {
        $secretary = $this->userWithRole(IntranetRole::Secretaria);
        $attendance = Attendance::factory()->create();

        $this->actingAs($secretary)
            ->get(route('intranet.attendance.index'))
            ->assertOk();

        $this->actingAs($secretary)
            ->get(route('intranet.attendance.students.show', $attendance->student_id))
            ->assertOk();

        $this->actingAs($secretary)
            ->post(route('intranet.attendance.store'), [
                'academic_year_id' => $attendance->academic_year_id,
                'educational_level_id' => $attendance->educational_level_id,
                'grade_id' => $attendance->grade_id,
                'section_id' => $attendance->section_id,
                'attendance_date' => now()->toDateString(),
                'entries' => [[
                    'student_id' => $attendance->student_id,
                    'status' => 'presente',
                ]],
            ])
            ->assertForbidden();
    }

    public function test_estudiante_no_accede(): void
    {
        $studentRole = $this->userWithRole(IntranetRole::Estudiante);
        $this->actingAs($studentRole)->get(route('intranet.attendance.index'))->assertForbidden();
    }

    public function test_apoderado_no_accede(): void
    {
        $guardianRole = $this->userWithRole(IntranetRole::Apoderado);
        $this->actingAs($guardianRole)->get(route('intranet.attendance.index'))->assertForbidden();
    }

    public function test_no_permite_duplicar_asistencia(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $year = AcademicYear::factory()->create([
            'is_active' => true,
            'starts_at' => now()->startOfYear()->toDateString(),
            'ends_at' => now()->endOfYear()->toDateString(),
        ]);
        $attendance = Attendance::factory()->create([
            'attendance_date' => now()->toDateString(),
            'status' => 'presente',
            'academic_year_id' => $year->id,
        ]);

        Enrollment::factory()->create([
            'student_id' => $attendance->student_id,
            'academic_year_id' => $attendance->academic_year_id,
            'educational_level_id' => $attendance->educational_level_id,
            'grade_id' => $attendance->grade_id,
            'section_id' => $attendance->section_id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $this->actingAs($admin)->post(route('intranet.attendance.store'), [
            'academic_year_id' => $attendance->academic_year_id,
            'educational_level_id' => $attendance->educational_level_id,
            'grade_id' => $attendance->grade_id,
            'section_id' => $attendance->section_id,
            'attendance_date' => now()->toDateString(),
            'entries' => [[
                'student_id' => $attendance->student_id,
                'status' => 'falta',
                'observation' => 'Actualizado',
            ]],
        ])->assertRedirect(route('intranet.attendance.index'));

        $this->assertSame(1, Attendance::query()
            ->where('student_id', $attendance->student_id)
            ->where('section_id', $attendance->section_id)
            ->whereDate('attendance_date', now()->toDateString())
            ->count());

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'falta',
        ]);
    }

    public function test_export_pdf_responde(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        Attendance::factory()->create();

        $this->actingAs($admin)
            ->get(route('intranet.attendance.reports.export.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_excel_csv_responde(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        Attendance::factory()->create();

        $this->actingAs($admin)
            ->get(route('intranet.attendance.reports.export.excel'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_docente_y_secretaria_pueden_descargar_reportes(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);
        Attendance::factory()->create();

        $this->actingAs($docente)
            ->get(route('intranet.attendance.reports.export.pdf'))
            ->assertOk();
        $this->actingAs($docente)
            ->get(route('intranet.attendance.reports.export.excel'))
            ->assertOk();

        $this->actingAs($secretaria)
            ->get(route('intranet.attendance.reports.export.pdf'))
            ->assertOk();
        $this->actingAs($secretaria)
            ->get(route('intranet.attendance.reports.export.excel'))
            ->assertOk();
    }

    public function test_estudiante_y_apoderado_no_descargan_reportes(): void
    {
        $estudiante = $this->userWithRole(IntranetRole::Estudiante);
        $apoderado = $this->userWithRole(IntranetRole::Apoderado);

        $this->actingAs($estudiante)
            ->get(route('intranet.attendance.reports.export.pdf'))
            ->assertForbidden();
        $this->actingAs($estudiante)
            ->get(route('intranet.attendance.reports.export.excel'))
            ->assertForbidden();

        $this->actingAs($apoderado)
            ->get(route('intranet.attendance.reports.export.pdf'))
            ->assertForbidden();
        $this->actingAs($apoderado)
            ->get(route('intranet.attendance.reports.export.excel'))
            ->assertForbidden();
    }

    public function test_asistencia_carga_estudiantes_matriculados_en_seccion(): void
    {
        $teacher = $this->userWithRole(IntranetRole::Docente);
        $year = AcademicYear::factory()->create(['is_active' => true]);
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $student = Student::factory()->create();

        Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $this->actingAs($teacher)
            ->get(route('intranet.attendance.section-date', ['date' => now()->toDateString(), 'section' => $section->id, 'academic_year_id' => $year->id]))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Attendance/Create')
                ->has('batch.students', 1));
    }

    public function test_no_carga_estudiantes_sin_matricula_activa(): void
    {
        $teacher = $this->userWithRole(IntranetRole::Docente);
        $year = AcademicYear::factory()->create(['is_active' => true]);
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $student = Student::factory()->create();

        Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Anulado->value,
        ]);

        $this->actingAs($teacher)
            ->get(route('intranet.attendance.section-date', ['date' => now()->toDateString(), 'section' => $section->id, 'academic_year_id' => $year->id]))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Attendance/Create')
                ->has('batch.students', 0));
    }

    public function test_route_section_date_funciona_correctamente(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $year = AcademicYear::factory()->create(['is_active' => true]);
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);

        $this->actingAs($admin)
            ->get(route('intranet.attendance.section-date', ['date' => now()->toDateString(), 'section' => $section->id, 'academic_year_id' => $year->id]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Intranet/Attendance/Create'));
    }

    public function test_rutas_historial_y_reportes_no_fallan(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)
            ->get(route('intranet.attendance.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Intranet/Attendance/Index'));

        $this->actingAs($admin)
            ->get(route('intranet.attendance.reports.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Intranet/Attendance/Reports'));
    }
}
