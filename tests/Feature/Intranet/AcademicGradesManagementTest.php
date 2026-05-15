<?php

namespace Tests\Feature\Intranet;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Models\AcademicYear;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\GradeRecord;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicGradesManagementTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_administrador_crea_curso(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)->post(route('intranet.academic.subjects.store'), [
            'code' => 'CUR-MAT-01',
            'name' => 'Matemática',
            'description' => 'Curso base',
            'is_active' => true,
        ])->assertRedirect(route('intranet.academic.subjects.index'));

        $this->assertDatabaseHas('subjects', ['code' => 'CUR-MAT-01']);
    }

    public function test_administrador_crea_evaluacion(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $subject = Subject::factory()->create();
        $year = AcademicYear::factory()->create();
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);

        $this->actingAs($admin)->post(route('intranet.academic.evaluations.store'), [
            'subject_id' => $subject->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'title' => 'Examen parcial',
            'period' => 'Bimestre 1',
            'evaluated_at' => now()->toDateString(),
            'max_score' => 20,
            'weight' => 1,
            'is_active' => true,
        ])->assertRedirect(route('intranet.academic.evaluations.index'));

        $this->assertDatabaseHas('evaluations', ['title' => 'Examen parcial']);
    }

    public function test_docente_registra_notas(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);
        [$evaluation, $student] = $this->evaluationWithEnrollment();

        $this->actingAs($docente)->post(route('intranet.academic.grades.records.store'), [
            'evaluation_id' => $evaluation->id,
            'entries' => [[
                'student_id' => $student->id,
                'score' => 16,
                'observations' => 'Buen avance',
            ]],
        ])->assertRedirect(route('intranet.academic.grades.records.index', ['evaluation_id' => $evaluation->id]));

        $this->assertDatabaseHas('grade_records', [
            'evaluation_id' => $evaluation->id,
            'student_id' => $student->id,
            'score' => 16,
        ]);
    }

    public function test_registro_notas_get_responde(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);
        $this->actingAs($docente)->get(route('intranet.academic.grades.records.index'))->assertOk();
    }

    public function test_secretaria_consulta_reportes(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);
        [$evaluation, $student] = $this->evaluationWithEnrollment();
        GradeRecord::factory()->create([
            'evaluation_id' => $evaluation->id,
            'student_id' => $student->id,
        ]);

        $this->actingAs($secretaria)
            ->get(route('intranet.academic.grades.reports.export.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_secretaria_no_puede_registrar_notas(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);
        [$evaluation, $student] = $this->evaluationWithEnrollment();

        $this->actingAs($secretaria)->post(route('intranet.academic.grades.records.store'), [
            'evaluation_id' => $evaluation->id,
            'entries' => [[
                'student_id' => $student->id,
                'score' => 15,
                'observations' => '',
            ]],
        ])->assertForbidden();
    }

    public function test_no_permite_nota_duplicada(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);
        [$evaluation, $student] = $this->evaluationWithEnrollment();
        GradeRecord::factory()->create([
            'evaluation_id' => $evaluation->id,
            'student_id' => $student->id,
            'score' => 10,
            'recorded_by_user_id' => $docente->id,
        ]);

        $this->actingAs($docente)->post(route('intranet.academic.grades.records.store'), [
            'evaluation_id' => $evaluation->id,
            'entries' => [[
                'student_id' => $student->id,
                'score' => 19,
                'observations' => 'Actualizado',
            ]],
        ])->assertRedirect();

        $this->assertSame(1, GradeRecord::query()
            ->where('evaluation_id', $evaluation->id)
            ->where('student_id', $student->id)
            ->count());
    }

    public function test_calcula_promedio(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        [$evaluation, $student] = $this->evaluationWithEnrollment();
        $student2 = Student::factory()->create();
        Enrollment::factory()->create([
            'student_id' => $student2->id,
            'academic_year_id' => $evaluation->academic_year_id,
            'educational_level_id' => $evaluation->educational_level_id,
            'grade_id' => $evaluation->grade_id,
            'section_id' => $evaluation->section_id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);
        GradeRecord::factory()->create(['evaluation_id' => $evaluation->id, 'student_id' => $student->id, 'score' => 14, 'recorded_by_user_id' => $admin->id]);
        GradeRecord::factory()->create(['evaluation_id' => $evaluation->id, 'student_id' => $student2->id, 'score' => 18, 'recorded_by_user_id' => $admin->id]);

        $this->assertSame(16.0, round((float) GradeRecord::query()->where('evaluation_id', $evaluation->id)->avg('score'), 1));
    }

    public function test_export_pdf_responde(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);
        [$evaluation, $student] = $this->evaluationWithEnrollment();
        GradeRecord::factory()->create(['evaluation_id' => $evaluation->id, 'student_id' => $student->id, 'recorded_by_user_id' => $docente->id]);

        $this->actingAs($docente)->get(route('intranet.academic.grades.reports.export.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_csv_responde(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);
        [$evaluation, $student] = $this->evaluationWithEnrollment();
        GradeRecord::factory()->create(['evaluation_id' => $evaluation->id, 'student_id' => $student->id, 'recorded_by_user_id' => $docente->id]);

        $this->actingAs($docente)->get(route('intranet.academic.grades.reports.export.excel'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_historial_academico_index_responde(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);
        $this->actingAs($docente)->get(route('intranet.academic.grades.history.index'))->assertOk();
    }

    public function test_historial_individual_responde(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);
        [, $student] = $this->evaluationWithEnrollment();
        $this->actingAs($docente)->get(route('intranet.academic.grades.students.show', $student->id))->assertOk();
    }

    public function test_reportes_academicos_index_responde(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);
        $this->actingAs($secretaria)->get(route('intranet.academic.grades.reports.index'))->assertOk();
    }

    public function test_estudiante_no_accede(): void
    {
        $estudiante = $this->userWithRole(IntranetRole::Estudiante);
        $this->actingAs($estudiante)->get(route('intranet.academic.grades.records.index'))->assertForbidden();
    }

    public function test_apoderado_no_accede(): void
    {
        $apoderado = $this->userWithRole(IntranetRole::Apoderado);
        $this->actingAs($apoderado)->get(route('intranet.academic.grades.records.index'))->assertForbidden();
    }

    /**
     * @return array{0:Evaluation,1:Student}
     */
    private function evaluationWithEnrollment(): array
    {
        $subject = Subject::factory()->create();
        $year = AcademicYear::factory()->create();
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $admin = User::factory()->create();
        $student = Student::factory()->create();

        Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $evaluation = Evaluation::factory()->create([
            'subject_id' => $subject->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'created_by_user_id' => $admin->id,
        ]);

        return [$evaluation, $student];
    }
}
