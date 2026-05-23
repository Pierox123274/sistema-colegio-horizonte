<?php

namespace App\Http\Controllers\Intranet\Cms;

use App\Enums\CmsPublicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\Cms\StoreCmsNewsRequest;
use App\Http\Requests\Intranet\Cms\UpdateCmsNewsRequest;
use App\Models\Cms\CmsNews;
use App\Services\Cms\CmsContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CmsNewsController extends Controller
{
    public function __construct(
        private readonly CmsContentService $content,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', CmsNews::class);

        return Inertia::render('Intranet/Cms/News/Index', [
            'news' => $this->content->paginateNews([
                'search' => (string) $request->query('search', ''),
                'status' => (string) $request->query('status', ''),
            ]),
            'filters' => $request->only(['search', 'status']),
            'catalog' => [
                'statuses' => CmsPublicationStatus::options(),
                'categories' => $this->content->newsCategories(),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', CmsNews::class);

        return Inertia::render('Intranet/Cms/News/Create', [
            'catalog' => [
                'statuses' => CmsPublicationStatus::options(),
                'categories' => $this->content->newsCategories(),
            ],
        ]);
    }

    public function store(StoreCmsNewsRequest $request): RedirectResponse
    {
        $news = $this->content->createNews($request->user(), $request->validated());

        return redirect()->route('intranet.cms.news.edit', $news)->with('success', 'Noticia creada.');
    }

    public function edit(CmsNews $news): Response
    {
        $this->authorize('update', $news);

        return Inertia::render('Intranet/Cms/News/Edit', [
            'news' => $news->load('category:id,name,slug'),
            'catalog' => [
                'statuses' => CmsPublicationStatus::options(),
                'categories' => $this->content->newsCategories(),
            ],
        ]);
    }

    public function update(UpdateCmsNewsRequest $request, CmsNews $news): RedirectResponse
    {
        $this->content->updateNews($request->user(), $news, $request->validated());

        return back()->with('success', 'Noticia actualizada.');
    }

    public function destroy(Request $request, CmsNews $news): RedirectResponse
    {
        $this->authorize('delete', $news);
        $this->content->deleteNews($request->user(), $news);

        return redirect()->route('intranet.cms.news.index')->with('success', 'Noticia eliminada.');
    }
}
