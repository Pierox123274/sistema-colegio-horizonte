<?php

namespace App\Http\Controllers\Intranet\Cms;

use App\Http\Controllers\Controller;
use App\Models\Cms\CmsPage;
use App\Services\Cms\CmsContentService;
use Inertia\Inertia;
use Inertia\Response;

class CmsDashboardController extends Controller
{
    public function __construct(
        private readonly CmsContentService $content,
    ) {}

    public function index(): Response
    {
        $this->authorize('viewAny', CmsPage::class);

        return Inertia::render('Intranet/Cms/Dashboard', [
            'overview' => $this->content->dashboardOverview(),
        ]);
    }
}
