<?php

namespace Database\Factories;

use App\Models\EducationalLevel;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grade>
 */
class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        return [
            'educational_level_id' => EducationalLevel::factory(),
            'code' => 'G-'.fake()->unique()->numerify('###'),
            'name' => fake()->word(),
            'order' => fake()->unique()->numberBetween(1, 9999),
            'is_active' => true,
        ];
    }
}
