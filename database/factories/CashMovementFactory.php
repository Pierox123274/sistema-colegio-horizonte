<?php

namespace Database\Factories;

use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashMovement>
 */
class CashMovementFactory extends Factory
{
    protected $model = CashMovement::class;

    public function definition(): array
    {
        return [
            'cash_register_id' => CashRegister::factory(),
            'sale_id' => null,
            'type' => fake()->randomElement(['apertura', 'venta', 'anulacion_venta', 'cierre']),
            'amount' => fake()->randomFloat(2, -80, 200),
            'description' => fake()->sentence(),
            'moved_at' => now(),
            'created_by_user_id' => User::factory(),
        ];
    }
}
