<?php

namespace App\Http\Controllers\Intranet\Cms;

use App\Enums\CmsPublicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\Cms\StoreCmsPageRequest;
use App\Http\Requests\Intranet\Cms\UpdateCmsPageRequest;
use App\Models\Cms\CmsPage;
use App\Services\Cms\CmsContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CmsPageController extends Controller
{
    public function __construct(
        private readonly CmsContentService $content,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', CmsPage::class);

        return Inertia::render('Intranet/Cms/Pages/Index', [
            'pages' => $this->content->paginatePages([
                'search' => (string) $request->query('search', ''),
                'status' => (string) $request->query('status', ''),
            ]),
            'filters' => $request->only(['search', 'status']),
            'catalog' => ['statuses' => CmsPublicationStatus::options()],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', CmsPage::class);

        return Inertia::render('Intranet/Cms/Pages/Create', [
            'catalog' => ['statuses' => CmsPublicationStatus::options()],
        ]);
    }

    public function store(StoreCmsPageRequest $request): RedirectResponse
    {
        $page = $this->content->createPage($request->user(), $request->validated());

        return redirect()->route('intranet.cms.pages.edit', $page)->with('success', 'Página creada.');
    }

    public function edit(CmsPage $page): Response
    {
        $this->authorize('update', $page);

        return Inertia::render('Intranet/Cms/Pages/Edit', [
            'page' => $page,
            'catalog' => ['statuses' => CmsPublicationStatus::options()],
        ]);
    }

    public function update(UpdateCmsPageRequest $request, CmsPage $page): RedirectResponse
    {
        $this->content->updatePage($request->user(), $page, $request->validated());

        return back()->with('success', 'Página actualizada.');
    }

    public function destroy(Request $request, CmsPage $page): RedirectResponse
    {
        $this->authorize('delete', $page);
        $this->content->deletePage($request->user(), $page);

        return redirect()->route('intranet.cms.pages.index')->with('success', 'Página eliminada.');
    }
}
