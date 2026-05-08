<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'product_category_id' => ProductCategory::factory(),
            'code' => 'PRD-'.strtoupper(fake()->unique()->bothify('###??')),
            'name' => 'Producto '.fake()->unique()->word(),
            'description' => fake()->optional()->sentence(),
            'product_type' => fake()->randomElement(['uniforme', 'buzo', 'libro', 'agenda', 'util', 'otro']),
            'size' => fake()->randomElement(['4', '6', '8', '10', '12', '14', 'S', 'M', 'L', 'XL', 'unico']),
            'color' => fake()->optional()->safeColorName(),
            'gender_target' => fake()->randomElement(['varon', 'mujer', 'unisex', 'no_aplica']),
            'unit' => fake()->randomElement(['unidad', 'paquete', 'caja']),
            'purchase_price' => fake()->randomFloat(2, 0, 150),
            'sale_price' => fake()->randomFloat(2, 0, 220),
            'current_stock' => fake()->randomFloat(2, 0, 120),
            'minimum_stock' => fake()->randomFloat(2, 0, 20),
            'is_active' => true,
        ];
    }
}
