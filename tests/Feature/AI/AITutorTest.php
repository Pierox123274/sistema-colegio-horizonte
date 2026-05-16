<?php

namespace Tests\Feature\AI;

use App\Enums\IntranetRole;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AITutorTest extends TestCase
{
    use RefreshDatabase;

    private function studentUser(): User
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Estudiante->value]);
        Student::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_student_can_access_ai_tutor_page(): void
    {
        $user = $this->studentUser();

        $this->actingAs($user)
            ->get(route('student.ai-tutor.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Student/AITutor'));
    }

    public function test_teacher_can_access_ai_insights(): void
    {
        $teacher = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($teacher)
            ->get(route('teacher.ai-insights.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/AIInsights'));
    }

    public function test_admin_can_access_intranet_ai_analytics(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)
            ->get(route('intranet.ai-analytics.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Intranet/AIAnalytics/Index'));
    }

    public function test_secretaria_cannot_access_intranet_ai_analytics(): void
    {
        $u = $this->userWithRole(IntranetRole::Secretaria);

        $this->actingAs($u)
            ->get(route('intranet.ai-analytics.index'))
            ->assertForbidden();
    }

    public function test_student_chat_calls_openai_and_logs_audit(): void
    {
        Config::set('ai.tutor_enabled', true);
        Config::set('ai.provider', 'openai');
        Config::set('ai.openai.api_key', 'sk-test-secret');

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Respuesta de prueba del tutor.']],
                ],
                'model' => 'gpt-4o-mini',
            ], 200),
        ]);

        $user = $this->studentUser();

        $this->actingAs($user)
            ->post(route('student.ai-tutor.message'), ['message' => '¿Cómo me va en el colegio?'])
            ->assertRedirect();

        Http::assertSentCount(1);

        $this->assertGreaterThan(0, AuditLog::query()->count());
    }

    public function test_student_chat_graceful_when_openai_errors(): void
    {
        Config::set('ai.tutor_enabled', true);
        Config::set('ai.openai.api_key', 'sk-test');

        Http::fake([
            'api.openai.com/*' => Http::response(['error' => 'bad'], 502),
        ]);

        $user = $this->studentUser();

        $this->actingAs($user)
            ->post(route('student.ai-tutor.message'), ['message' => 'Hola'])
            ->assertRedirect();
    }

    public function test_student_chat_returns_json_payload_when_expected(): void
    {
        Config::set('ai.tutor_enabled', true);
        Config::set('ai.provider', 'openai');
        Config::set('ai.openai.api_key', 'sk-test-secret');

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Respuesta JSON del tutor.']],
                ],
                'model' => 'gpt-4o-mini',
            ], 200),
        ]);

        $user = $this->studentUser();

        $this->actingAs($user)
            ->postJson(route('student.ai-tutor.message'), ['message' => 'Hola desde JSON'])
            ->assertOk()
            ->assertJsonStructure([
                'reply',
                'success',
                'cached',
                'model',
                'error_code',
            ])
            ->assertJson([
                'success' => true,
                'cached' => false,
            ]);
    }
}
