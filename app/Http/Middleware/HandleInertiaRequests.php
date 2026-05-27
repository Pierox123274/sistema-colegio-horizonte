<?php

namespace App\Http\Middleware;

use App\Services\AnnouncementService;
use App\Services\UserNotificationService;
use App\Support\IntranetNavigation;
use App\Support\StudentNavigation;
use App\Support\TeacherNavigation;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                    'roles' => $user->getRoleNames()->values()->all(),
                ] : null,
            ],
            'sidebarNav' => IntranetNavigation::items($user),
            'intranetHomeHref' => IntranetNavigation::sidebarHomeHref($user),
            'teacherNav' => TeacherNavigation::items($user),
            'studentNav' => StudentNavigation::items($user),
            'current_route' => fn () => $request->route()?->getName(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'ai' => fn () => $request->session()->only(['ai_reply', 'ai_meta']),
            ],
            'announcementBell' => fn () => $user !== null
                ? app(AnnouncementService::class)->headerPayloadFor($user)
                : null,
            'notificationCenter' => fn () => $user !== null
                ? app(UserNotificationService::class)->headerPayloadFor($user)
                : null,
        ];
    }
}
