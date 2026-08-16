<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TraceContextMiddleware
{
    /**
     * Handle an incoming request: inject correlation trace ID and contextual metadata into logs and response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-ID');
        if (! $requestId || ! Str::isUuid($requestId)) {
            $requestId = (string) Str::uuid();
        }

        $request->headers->set('X-Request-ID', $requestId);

        // Share with Monolog global context for this request lifecycle
        Log::shareContext([
            'request_id' => $requestId,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'uri' => $request->path(),
            'user_id' => $request->user()?->id_user ?? null,
        ]);

        $response = $next($request);

        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}
