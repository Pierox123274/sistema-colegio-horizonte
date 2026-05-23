<?php

namespace App\Http\Controllers\Intranet\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\Cms\UpdateCmsHomepageRequest;
use App\Models\Cms\CmsSection;
use App\Services\Cms\CmsContentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CmsHomepageController extends Controller
{
    public function __construct(
        private readonly CmsContentService $content,
    ) {}

    public function edit(): Response
    {
        $this->authorize('viewAny', CmsSection::class);

        return Inertia::render('Intranet/Cms/Homepage/Edit', [
            'sections' => $this->content->homepageSections(),
        ]);
    }

    public function update(UpdateCmsHomepageRequest $request): RedirectResponse
    {
        $this->content->syncHomepageSections($request->user(), $request->input('sections', []));

        return back()->with('success', 'Inicio actualizado.');
    }
}
