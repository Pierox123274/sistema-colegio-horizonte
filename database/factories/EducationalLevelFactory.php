<?php

namespace Database\Factories;

use App\Models\EducationalLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EducationalLevel>
 */
class EducationalLevelFactory extends Factory
{
    protected $model = EducationalLevel::class;

    public function definition(): array
    {
        $code = fake()->unique()->lexify('LVL-???');

        return [
            'code' => strtoupper($code),
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
