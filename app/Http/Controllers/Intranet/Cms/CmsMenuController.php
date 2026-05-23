<?php

namespace App\Http\Controllers\Intranet\Cms;

use App\Enums\CmsMenuLocation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\Cms\UpdateCmsMenuRequest;
use App\Models\Cms\CmsMenu;
use App\Services\Cms\CmsContentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CmsMenuController extends Controller
{
    public function __construct(
        private readonly CmsContentService $content,
    ) {}

    public function index(): Response
    {
        $this->authorize('viewAny', CmsMenu::class);

        return Inertia::render('Intranet/Cms/Menus/Index', [
            'locations' => CmsMenuLocation::options(),
        ]);
    }

    public function edit(string $location): Response
    {
        $loc = CmsMenuLocation::from($location);
        $menu = $this->content->menuForLocation($loc);
        abort_if($menu === null, 404);

        $this->authorize('view', $menu);

        return Inertia::render('Intranet/Cms/Menus/Edit', [
            'menu' => $menu,
            'location' => $loc->value,
        ]);
    }

    public function update(UpdateCmsMenuRequest $request, CmsMenu $menu): RedirectResponse
    {
        $this->content->syncMenu($request->user(), $menu, $request->input('items', []));

        return back()->with('success', 'Menú actualizado.');
    }
}
