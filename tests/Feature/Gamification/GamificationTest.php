<?php

namespace Tests\Feature\Gamification;

use App\Enums\ExperienceSource;
use App\Enums\IntranetRole;
use App\Models\Challenge;
use App\Models\Student;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GamificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_gamification_page(): void
    {
        $studentUser = User::factory()->create();
        $studentUser->syncRoles([IntranetRole::Estudiante->value]);
        Student::factory()->create(['user_id' => $studentUser->id]);

        $this->actingAs($studentUser)
            ->get(route('student.gamification.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Student/Gamification/Index')
                ->has('summary.profile.total_xp'));
    }

    public function test_xp_and_level_progression_is_calculated(): void
    {
        $student = Student::factory()->create();
        $service = app(GamificationService::class);

        $service->awardXp($student, ExperienceSource::AssignmentCompleted, 200, 'xp 1');
        $service->awardXp($student, ExperienceSource::ExamApproved, 250, 'xp 2');

        $summary = $service->studentSummary($student);

        $this->assertGreaterThanOrEqual(450, $summary['profile']['total_xp']);
        $this->assertGreaterThanOrEqual(2, $summary['profile']['current_level']);
    }

    public function test_challenge_progress_and_completion_grants_xp(): void
    {
        $student = Student::factory()->create();
        $service = app(GamificationService::class);

        $challenge = Challenge::query()->firstOrCreate(
            ['code' => 'test_task_challenge'],
            [
                'type' => 'task_completion',
                'title' => 'Reto test',
                'description' => 'Completa 2 tareas',
                'target_value' => 2,
                'xp_reward' => 100,
                'is_active' => true,
            ]
        );

        $service->awardXp($student, ExperienceSource::AssignmentCompleted, 50, 'tarea 1');
        $service->awardXp($student, ExperienceSource::AssignmentCompleted, 50, 'tarea 2');

        $this->assertDatabaseHas('student_challenges', [
            'student_id' => $student->id,
            'challenge_id' => $challenge->id,
            'status' => 'completed',
        ]);
    }

    public function test_permissions_for_gamification_routes(): void
    {
        $teacher = User::factory()->create();
        $teacher->syncRoles([IntranetRole::Docente->value]);

        $this->actingAs($teacher)
            ->get(route('student.gamification.index'))
            ->assertForbidden();

        $admin = User::factory()->create();
        $admin->syncRoles([IntranetRole::Administrador->value]);

        $this->actingAs($admin)
            ->get(route('intranet.gamification.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Gamification/Index')
                ->has('overview.students_with_profile'));
    }
}
