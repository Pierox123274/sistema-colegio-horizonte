<?php

namespace App\Http\Controllers;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditSeverity;
use App\Models\UserSession;
use App\Services\AuditService;
use App\Services\SecurityService;
use App\Services\SessionSecurityService;
use App\Support\SecurityDashboard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class IntranetSecurityController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly SecurityService $security,
        private readonly SessionSecurityService $sessions,
    ) {}

    public function auditLogs(Request $request): Response
    {
        $this->authorize('viewAuditLogs', SecurityDashboard::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $filters = $request->only(['search', 'module', 'action', 'severity', 'user_id', 'date_from', 'date_to']);
        $paginator = $this->audit->paginate($filters, $user);
        $timeline = $this->audit->recentTimeline($filters, $user, 8);

        return Inertia::render('Intranet/Security/AuditLogs', [
            'filters' => $filters,
            'stats' => $this->audit->dashboardStats($user, $filters),
            'timeline' => $timeline->map(fn ($log) => $this->audit->serialize($log))->values()->all(),
            'logs' => [
                'data' => collect($paginator->items())->map(fn ($log) => $this->audit->serialize($log))->values()->all(),
                'links' => $paginator->linkCollection()->toArray(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                ],
            ],
            'catalog' => [
                'modules' => AuditModule::options(),
                'actions' => AuditAction::options(),
                'severities' => [
                    ['value' => 'info', 'label' => 'Informativo'],
                    ['value' => 'warning', 'label' => 'Advertencia'],
                    ['value' => 'critical', 'label' => 'Crítico'],
                ],
            ],
            'permissions' => [
                'full_audit' => $user->hasRole('Administrador'),
            ],
        ]);
    }

    public function sessions(Request $request): Response
    {
        $this->authorize('viewSessions', SecurityDashboard::class);

        $filters = $request->only(['user_id']);
        $paginator = $this->sessions->paginateActiveSessions($filters);

        return Inertia::render('Intranet/Security/Sessions', [
            'filters' => $filters,
            'stats' => $this->sessions->sessionDashboardStats(),
            'sessions' => [
                'data' => collect($paginator->items())->map(fn ($s) => $this->sessions->serializeSession($s))->values()->all(),
                'links' => $paginator->linkCollection()->toArray(),
                'meta' => ['total' => $paginator->total()],
            ],
        ]);
    }

    public function loginAttempts(Request $request): Response
    {
        $this->authorize('viewLoginAttempts', SecurityDashboard::class);

        $filters = $request->only(['email', 'successful', 'ip']);
        $paginator = $this->security->paginateLoginAttempts($filters);

        return Inertia::render('Intranet/Security/LoginAttempts', [
            'filters' => $filters,
            'stats' => $this->security->loginAttemptStats(),
            'attempts' => [
                'data' => collect($paginator->items())->map(fn ($a) => $this->security->serializeLoginAttempt($a))->values()->all(),
                'links' => $paginator->linkCollection()->toArray(),
                'meta' => ['total' => $paginator->total()],
            ],
        ]);
    }

    public function accessMonitor(Request $request): Response
    {
        $this->authorize('viewAccessMonitor', SecurityDashboard::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $recent = $this->security->recentAccesses(20)->map(
            fn ($attempt) => $this->security->serializeLoginAttempt($attempt)
        )->values()->all();

        return Inertia::render('Intranet/Security/AccessMonitor', [
            'summary' => array_merge(
                $this->security->accessMonitorSummary(),
                [
                    'events_today' => \App\Models\AuditLog::query()
                        ->whereDate('created_at', today())
                        ->count(),
                    'critical_events' => \App\Models\AuditLog::query()
                        ->where('severity', AuditSeverity::Critical->value)
                        ->where('created_at', '>=', now()->subHours(24))
                        ->count(),
                ],
            ),
            'recent_accesses' => $recent,
            'recent_audit' => $this->audit->recentTimeline([], $user, 10)
                ->map(fn ($log) => $this->audit->serialize($log))
                ->values()
                ->all(),
        ]);
    }

    public function revokeOtherSessions(Request $request): RedirectResponse
    {
        $this->authorize('revokeOwnSessions', SecurityDashboard::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $count = $this->sessions->invalidateOtherSessions($user, (string) $request->session()->getId());

        $this->audit->log(
            AuditAction::Logout,
            AuditModule::Security,
            $user,
            description: "Cierre de {$count} sesión(es) adicional(es)",
            severity: AuditSeverity::Warning,
            request: $request,
        );

        return back()->with('success', 'Se cerraron las demás sesiones activas.');
    }

    public function revokeSession(Request $request, UserSession $userSession): RedirectResponse
    {
        $this->authorize('revokeSessions', SecurityDashboard::class);

        $actor = $request->user();
        abort_if($actor === null, 403);

        DB::table('sessions')
            ->where('id', $userSession->session_id)
            ->delete();

        $userSession->update([
            'is_active' => false,
            'logged_out_at' => now(),
        ]);

        $this->audit->log(
            AuditAction::Logout,
            AuditModule::Security,
            $actor,
            UserSession::class,
            $userSession->id,
            description: 'Sesión revocada por administrador',
            severity: AuditSeverity::Critical,
            request: $request,
        );

        return back()->with('success', 'Sesión cerrada correctamente.');
    }
}
