<?php

namespace Tests\Unit\AI;

use App\Models\Student;
use App\Services\LocalTutorFallbackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalTutorFallbackServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_greeting_returns_local_fallback_payload(): void
    {
        $student = Student::factory()->create();
        $service = app(LocalTutorFallbackService::class);

        $payload = $service->studentChat($student, 'hola');

        $this->assertTrue($payload['success']);
        $this->assertTrue($payload['fallback']);
        $this->assertSame('local-fallback', $payload['model']);
        $this->assertStringContainsString('tutor académico', $payload['reply']);
    }

    public function test_study_topic_includes_recommendations(): void
    {
        $student = Student::factory()->create();
        $service = app(LocalTutorFallbackService::class);

        $payload = $service->studentChat($student, 'necesito ayuda con mi examen de matemáticas');

        $this->assertTrue($payload['success']);
        $this->assertStringContainsString('repaso', mb_strtolower($payload['reply']));
    }
}
