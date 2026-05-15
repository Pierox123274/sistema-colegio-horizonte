<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('CUR-###')),
            'name' => fake()->unique()->randomElement([
                'Matemática',
                'Comunicación',
                'Ciencia y Tecnología',
                'Personal Social',
                'Inglés',
            ]).' '.fake()->unique()->numberBetween(1, 99),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
