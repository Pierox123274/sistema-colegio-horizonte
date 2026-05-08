<?php

namespace App\Services;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryMovementService
{
    public function paginateForIndex(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = InventoryMovement::query()
            ->with([
                'product:id,code,name,product_category_id',
                'product.category:id,code,name',
                'createdByUser:id,name',
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('reason', 'like', $like)
                    ->orWhereHas('product', function ($p) use ($like): void {
                        $p->where('code', 'like', $like)
                            ->orWhere('name', 'like', $like);
                    });
            });
        }

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->query('category_id')) {
            $query->whereHas('product', function ($p) use ($category): void {
                $p->where('product_category_id', (int) $category);
            });
        }

        if (filter_var($request->query('low_stock', false), FILTER_VALIDATE_BOOLEAN)) {
            $query->whereHas('product', function ($p): void {
                $p->whereColumn('current_stock', '<=', 'minimum_stock');
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function register(array $data, ?int $userId): InventoryMovement
    {
        return DB::transaction(function () use ($data, $userId): InventoryMovement {
            /** @var Product $product */
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail((int) $data['product_id']);

            $previousStock = (float) $product->current_stock;
            $quantity = (float) $data['quantity'];

            $newStock = match ($data['type']) {
                InventoryMovementType::Entrada->value => $previousStock + $quantity,
                InventoryMovementType::Salida->value => $previousStock - $quantity,
                InventoryMovementType::Ajuste->value => $quantity,
                default => throw ValidationException::withMessages([
                    'type' => ['Tipo de movimiento no válido.'],
                ]),
            };

            if ($newStock < -0.0001) {
                throw ValidationException::withMessages([
                    'quantity' => ['La operación deja stock negativo.'],
                ]);
            }

            $movement = InventoryMovement::query()->create([
                'product_id' => $product->id,
                'type' => $data['type'],
                'status' => 'registrado',
                'quantity' => $quantity,
                'previous_stock' => $previousStock,
                'new_stock' => max(0, $newStock),
                'reason' => $data['reason'],
                'observations' => $data['observations'] ?? null,
                'created_by_user_id' => $userId,
            ]);

            $product->update([
                'current_stock' => max(0, $newStock),
            ]);

            return $movement->fresh(['product', 'createdByUser']);
        });
    }

    /**
     * @return array{total_products: int, low_stock_products: int, active_products: int, total_movements: int}
     */
    public function stats(): array
    {
        return [
            'total_products' => Product::query()->count(),
            'low_stock_products' => Product::query()->whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'active_products' => Product::query()->where('is_active', true)->count(),
            'total_movements' => InventoryMovement::query()->count(),
        ];
    }

    public function cancel(InventoryMovement $movement): InventoryMovement
    {
        return DB::transaction(function () use ($movement): InventoryMovement {
            /** @var InventoryMovement $locked */
            $locked = InventoryMovement::query()->lockForUpdate()->findOrFail($movement->id);

            if ($locked->status === 'anulado') {
                return $locked;
            }

            $hasLaterRegistered = InventoryMovement::query()
                ->where('product_id', $locked->product_id)
                ->where('status', 'registrado')
                ->where('id', '>', $locked->id)
                ->exists();

            if ($hasLaterRegistered) {
                throw ValidationException::withMessages([
                    'movement' => ['No se puede anular: existen movimientos posteriores del mismo producto.'],
                ]);
            }

            $product = Product::query()->lockForUpdate()->findOrFail($locked->product_id);
            $product->update(['current_stock' => $locked->previous_stock]);
            $locked->update(['status' => 'anulado']);

            return $locked->fresh(['product', 'createdByUser']);
        });
    }
}
