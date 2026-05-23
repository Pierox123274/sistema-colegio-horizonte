<?php

namespace App\Http\Controllers\Intranet\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\Cms\StoreCmsGalleryRequest;
use App\Http\Requests\Intranet\Cms\UpdateCmsGalleryRequest;
use App\Http\Requests\Intranet\Cms\UploadCmsGalleryImagesRequest;
use App\Models\Cms\CmsGallery;
use App\Models\Cms\CmsGalleryImage;
use App\Services\Cms\CmsContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CmsGalleryController extends Controller
{
    public function __construct(
        private readonly CmsContentService $content,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', CmsGallery::class);

        return Inertia::render('Intranet/Cms/Galleries/Index', [
            'galleries' => $this->content->paginateGalleries([
                'search' => (string) $request->query('search', ''),
            ]),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', CmsGallery::class);

        return Inertia::render('Intranet/Cms/Galleries/Create');
    }

    public function store(StoreCmsGalleryRequest $request): RedirectResponse
    {
        $gallery = $this->content->createGallery($request->user(), $request->validated());

        return redirect()->route('intranet.cms.galleries.edit', $gallery)->with('success', 'Galería creada.');
    }

    public function edit(CmsGallery $gallery): Response
    {
        $this->authorize('update', $gallery);

        return Inertia::render('Intranet/Cms/Galleries/Edit', [
            'gallery' => $gallery->load('images'),
        ]);
    }

    public function update(UpdateCmsGalleryRequest $request, CmsGallery $gallery): RedirectResponse
    {
        $this->content->updateGallery($request->user(), $gallery, $request->validated());

        return back()->with('success', 'Galería actualizada.');
    }

    public function destroy(Request $request, CmsGallery $gallery): RedirectResponse
    {
        $this->authorize('delete', $gallery);
        $this->content->deleteGallery($request->user(), $gallery);

        return redirect()->route('intranet.cms.galleries.index')->with('success', 'Galería eliminada.');
    }

    public function uploadImages(UploadCmsGalleryImagesRequest $request, CmsGallery $gallery): RedirectResponse
    {
        $this->content->addGalleryImages(
            $request->user(),
            $gallery,
            $request->input('images', []),
            $request->file('file'),
        );

        return back()->with('success', 'Imágenes agregadas.');
    }

    public function updateImage(Request $request, CmsGallery $gallery, CmsGalleryImage $image): RedirectResponse
    {
        $this->authorize('update', $gallery);

        $data = $request->validate([
            'caption' => ['nullable', 'string', 'max:300'],
            'category' => ['nullable', 'string', 'max:80'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $this->content->updateGalleryImage($request->user(), $gallery, $image, $data);

        return back()->with('success', 'Imagen actualizada.');
    }

    public function destroyImage(Request $request, CmsGallery $gallery, CmsGalleryImage $image): RedirectResponse
    {
        $this->authorize('update', $gallery);
        $this->content->deleteGalleryImage($request->user(), $gallery, $image);

        return back()->with('success', 'Imagen eliminada.');
    }

    public function reorderImages(Request $request, CmsGallery $gallery): RedirectResponse
    {
        $this->authorize('update', $gallery);

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:cms_gallery_images,id'],
        ]);

        $this->content->reorderGalleryImages($request->user(), $gallery, $validated['order']);

        return back()->with('success', 'Orden actualizado.');
    }
}
