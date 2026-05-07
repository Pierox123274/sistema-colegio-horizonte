<?php

namespace Database\Factories;

use App\Enums\PaymentEntryStatus;
use App\Enums\PaymentMethod;
use App\Models\Payment;
use App\Models\PaymentConcept;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'payment_code' => 'PAY-TST-'.Str::upper(Str::random(8)),
            'student_id' => Student::factory(),
            'guardian_id' => null,
            'enrollment_id' => null,
            'pension_id' => null,
            'payment_concept_id' => PaymentConcept::factory(),
            'amount' => fake()->randomFloat(2, 10, 500),
            'payment_method' => PaymentMethod::Efectivo->value,
            'paid_at' => now(),
            'status' => PaymentEntryStatus::Registrado->value,
            'observations' => null,
        ];
    }
}
