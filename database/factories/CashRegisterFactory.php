<?php

namespace Database\Factories;

use App\Models\CashRegister;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashRegister>
 */
class CashRegisterFactory extends Factory
{
    protected $model = CashRegister::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'closed_by_user_id' => null,
            'business_date' => now()->toDateString(),
            'status' => 'abierta',
            'opening_balance' => 50,
            'closing_balance' => null,
            'opened_at' => now(),
            'closed_at' => null,
            'opening_notes' => null,
            'closing_notes' => null,
        ];
    }
}
