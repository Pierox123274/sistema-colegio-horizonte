<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditSeverity;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuditService;
use App\Services\SessionSecurityService;
use App\Support\AuthRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly SessionSecurityService $sessions,
    ) {}

    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        if ($user !== null) {
            $this->sessions->registerSession($user, $request);
            $this->audit->log(
                AuditAction::Login,
                AuditModule::Auth,
                $user,
                description: 'Inicio de sesión exitoso',
                severity: AuditSeverity::Info,
                request: $request,
            );
        }

        return redirect()->intended(
            AuthRedirect::redirectPathForUser($user),
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user !== null) {
            $this->audit->log(
                AuditAction::Logout,
                AuditModule::Auth,
                $user,
                description: 'Cierre de sesión',
                severity: AuditSeverity::Info,
                request: $request,
            );
            $this->sessions->terminateSession($request);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
