<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Section>
 */
class SectionFactory extends Factory
{
    protected $model = Section::class;

    public function definition(): array
    {
        return [
            'grade_id' => Grade::factory(),
            'code' => fake()->randomElement(['A', 'B', 'C']),
            'name' => 'Sección '.fake()->randomElement(['A', 'B', 'C']),
            'capacity' => fake()->numberBetween(20, 40),
            'is_active' => true,
        ];
    }
}
