<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityLogger
{
    /**
     * Handle an incoming request and log security events.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log failed authentication attempts
        if ($response->getStatusCode() === 401) {
            Log::warning('Failed authentication attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'timestamp' => now(),
            ]);
        }

        // Log suspicious activity (multiple failed attempts, unusual requests)
        if ($this->isSuspiciousRequest($request)) {
            Log::warning('Suspicious request detected', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'timestamp' => now(),
            ]);
        }

        // Log admin panel access
        if ($request->is('admin/*') && Auth::check()) {
            Log::info('Admin panel access', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'timestamp' => now(),
            ]);
        }

        return $response;
    }

    /**
     * Detect suspicious request patterns
     */
    private function isSuspiciousRequest(Request $request): bool
    {
        // Check for common attack patterns
        $suspiciousPatterns = [
            'script',
            'javascript:',
            '<script',
            'union select',
            'drop table',
            '../',
            '..\\',
            'eval(',
            'exec(',
        ];

        $requestData = strtolower($request->getContent() . $request->getQueryString());

        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($requestData, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}
