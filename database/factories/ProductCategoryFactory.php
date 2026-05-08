<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        $code = 'CAT-'.fake()->unique()->bothify('##??');

        return [
            'code' => strtoupper($code),
            'name' => 'Categoría '.fake()->unique()->word(),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
