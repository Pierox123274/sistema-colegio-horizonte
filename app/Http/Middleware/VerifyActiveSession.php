<?php

namespace App\Http\Middleware;

use App\Services\SessionSecurityService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyActiveSession
{
    public function __construct(
        private readonly SessionSecurityService $sessions
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() !== null && ! $this->sessions->isSessionValid($request)) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('status', 'Su sesión expiró o fue cerrada. Inicie sesión nuevamente.');
        }

        if ($request->user() !== null) {
            $this->sessions->touchSession($request);
        }

        return $next($request);
    }
}
