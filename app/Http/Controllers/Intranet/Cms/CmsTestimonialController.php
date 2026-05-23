<?php

namespace App\Http\Controllers\Intranet\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\Cms\StoreCmsTestimonialRequest;
use App\Http\Requests\Intranet\Cms\UpdateCmsTestimonialRequest;
use App\Models\Cms\CmsTestimonial;
use App\Services\Cms\CmsContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CmsTestimonialController extends Controller
{
    public function __construct(
        private readonly CmsContentService $content,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', CmsTestimonial::class);

        return Inertia::render('Intranet/Cms/Testimonials/Index', [
            'testimonials' => $this->content->paginateTestimonials(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', CmsTestimonial::class);

        return Inertia::render('Intranet/Cms/Testimonials/Create');
    }

    public function store(StoreCmsTestimonialRequest $request): RedirectResponse
    {
        $item = $this->content->createTestimonial($request->user(), $request->validated());

        return redirect()->route('intranet.cms.testimonials.edit', $item)->with('success', 'Testimonio creado.');
    }

    public function edit(CmsTestimonial $testimonial): Response
    {
        $this->authorize('update', $testimonial);

        return Inertia::render('Intranet/Cms/Testimonials/Edit', ['testimonial' => $testimonial]);
    }

    public function update(UpdateCmsTestimonialRequest $request, CmsTestimonial $testimonial): RedirectResponse
    {
        $this->content->updateTestimonial($request->user(), $testimonial, $request->validated());

        return back()->with('success', 'Testimonio actualizado.');
    }

    public function destroy(Request $request, CmsTestimonial $testimonial): RedirectResponse
    {
        $this->authorize('delete', $testimonial);
        $this->content->deleteTestimonial($request->user(), $testimonial);

        return redirect()->route('intranet.cms.testimonials.index')->with('success', 'Testimonio eliminado.');
    }
}
