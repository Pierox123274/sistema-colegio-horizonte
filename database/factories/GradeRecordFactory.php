<?php

namespace Database\Factories;

use App\Models\Evaluation;
use App\Models\GradeRecord;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradeRecord>
 */
class GradeRecordFactory extends Factory
{
    protected $model = GradeRecord::class;

    public function definition(): array
    {
        return [
            'evaluation_id' => Evaluation::factory(),
            'student_id' => Student::factory(),
            'score' => fake()->randomFloat(2, 0, 20),
            'observations' => fake()->optional()->sentence(),
            'recorded_by_user_id' => User::factory(),
        ];
    }
}
