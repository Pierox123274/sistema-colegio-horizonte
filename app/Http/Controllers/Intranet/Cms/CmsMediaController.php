<?php

namespace App\Http\Controllers\Intranet\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\Cms\StoreCmsMediaRequest;
use App\Models\Cms\CmsMedia;
use App\Services\Cms\CmsMediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CmsMediaController extends Controller
{
    public function __construct(
        private readonly CmsMediaService $media,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', CmsMedia::class);

        $paginator = $this->media->paginate([
            'search' => (string) $request->query('search', ''),
            'mime' => (string) $request->query('mime', 'image'),
        ]);

        return Inertia::render('Intranet/Cms/Media/Index', [
            'media' => [
                'data' => $this->media->browseItems($paginator),
                'links' => $paginator->linkCollection()->toArray(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                ],
            ],
            'filters' => $request->only(['search']),
        ]);
    }

    public function browse(Request $request): JsonResponse
    {
        $this->authorize('viewAny', CmsMedia::class);

        $paginator = $this->media->paginate([
            'search' => (string) $request->query('search', ''),
            'mime' => 'image',
        ], (int) $request->query('per_page', 24));

        return response()->json([
            'data' => $this->media->browseItems($paginator),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function store(StoreCmsMediaRequest $request): RedirectResponse|JsonResponse
    {
        $media = $this->media->store(
            $request->file('file'),
            $request->user(),
            $request->input('alt'),
        );

        if ($request->wantsJson()) {
            return response()->json([
                'media' => $this->media->toPickerArray($media),
                'message' => 'Archivo subido.',
            ]);
        }

        return back()->with('success', 'Archivo subido.');
    }

    public function destroy(Request $request, CmsMedia $cms_medium): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $cms_medium);
        $this->media->destroy($cms_medium, $request->user());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Archivo eliminado.']);
        }

        return back()->with('success', 'Archivo eliminado.');
    }
}
