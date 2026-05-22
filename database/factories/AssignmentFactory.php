<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\User;
use App\Models\VirtualClassroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        return [
            'virtual_classroom_id' => VirtualClassroom::factory(),
            'title' => 'Tarea '.fake()->word(),
            'description' => fake()->sentence(),
            'max_score' => 20,
            'due_at' => now()->addDays(7),
            'rubric' => null,
            'is_published' => true,
            'created_by_user_id' => User::factory(),
        ];
    }
}
