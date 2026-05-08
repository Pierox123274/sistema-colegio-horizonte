<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\CashRegister;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'cash_register_id' => CashRegister::factory(),
            'sale_code' => 'VTA-'.now()->format('Ymd').'-'.fake()->unique()->numerify('######'),
            'student_id' => null,
            'guardian_id' => null,
            'payment_method' => fake()->randomElement(PaymentMethod::values()),
            'status' => 'registrada',
            'total' => fake()->randomFloat(2, 5, 300),
            'sold_at' => now(),
            'created_by_user_id' => User::factory(),
            'canceled_by_user_id' => null,
            'canceled_at' => null,
            'observations' => null,
        ];
    }
}
