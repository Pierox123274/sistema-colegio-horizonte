<?php

namespace App\Http\Controllers\Intranet\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Intranet\Cms\UpdateCmsSettingsRequest;
use App\Models\Cms\CmsSetting;
use App\Services\Cms\CmsContentService;
use App\Services\Cms\CmsSettingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CmsSettingController extends Controller
{
    public function __construct(
        private readonly CmsSettingService $settings,
        private readonly CmsContentService $content,
    ) {}

    public function index(): Response
    {
        $this->authorize('viewAny', CmsSetting::class);

        return Inertia::render('Intranet/Cms/Settings/Index', [
            'settings' => $this->settings->all(),
        ]);
    }

    public function update(UpdateCmsSettingsRequest $request): RedirectResponse
    {
        $this->content->updateSettings($request->user(), $request->input('settings', []));

        return back()->with('success', 'Configuración actualizada.');
    }
}
