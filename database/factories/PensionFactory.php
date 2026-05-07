<?php

namespace Database\Factories;

use App\Enums\PensionStatus;
use App\Models\Enrollment;
use App\Models\PaymentConcept;
use App\Models\Pension;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pension>
 */
class PensionFactory extends Factory
{
    protected $model = Pension::class;

    public function definition(): array
    {
        $month = fake()->numberBetween(1, 12);
        $year = (int) fake()->year();

        return [
            'enrollment_id' => Enrollment::factory(),
            'payment_concept_id' => PaymentConcept::factory(),
            'month' => $month,
            'year' => $year,
            'amount' => fake()->randomFloat(2, 100, 600),
            'due_date' => sprintf('%04d-%02d-10', $year, $month),
            'status' => PensionStatus::Pendiente->value,
            'observations' => null,
        ];
    }
}
