<?php

namespace App\Services;

use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\Guardian;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    public function paginateForIndex(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = Sale::query()
            ->with(['student:id,first_name,last_name,code', 'guardian:id,first_name,last_name', 'cashRegister:id,business_date', 'createdByUser:id,name'])
            ->withCount('items')
            ->orderByDesc('sold_at')
            ->orderByDesc('id');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($method = $request->query('payment_method')) {
            $query->where('payment_method', $method);
        }

        if ($from = $request->query('date_from')) {
            $query->whereDate('sold_at', '>=', $from);
        }

        if ($to = $request->query('date_to')) {
            $query->whereDate('sold_at', '<=', $to);
        }

        if ($day = $request->query('day')) {
            $query->whereDate('sold_at', $day);
        }

        if ($cashier = $request->query('cashier_id')) {
            $query->where('created_by_user_id', (int) $cashier);
        }

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('sale_code', 'like', $like)
                    ->orWhereHas('student', fn ($s) => $s->where('code', 'like', $like)->orWhere('first_name', 'like', $like)->orWhere('last_name', 'like', $like))
                    ->orWhereHas('guardian', fn ($g) => $g->where('first_name', 'like', $like)->orWhere('last_name', 'like', $like));
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, int $userId): Sale
    {
        return DB::transaction(function () use ($data, $userId): Sale {
            /** @var CashRegister|null $cashRegister */
            $cashRegister = CashRegister::query()
                ->where('user_id', $userId)
                ->where('status', 'abierta')
                ->latest('opened_at')
                ->first();

            if (! $cashRegister) {
                throw ValidationException::withMessages([
                    'cash_register' => ['Debe tener una caja abierta para registrar ventas.'],
                ]);
            }

            $items = $data['items'] ?? [];
            $total = 0.0;
            $normalized = [];
            foreach ($items as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail((int) $item['product_id']);
                if (! $product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => ['No se puede vender producto inactivo.'],
                    ]);
                }
                $qty = (float) $item['quantity'];
                if ((float) $product->current_stock < $qty - 0.0001) {
                    throw ValidationException::withMessages([
                        'items' => ['No hay stock suficiente para '.$product->name.'.'],
                    ]);
                }
                $unitPrice = (float) $item['unit_price'];
                $subtotal = $qty * $unitPrice;
                $total += $subtotal;
                $normalized[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];
            }

            $studentId = isset($data['student_id']) ? (int) $data['student_id'] : null;
            $guardianId = isset($data['guardian_id']) ? (int) $data['guardian_id'] : null;
            if ($studentId !== null && $guardianId !== null) {
                $linked = Student::query()
                    ->whereKey($studentId)
                    ->whereHas('guardians', fn ($q) => $q->where('guardians.id', $guardianId))
                    ->exists();
                if (! $linked) {
                    throw ValidationException::withMessages([
                        'guardian_id' => ['El apoderado no pertenece al estudiante seleccionado.'],
                    ]);
                }
            }

            $sale = Sale::query()->create([
                'cash_register_id' => $cashRegister->id,
                'sale_code' => $this->nextCode(),
                'student_id' => $studentId,
                'guardian_id' => $guardianId,
                'payment_method' => $data['payment_method'],
                'status' => 'registrada',
                'total' => $total,
                'sold_at' => $data['sold_at'],
                'created_by_user_id' => $userId,
                'observations' => $data['observations'] ?? null,
            ]);

            foreach ($normalized as $item) {
                /** @var Product $product */
                $product = $item['product'];
                SaleItem::query()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $newStock = max(0, (float) $product->current_stock - (float) $item['quantity']);
                $product->update(['current_stock' => $newStock]);
            }

            CashMovement::query()->create([
                'cash_register_id' => $cashRegister->id,
                'sale_id' => $sale->id,
                'type' => 'venta',
                'amount' => $total,
                'description' => 'Venta '.$sale->sale_code,
                'moved_at' => $data['sold_at'],
                'created_by_user_id' => $userId,
            ]);

            return $sale->fresh(['items.product', 'student', 'guardian', 'cashRegister']);
        });
    }

    public function cancel(Sale $sale, int $userId): Sale
    {
        return DB::transaction(function () use ($sale, $userId): Sale {
            /** @var Sale $locked */
            $locked = Sale::query()->with('items')->lockForUpdate()->findOrFail($sale->id);
            if ($locked->status === 'anulada') {
                return $locked;
            }

            foreach ($locked->items as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);
                $product->update([
                    'current_stock' => (float) $product->current_stock + (float) $item->quantity,
                ]);
            }

            $locked->update([
                'status' => 'anulada',
                'canceled_at' => now(),
                'canceled_by_user_id' => $userId,
            ]);

            CashMovement::query()->create([
                'cash_register_id' => $locked->cash_register_id,
                'sale_id' => $locked->id,
                'type' => 'anulacion_venta',
                'amount' => -1 * (float) $locked->total,
                'description' => 'Anulación venta '.$locked->sale_code,
                'moved_at' => now(),
                'created_by_user_id' => $userId,
            ]);

            return $locked->fresh(['items.product', 'student', 'guardian', 'cashRegister']);
        });
    }

    public function nextCode(): string
    {
        $date = now()->format('Ymd');
        $lastId = (int) (Sale::query()->max('id') ?? 0) + 1;

        return 'VTA-'.$date.'-'.str_pad((string) $lastId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * @return list<array{id:int,code:string,first_name:string,last_name:string,document_number:string|null}>
     */
    public function searchStudents(string $query): array
    {
        $term = trim($query);
        if (mb_strlen($term) < 2) {
            return [];
        }

        $like = '%'.$term.'%';

        return Student::query()
            ->where(function ($q) use ($like): void {
                $q->where('code', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('document_number', 'like', $like);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(20)
            ->get(['id', 'code', 'first_name', 'last_name', 'document_number'])
            ->map(fn (Student $s): array => [
                'id' => $s->id,
                'code' => $s->code,
                'first_name' => $s->first_name,
                'last_name' => $s->last_name,
                'document_number' => $s->document_number,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string,mixed>
     */
    public function studentPreview(Student $student): array
    {
        $student->load(['guardians:id,first_name,last_name']);
        $enrollment = $student->enrollments()
            ->latest('enrollment_date')
            ->first(['educational_level_id', 'grade_id', 'section_id']);

        return [
            'id' => $student->id,
            'code' => $student->code,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'document_number' => $student->document_number,
            'educational_level' => $student->educational_level?->value,
            'grade' => $student->grade,
            'section' => $student->section,
            'guardians' => $student->guardians->map(fn (Guardian $g): array => [
                'value' => (string) $g->id,
                'label' => $g->fullName(),
            ])->values()->all(),
            'latest_enrollment_id' => $enrollment?->id,
        ];
    }

    public function reportQuery(array $filters)
    {
        $query = Sale::query()
            ->with(['student:id,first_name,last_name,code', 'guardian:id,first_name,last_name', 'createdByUser:id,name']);

        if (! empty($filters['day'])) {
            $query->whereDate('sold_at', $filters['day']);
        } else {
            if (! empty($filters['date_from'])) {
                $query->whereDate('sold_at', '>=', $filters['date_from']);
            }
            if (! empty($filters['date_to'])) {
                $query->whereDate('sold_at', '<=', $filters['date_to']);
            }
        }
        if (! empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['cashier_id'])) {
            $query->where('created_by_user_id', (int) $filters['cashier_id']);
        }

        return $query->orderBy('sold_at')->orderBy('id');
    }

    /**
     * @param  Collection<int, Sale>  $sales
     * @return array<string,mixed>
     */
    public function reportSummary(Collection $sales): array
    {
        $totalSold = (float) $sales->where('status', 'registrada')->sum('total');
        $totalCanceled = (float) $sales->where('status', 'anulada')->sum('total');
        $net = $totalSold - $totalCanceled;
        $byMethod = $sales->groupBy('payment_method')
            ->map(fn (Collection $rows): array => [
                'count' => $rows->count(),
                'amount' => (float) $rows->sum('total'),
            ])->all();

        return [
            'count' => $sales->count(),
            'total_sold' => number_format($totalSold, 2, '.', ''),
            'total_canceled' => number_format($totalCanceled, 2, '.', ''),
            'net_total' => number_format($net, 2, '.', ''),
            'by_method' => $byMethod,
        ];
    }
}
