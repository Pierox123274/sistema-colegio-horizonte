<?php

namespace Database\Factories;

use App\Enums\PaymentConceptType;
use App\Models\PaymentConcept;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentConcept>
 */
class PaymentConceptFactory extends Factory
{
    protected $model = PaymentConcept::class;

    public function definition(): array
    {
        return [
            'code' => 'CON-'.fake()->unique()->numerify('####'),
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'default_amount' => fake()->randomFloat(2, 50, 800),
            'type' => PaymentConceptType::Pension,
            'is_active' => true,
        ];
    }
}
