<?php

namespace App\Http\Middleware;

use App\Services\SecurityService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventSuspiciousAccess
{
    public function __construct(
        private readonly SecurityService $security
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() === null && ! $request->is('login')) {
            return $next($request);
        }

        if ($this->security->isIpSuspicious($request->ip())) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Acceso restringido por actividad sospechosa.',
                ], 429);
            }

            abort(429, 'Acceso restringido por actividad sospechosa.');
        }

        return $next($request);
    }
}
