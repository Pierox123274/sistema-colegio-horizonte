<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ProductService
{
    public function paginateForIndex(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['category:id,code,name'])
            ->withCount('inventoryMovements')
            ->orderBy('code');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('code', 'like', $like)
                    ->orWhere('name', 'like', $like);
            });
        }

        if ($category = $request->query('category_id')) {
            $query->where('product_category_id', (int) $category);
        }

        if ($type = $request->query('product_type')) {
            $query->where('product_type', $type);
        }

        if ($size = $request->query('size')) {
            $query->where('size', $size);
        }

        if ($request->query('is_active') !== null && $request->query('is_active') !== '') {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        if (filter_var($request->query('low_stock', false), FILTER_VALIDATE_BOOLEAN)) {
            $query->whereColumn('current_stock', '<=', 'minimum_stock');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public function categoryOptions(): array
    {
        return ProductCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name'])
            ->map(fn (ProductCategory $category): array => [
                'value' => (string) $category->id,
                'label' => $category->code.' - '.$category->name,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function productTypeOptions(): array
    {
        return [
            ['value' => 'uniforme', 'label' => 'Uniforme'],
            ['value' => 'buzo', 'label' => 'Buzo'],
            ['value' => 'polo', 'label' => 'Polo'],
            ['value' => 'casaca', 'label' => 'Casaca'],
            ['value' => 'pantalon', 'label' => 'Pantalón'],
            ['value' => 'agenda', 'label' => 'Agenda'],
            ['value' => 'libro', 'label' => 'Libro'],
            ['value' => 'util', 'label' => 'Útil'],
            ['value' => 'otro', 'label' => 'Otro'],
        ];
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function sizeOptions(): array
    {
        return [
            ['value' => '4', 'label' => '4'],
            ['value' => '6', 'label' => '6'],
            ['value' => '8', 'label' => '8'],
            ['value' => '10', 'label' => '10'],
            ['value' => '12', 'label' => '12'],
            ['value' => '14', 'label' => '14'],
            ['value' => 'S', 'label' => 'S'],
            ['value' => 'M', 'label' => 'M'],
            ['value' => 'L', 'label' => 'L'],
            ['value' => 'XL', 'label' => 'XL'],
            ['value' => 'unico', 'label' => 'Único'],
            ['value' => 'no_aplica', 'label' => 'No aplica'],
        ];
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function genderTargetOptions(): array
    {
        return [
            ['value' => 'varon', 'label' => 'Varón'],
            ['value' => 'mujer', 'label' => 'Mujer'],
            ['value' => 'unisex', 'label' => 'Unisex'],
            ['value' => 'no_aplica', 'label' => 'No aplica'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        return Product::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh();
    }

    public function deactivate(Product $product): Product
    {
        $product->update(['is_active' => false]);

        return $product->fresh();
    }
}
