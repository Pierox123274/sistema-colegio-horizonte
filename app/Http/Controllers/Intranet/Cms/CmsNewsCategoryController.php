<?php

namespace App\Http\Controllers\Intranet\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\Cms\StoreCmsNewsCategoryRequest;
use App\Http\Requests\Intranet\Cms\UpdateCmsNewsCategoryRequest;
use App\Models\Cms\CmsNewsCategory;
use App\Services\Cms\CmsContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CmsNewsCategoryController extends Controller
{
    public function __construct(
        private readonly CmsContentService $content,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', CmsNewsCategory::class);

        return Inertia::render('Intranet/Cms/News/Categories/Index', [
            'categories' => $this->content->newsCategories(),
        ]);
    }

    public function store(StoreCmsNewsCategoryRequest $request): RedirectResponse
    {
        $this->content->createNewsCategory($request->user(), $request->validated());

        return back()->with('success', 'Categoría creada.');
    }

    public function update(UpdateCmsNewsCategoryRequest $request, CmsNewsCategory $category): RedirectResponse
    {
        $this->content->updateNewsCategory($request->user(), $category, $request->validated());

        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(Request $request, CmsNewsCategory $category): RedirectResponse
    {
        $this->authorize('delete', $category);
        $this->content->deleteNewsCategory($request->user(), $category);

        return back()->with('success', 'Categoría eliminada.');
    }
}
