<?php

namespace App\Http\Controllers;

use App\Http\Requests\Intranet\StoreProductRequest;
use App\Http\Requests\Intranet\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Product::class);

        return Inertia::render('Intranet/Inventory/Products/Index', [
            'products' => $this->productService->paginateForIndex($request),
            'filters' => [
                'search' => $request->query('search', ''),
                'category_id' => $request->query('category_id', ''),
                'product_type' => $request->query('product_type', ''),
                'size' => $request->query('size', ''),
                'is_active' => $request->query('is_active', ''),
                'low_stock' => filter_var($request->query('low_stock', false), FILTER_VALIDATE_BOOLEAN),
            ],
            'catalog' => [
                'categories' => $this->productService->categoryOptions(),
                'product_types' => ProductService::productTypeOptions(),
                'sizes' => ProductService::sizeOptions(),
                'gender_targets' => ProductService::genderTargetOptions(),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Product::class);

        return Inertia::render('Intranet/Inventory/Products/Create', [
            'catalog' => [
                'categories' => $this->productService->categoryOptions(),
                'product_types' => ProductService::productTypeOptions(),
                'sizes' => ProductService::sizeOptions(),
                'gender_targets' => ProductService::genderTargetOptions(),
            ],
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productService->create($request->validated());

        return redirect()
            ->route('intranet.inventory.products.show', $product)
            ->with('success', 'Producto registrado.');
    }

    public function show(Product $product): Response
    {
        $this->authorize('view', $product);

        $product->load([
            'category',
            'inventoryMovements' => fn ($q) => $q
                ->with('createdByUser:id,name')
                ->orderByDesc('created_at')
                ->limit(20),
        ]);

        return Inertia::render('Intranet/Inventory/Products/Show', [
            'product' => $product,
            'is_low_stock' => (float) $product->current_stock <= (float) $product->minimum_stock,
        ]);
    }

    public function edit(Product $product): Response
    {
        $this->authorize('update', $product);

        $product->load('category:id,code,name');

        return Inertia::render('Intranet/Inventory/Products/Edit', [
            'product' => $product,
            'catalog' => [
                'categories' => $this->productService->categoryOptions(),
                'product_types' => ProductService::productTypeOptions(),
                'sizes' => ProductService::sizeOptions(),
                'gender_targets' => ProductService::genderTargetOptions(),
            ],
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->update($product, $request->validated());

        return redirect()
            ->route('intranet.inventory.products.show', $product)
            ->with('success', 'Producto actualizado.');
    }

    public function deactivate(Product $product): RedirectResponse
    {
        $this->authorize('update', $product);
        $this->productService->deactivate($product);

        return redirect()
            ->route('intranet.inventory.products.index')
            ->with('success', 'Producto desactivado.');
    }
}
