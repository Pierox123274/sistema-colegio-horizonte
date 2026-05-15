<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class InventoryAnalyticsService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function summary(array $filters): array
    {
        $today = now()->toDateString();

        $salesToday = Sale::query()
            ->where('status', 'registrada')
            ->whereDate('sold_at', $today)
            ->count();

        $salesIncomeToday = (float) Sale::query()
            ->where('status', 'registrada')
            ->whereDate('sold_at', $today)
            ->sum('total');

        $periodIncome = (float) $this->salesQuery($filters)->sum('total');

        return [
            'low_stock_count' => $this->lowStockQuery()->count(),
            'active_products' => Product::query()->where('is_active', true)->count(),
            'sales_today' => $salesToday,
            'sales_income_today' => round($salesIncomeToday, 2),
            'period_sales_income' => round($periodIncome, 2),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function lowStockProducts(int $limit = 8): array
    {
        return $this->lowStockQuery()
            ->orderBy('current_stock')
            ->limit($limit)
            ->get(['id', 'name', 'code', 'current_stock', 'minimum_stock'])
            ->map(fn (Product $p): array => [
                'name' => $p->name,
                'code' => $p->code,
                'current_stock' => (float) $p->current_stock,
                'minimum_stock' => (float) $p->minimum_stock,
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return list<array{label: string, value: float}>
     */
    public function topProducts(array $filters, int $limit = 6): array
    {
        $query = SaleItem::query()
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->where('sales.status', 'registrada')
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->limit($limit);

        if (! empty($filters['date_from'])) {
            $query->whereDate('sales.sold_at', '>=', $filters['date_from']);
        } else {
            $query->whereDate('sales.sold_at', '>=', now()->subDays(30)->toDateString());
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('sales.sold_at', '<=', $filters['date_to']);
        }

        return $query->get()->map(fn ($row): array => [
            'label' => (string) $row->name,
            'value' => (float) $row->qty,
        ])->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return list<array{label: string, value: float}>
     */
    public function salesTrend(array $filters): array
    {
        return $this->salesQuery($filters)
            ->select(DB::raw('DATE(sold_at) as day'), DB::raw('SUM(total) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->limit(14)
            ->get()
            ->map(fn ($row): array => [
                'label' => (string) $row->day,
                'value' => round((float) $row->total, 2),
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<Sale>
     */
    public function salesQuery(array $filters): Builder
    {
        $query = Sale::query()->where('status', 'registrada');

        if (! empty($filters['date_from'])) {
            $query->whereDate('sold_at', '>=', $filters['date_from']);
        } else {
            $query->whereDate('sold_at', '>=', now()->subDays(30)->toDateString());
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('sold_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * @return Builder<Product>
     */
    private function lowStockQuery(): Builder
    {
        return Product::query()
            ->where('is_active', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock');
    }
}
