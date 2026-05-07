<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classroom>
 */
class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'code' => 'AUL-'.fake()->unique()->numerify('###'),
            'name' => 'Aula '.fake()->numerify('###'),
            'floor' => (string) fake()->numberBetween(1, 3),
            'capacity' => fake()->numberBetween(25, 45),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }

    public function withoutSection(): static
    {
        return $this->state(fn (array $attributes) => [
            'section_id' => null,
        ]);
    }
}
