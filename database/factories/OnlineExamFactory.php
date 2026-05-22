<?php

namespace Database\Factories;

use App\Enums\OnlineExamGradingMode;
use App\Models\OnlineExam;
use App\Models\User;
use App\Models\VirtualClassroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OnlineExam>
 */
class OnlineExamFactory extends Factory
{
    protected $model = OnlineExam::class;

    public function definition(): array
    {
        return [
            'virtual_classroom_id' => VirtualClassroom::factory(),
            'title' => 'Evaluación '.fake()->word(),
            'description' => fake()->optional()->sentence(),
            'grading_mode' => OnlineExamGradingMode::Automatic,
            'time_limit_minutes' => 30,
            'max_attempts' => 1,
            'shuffle_questions' => false,
            'show_results_after' => true,
            'is_published' => true,
            'available_from' => now(),
            'available_until' => now()->addWeek(),
            'created_by_user_id' => User::factory(),
        ];
    }
}
