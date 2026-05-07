<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    public function definition(): array
    {
        $year = (int) fake()->unique()->numberBetween(2020, 2040);

        return [
            'name' => 'Año '.$year,
            'year' => $year,
            'starts_at' => $year.'-03-01',
            'ends_at' => $year.'-12-15',
            'is_active' => false,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
