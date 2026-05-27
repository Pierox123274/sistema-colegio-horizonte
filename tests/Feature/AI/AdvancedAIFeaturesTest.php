<?php

namespace Tests\Feature\AI;

use App\Enums\IntranetRole;
use App\Models\AuditLog;
use App\Models\QuestionBank;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Models\VirtualClassroom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdvancedAIFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private function teacher(): User
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Docente->value]);

        return $user;
    }

    private function studentUser(): User
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Estudiante->value]);
        Student::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    public function test_teacher_can_access_ai_copilot_hub(): void
    {
        $teacher = $this->teacher();

        $this->actingAs($teacher)
            ->get(route('teacher.ai-copilot.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Teacher/AICopilot/Index'));
    }

    public function test_secretaria_cannot_access_teacher_copilot(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Secretaria->value]);

        $this->actingAs($user)
            ->get(route('teacher.ai-copilot.exams'))
            ->assertForbidden();
    }

    public function test_exam_generation_uses_local_fallback_when_ai_disabled(): void
    {
        Config::set('ai.tutor_enabled', false);

        $teacher = $this->teacher();

        $this->actingAs($teacher)
            ->postJson(route('teacher.ai-copilot.exams.generate'), [
                'topic' => 'Fracciones',
                'question_count' => 2,
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('fallback', true)
            ->assertJsonStructure(['data' => ['questions']]);
    }

    public function test_exam_generation_with_openai_and_cache(): void
    {
        Config::set('ai.tutor_enabled', true);
        Config::set('ai.provider', 'openai');
        Config::set('ai.openai.api_key', 'sk-test');
        Cache::flush();

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => '{"questions":[{"stem":"P1","type":"multiple_choice","difficulty":"intermediate","options":[{"label":"A","body":"x","is_correct":true}],"explanation":"ok"}]}']],
                ],
                'model' => 'gpt-4o-mini',
            ], 200),
        ]);

        $teacher = $this->teacher();

        $this->actingAs($teacher)
            ->postJson(route('teacher.ai-copilot.exams.generate'), [
                'topic' => 'Álgebra',
                'question_count' => 1,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        Http::assertSentCount(1);

        $this->actingAs($teacher)
            ->postJson(route('teacher.ai-copilot.exams.generate'), [
                'topic' => 'Álgebra',
                'question_count' => 1,
            ])
            ->assertOk()
            ->assertJsonPath('cached', true);

        Http::assertSentCount(1);
    }

    public function test_export_questions_to_bank(): void
    {
        $teacher = $this->teacher();
        $subject = Subject::factory()->create();

        $this->actingAs($teacher)
            ->postJson(route('teacher.ai-copilot.exams.export'), [
                'subject_id' => $subject->id,
                'questions' => [
                    [
                        'stem' => '¿2+2?',
                        'type' => 'multiple_choice',
                        'difficulty' => 'intermediate',
                        'options' => [
                            ['label' => 'A', 'body' => '4', 'is_correct' => true],
                            ['label' => 'B', 'body' => '5', 'is_correct' => false],
                        ],
                        'explanation' => 'Suma básica',
                    ],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('created', 1);

        $this->assertSame(1, QuestionBank::query()->where('subject_id', $subject->id)->count());
    }

    public function test_assignment_generation_fallback(): void
    {
        Config::set('ai.tutor_enabled', false);
        $teacher = $this->teacher();

        $this->actingAs($teacher)
            ->postJson(route('teacher.ai-copilot.assignments.generate'), [
                'topic' => 'Ecosistemas',
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Tarea: Ecosistemas');
    }

    public function test_student_coach_summary_fallback(): void
    {
        Config::set('ai.tutor_enabled', false);
        $user = $this->studentUser();

        $this->actingAs($user)
            ->postJson(route('student.ai-tutor.summary'), ['topic' => 'Historia'])
            ->assertOk()
            ->assertJsonStructure(['data' => ['summary_points']]);
    }

    public function test_predictive_insights_for_teacher(): void
    {
        $teacher = $this->teacher();

        $this->actingAs($teacher)
            ->getJson(route('teacher.ai-copilot.predictive'))
            ->assertOk()
            ->assertJsonStructure([
                'students_visible',
                'high_risk',
                'suggested_interventions',
            ]);
    }

    public function test_intranet_ai_analytics_includes_usage(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles([IntranetRole::Administrador->value]);

        $this->actingAs($admin)
            ->get(route('intranet.ai-analytics.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Intranet/AIAnalytics/Index')
                ->has('usage')
                ->has('modules'));
    }

    public function test_generation_logs_audit(): void
    {
        Config::set('ai.tutor_enabled', true);
        Config::set('ai.openai.api_key', 'sk-test');
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => '{"title":"Rúbrica","criteria":[]}']],
                ],
                'model' => 'gpt-4o-mini',
            ], 200),
        ]);

        $teacher = $this->teacher();
        $before = AuditLog::query()->count();

        $this->actingAs($teacher)
            ->postJson(route('teacher.ai-copilot.rubrics.generate'), [
                'title' => 'Ensayo',
            ]);

        $this->assertGreaterThan($before, AuditLog::query()->count());
    }

    public function test_export_assignment_to_classroom(): void
    {
        $teacher = $this->teacher();
        $classroom = VirtualClassroom::factory()->create([
            'teacher_user_id' => $teacher->id,
            'created_by_user_id' => $teacher->id,
        ]);

        $this->actingAs($teacher)
            ->postJson(route('teacher.ai-copilot.assignments.export'), [
                'virtual_classroom_id' => $classroom->id,
                'payload' => [
                    'title' => 'Tarea IA',
                    'instructions' => 'Describa el tema.',
                ],
            ])
            ->assertOk()
            ->assertJsonStructure(['assignment_id', 'redirect']);
    }
}
