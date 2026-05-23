<?php

namespace App\Http\Controllers\Intranet\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\Cms\StoreCmsHeroSlideRequest;
use App\Http\Requests\Intranet\Cms\UpdateCmsHeroSlideRequest;
use App\Models\Cms\CmsHeroSlide;
use App\Services\Cms\CmsContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CmsHeroSlideController extends Controller
{
    public function __construct(
        private readonly CmsContentService $content,
    ) {}

    public function index(): Response
    {
        $this->authorize('viewAny', CmsHeroSlide::class);

        return Inertia::render('Intranet/Cms/HeroSlides/Index', [
            'slides' => $this->content->paginateHeroSlides(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', CmsHeroSlide::class);

        return Inertia::render('Intranet/Cms/HeroSlides/Create');
    }

    public function store(StoreCmsHeroSlideRequest $request): RedirectResponse
    {
        $slide = $this->content->createHeroSlide($request->user(), $request->validated());

        return redirect()->route('intranet.cms.hero-slides.edit', $slide)->with('success', 'Slide creado.');
    }

    public function edit(CmsHeroSlide $hero_slide): Response
    {
        $this->authorize('update', $hero_slide);

        return Inertia::render('Intranet/Cms/HeroSlides/Edit', ['slide' => $hero_slide]);
    }

    public function update(UpdateCmsHeroSlideRequest $request, CmsHeroSlide $hero_slide): RedirectResponse
    {
        $this->content->updateHeroSlide($request->user(), $hero_slide, $request->validated());

        return back()->with('success', 'Slide actualizado.');
    }

    public function destroy(Request $request, CmsHeroSlide $hero_slide): RedirectResponse
    {
        $this->authorize('delete', $hero_slide);
        $this->content->deleteHeroSlide($request->user(), $hero_slide);

        return redirect()->route('intranet.cms.hero-slides.index')->with('success', 'Slide eliminado.');
    }
}
