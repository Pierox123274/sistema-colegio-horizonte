<?php

namespace Database\Seeders;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $uniformes = ProductCategory::query()->firstOrCreate(
            ['code' => 'CAT-UNIFORME'],
            [
                'name' => 'Uniformes',
                'description' => 'Prendas institucionales',
                'is_active' => true,
            ]
        );

        $utiles = ProductCategory::query()->firstOrCreate(
            ['code' => 'CAT-UTILES'],
            [
                'name' => 'Útiles',
                'description' => 'Material escolar y oficina',
                'is_active' => true,
            ]
        );

        $products = [
            [
                'category_id' => $uniformes->id,
                'code' => 'PRD-POLO-H',
                'name' => 'Polo institucional',
                'unit' => 'unidad',
                'purchase' => 18,
                'sale' => 35,
                'stock' => 40,
                'min' => 10,
            ],
            [
                'category_id' => $uniformes->id,
                'code' => 'PRD-BUZO-H',
                'name' => 'Buzo institucional',
                'unit' => 'unidad',
                'purchase' => 42,
                'sale' => 75,
                'stock' => 15,
                'min' => 8,
            ],
            [
                'category_id' => $utiles->id,
                'code' => 'PRD-CUAD-A4',
                'name' => 'Cuaderno A4',
                'unit' => 'unidad',
                'purchase' => 4.5,
                'sale' => 7.5,
                'stock' => 120,
                'min' => 25,
            ],
        ];

        $admin = User::query()->first();

        foreach ($products as $row) {
            $product = Product::query()->firstOrCreate(
                ['code' => $row['code']],
                [
                    'product_category_id' => $row['category_id'],
                    'name' => $row['name'],
                    'description' => null,
                    'unit' => $row['unit'],
                    'purchase_price' => $row['purchase'],
                    'sale_price' => $row['sale'],
                    'current_stock' => 0,
                    'minimum_stock' => $row['min'],
                    'is_active' => true,
                ]
            );

            if ((float) $product->current_stock > 0) {
                continue;
            }

            InventoryMovement::query()->create([
                'product_id' => $product->id,
                'type' => InventoryMovementType::Entrada->value,
                'quantity' => $row['stock'],
                'previous_stock' => 0,
                'new_stock' => $row['stock'],
                'reason' => 'Carga inicial demo',
                'observations' => null,
                'created_by_user_id' => $admin?->id,
            ]);

            $product->update(['current_stock' => $row['stock']]);
        }
    }
}
