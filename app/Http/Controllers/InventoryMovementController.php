<?php

namespace App\Http\Controllers;

use App\Enums\InventoryMovementType;
use App\Http\Requests\Intranet\StoreInventoryMovementRequest;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\InventoryMovementService;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class InventoryMovementController extends Controller
{
    public function __construct(
        private readonly InventoryMovementService $inventoryMovementService,
        private readonly ProductService $productService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', InventoryMovement::class);

        return Inertia::render('Intranet/Inventory/Movements/Index', [
            'movements' => $this->inventoryMovementService->paginateForIndex($request),
            'filters' => [
                'search' => $request->query('search', ''),
                'type' => $request->query('type', ''),
                'status' => $request->query('status', ''),
                'category_id' => $request->query('category_id', ''),
                'low_stock' => filter_var($request->query('low_stock', false), FILTER_VALIDATE_BOOLEAN),
            ],
            'catalog' => [
                'types' => InventoryMovementType::options(),
                'statuses' => [
                    ['value' => 'registrado', 'label' => 'Registrado'],
                    ['value' => 'anulado', 'label' => 'Anulado'],
                ],
                'categories' => $this->productService->categoryOptions(),
            ],
            'stats' => $this->inventoryMovementService->stats(),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', InventoryMovement::class);

        $products = Product::query()
            ->with('category:id,code,name')
            ->orderBy('code')
            ->get(['id', 'product_category_id', 'code', 'name', 'product_type', 'size', 'current_stock', 'minimum_stock', 'is_active'])
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'value' => (string) $product->id,
                'label' => $product->code.' - '.$product->name,
                'stock' => (string) $product->current_stock,
                'minimum_stock' => (string) $product->minimum_stock,
                'is_active' => $product->is_active,
                'category' => $product->category?->name,
                'product_type' => $product->product_type,
                'size' => $product->size,
            ])
            ->values()
            ->all();

        return Inertia::render('Intranet/Inventory/Movements/Create', [
            'catalog' => [
                'types' => InventoryMovementType::options(),
                'products' => $products,
            ],
        ]);
    }

    public function store(StoreInventoryMovementRequest $request): RedirectResponse
    {
        $movement = $this->inventoryMovementService->register(
            $request->validated(),
            $request->user()?->id
        );

        return redirect()
            ->route('intranet.inventory.movements.show', $movement)
            ->with('success', 'Movimiento registrado.');
    }

    public function show(Request $request, InventoryMovement $inventoryMovement): Response
    {
        $this->authorize('view', $inventoryMovement);
        $inventoryMovement->load(['product.category', 'createdByUser:id,name']);

        return Inertia::render('Intranet/Inventory/Movements/Show', [
            'movement' => $inventoryMovement,
        ]);
    }

    public function cancel(Request $request, InventoryMovement $inventoryMovement): RedirectResponse
    {
        $this->authorize('create', InventoryMovement::class);

        try {
            $this->inventoryMovementService->cancel($inventoryMovement);
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->with('error', $e->errors()['movement'][0] ?? 'No se pudo anular el movimiento.');
        }

        return redirect()
            ->route('intranet.inventory.movements.index')
            ->with('success', 'Movimiento anulado.');
    }
}
