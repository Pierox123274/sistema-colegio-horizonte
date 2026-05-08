<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ProductCategoryService
{
    public function paginateForIndex(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = ProductCategory::query()
            ->withCount('products')
            ->orderBy('code');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('code', 'like', $like)
                    ->orWhere('name', 'like', $like);
            });
        }

        if ($request->query('is_active') !== null && $request->query('is_active') !== '') {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ProductCategory
    {
        return ProductCategory::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ProductCategory $category, array $data): ProductCategory
    {
        $category->update($data);

        return $category->fresh();
    }

    public function deactivate(ProductCategory $category): ProductCategory
    {
        $category->update(['is_active' => false]);

        Product::query()
            ->where('product_category_id', $category->id)
            ->update(['is_active' => false]);

        return $category->fresh();
    }
}
