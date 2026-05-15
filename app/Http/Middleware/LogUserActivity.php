<?php

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function __construct(
        private readonly AuditService $audit
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() !== null
            && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)
        ) {
            $this->audit->logFromRoute($request, $response->getStatusCode());
        }

        return $response;
    }
}
