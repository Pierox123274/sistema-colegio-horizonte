<?php

namespace App\Http\Controllers;

use App\Http\Requests\Intranet\StoreProductCategoryRequest;
use App\Http\Requests\Intranet\UpdateProductCategoryRequest;
use App\Models\ProductCategory;
use App\Services\ProductCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductCategoryController extends Controller
{
    public function __construct(
        private readonly ProductCategoryService $productCategoryService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', ProductCategory::class);

        return Inertia::render('Intranet/Inventory/Categories/Index', [
            'categories' => $this->productCategoryService->paginateForIndex($request),
            'filters' => [
                'search' => $request->query('search', ''),
                'is_active' => $request->query('is_active', ''),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', ProductCategory::class);

        return Inertia::render('Intranet/Inventory/Categories/Create');
    }

    public function store(StoreProductCategoryRequest $request): RedirectResponse
    {
        $category = $this->productCategoryService->create($request->validated());

        return redirect()
            ->route('intranet.inventory.categories.show', $category)
            ->with('success', 'Categoría registrada.');
    }

    public function show(Request $request, ProductCategory $productCategory): Response
    {
        $this->authorize('view', $productCategory);

        $productCategory->loadCount('products');
        $products = $productCategory->products()
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'current_stock', 'minimum_stock', 'is_active']);

        return Inertia::render('Intranet/Inventory/Categories/Show', [
            'category' => $productCategory,
            'products' => $products,
        ]);
    }

    public function edit(Request $request, ProductCategory $productCategory): Response
    {
        $this->authorize('update', $productCategory);

        return Inertia::render('Intranet/Inventory/Categories/Edit', [
            'category' => $productCategory,
        ]);
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory): RedirectResponse
    {
        $this->productCategoryService->update($productCategory, $request->validated());

        return redirect()
            ->route('intranet.inventory.categories.show', $productCategory)
            ->with('success', 'Categoría actualizada.');
    }

    public function deactivate(Request $request, ProductCategory $productCategory): RedirectResponse
    {
        $this->authorize('update', $productCategory);
        $this->productCategoryService->deactivate($productCategory);

        return redirect()
            ->route('intranet.inventory.categories.index')
            ->with('success', 'Categoría desactivada.');
    }
}
