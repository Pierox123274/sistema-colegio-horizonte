<?php

namespace Database\Factories;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryMovement>
 */
class InventoryMovementFactory extends Factory
{
    protected $model = InventoryMovement::class;

    public function definition(): array
    {
        $previous = fake()->randomFloat(2, 0, 100);
        $quantity = fake()->randomFloat(2, 1, 20);
        $type = fake()->randomElement(InventoryMovementType::values());
        $new = match ($type) {
            InventoryMovementType::Salida->value => max(0, $previous - $quantity),
            InventoryMovementType::Ajuste->value => $quantity,
            default => $previous + $quantity,
        };

        return [
            'product_id' => Product::factory(),
            'type' => $type,
            'status' => 'registrado',
            'quantity' => $quantity,
            'previous_stock' => $previous,
            'new_stock' => $new,
            'reason' => fake()->sentence(4),
            'observations' => fake()->optional()->sentence(),
            'created_by_user_id' => User::factory(),
        ];
    }
}
