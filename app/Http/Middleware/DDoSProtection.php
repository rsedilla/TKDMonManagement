<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DDoSProtection
{
    /**
     * Rate limiting configuration
     */
    private const RATE_LIMITS = [
        'api' => ['requests' => 60, 'window' => 60], // 60 requests per minute for API
        'web' => ['requests' => 120, 'window' => 60], // 120 requests per minute for web
        'login' => ['requests' => 5, 'window' => 300], // 5 login attempts per 5 minutes
        'admin' => ['requests' => 100, 'window' => 60], // 100 admin requests per minute
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $this->getClientIp($request);
        
        // Skip rate limiting for whitelisted IPs
        if ($this->isWhitelisted($ip)) {
            return $next($request);
        }

        // Check if IP is blacklisted
        if ($this->isBlacklisted($ip)) {
            return $this->blockRequest($ip, 'Blacklisted IP');
        }

        // Apply rate limiting based on route type
        $limitType = $this->getRateLimitType($request);
        
        if (!$this->checkRateLimit($ip, $limitType)) {
            return $this->blockRequest($ip, "Rate limit exceeded for {$limitType}");
        }

        // Check for suspicious patterns
        if ($this->isSuspiciousRequest($request)) {
            $this->flagSuspiciousActivity($ip, $request);
        }

        return $next($request);
    }

    /**
     * Get the real client IP address
     */
    private function getClientIp(Request $request): string
    {
        // Check for various proxy headers
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];

        foreach ($headers as $header) {
            if ($request->server($header)) {
                $ip = trim(explode(',', $request->server($header))[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }

    /**
     * Check if IP is whitelisted
     */
    private function isWhitelisted(string $ip): bool
    {
        $whitelist = config('ddos.whitelist', ['127.0.0.1', '::1']);
        return in_array($ip, $whitelist);
    }

    /**
     * Check if IP is blacklisted
     */
    private function isBlacklisted(string $ip): bool
    {
        return Cache::has("blacklist:{$ip}");
    }

    /**
     * Determine rate limit type based on request
     */
    private function getRateLimitType(Request $request): string
    {
        if ($request->is('api/*')) {
            return 'api';
        }
        
        if ($request->is('admin/*')) {
            return 'admin';
        }
        
        if ($request->is('login') || $request->is('admin/login')) {
            return 'login';
        }
        
        return 'web';
    }

    /**
     * Check rate limit for IP and type
     */
    private function checkRateLimit(string $ip, string $type): bool
    {
        $config = self::RATE_LIMITS[$type];
        $key = "rate_limit:{$type}:{$ip}";
        
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= $config['requests']) {
            // IP exceeded rate limit - add to temporary blacklist
            $this->tempBlacklist($ip, $type);
            return false;
        }
        
        // Increment counter
        Cache::put($key, $attempts + 1, $config['window']);
        
        return true;
    }

    /**
     * Add IP to temporary blacklist
     */
    private function tempBlacklist(string $ip, string $type): void
    {
        $duration = match($type) {
            'login' => 900, // 15 minutes for login abuse
            'api' => 300,   // 5 minutes for API abuse
            default => 600  // 10 minutes default
        };
        
        Cache::put("blacklist:{$ip}", true, $duration);
        
        Log::warning("IP temporarily blacklisted for rate limit violation", [
            'ip' => $ip,
            'type' => $type,
            'duration' => $duration,
            'timestamp' => now()
        ]);
    }

    /**
     * Check for suspicious request patterns
     */
    private function isSuspiciousRequest(Request $request): bool
    {
        // Check for common attack patterns
        $suspiciousPatterns = [
            'union select',
            'drop table',
            '<script',
            'javascript:',
            '../../../',
            'cmd.exe',
            '/etc/passwd',
            'wp-admin',
            'wp-login',
            '.env',
            'phpmyadmin'
        ];

        $content = strtolower($request->getContent() . $request->getQueryString() . $request->getPathInfo());
        
        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                return true;
            }
        }

        // Check for excessive query parameters
        if (count($request->query()) > 20) {
            return true;
        }

        // Check for suspicious user agents
        $userAgent = strtolower($request->userAgent() ?? '');
        $suspiciousAgents = ['bot', 'crawler', 'spider', 'scraper', 'curl', 'wget'];
        
        foreach ($suspiciousAgents as $agent) {
            if (strpos($userAgent, $agent) !== false && !$this->isLegitimateBot($userAgent)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if bot is legitimate (Google, Bing, etc.)
     */
    private function isLegitimateBot(string $userAgent): bool
    {
        $legitimateBots = [
            'googlebot',
            'bingbot',
            'slurp', // Yahoo
            'duckduckbot',
            'baiduspider',
            'yandexbot',
            'facebookexternalhit'
        ];

        foreach ($legitimateBots as $bot) {
            if (strpos($userAgent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Flag suspicious activity
     */
    private function flagSuspiciousActivity(string $ip, Request $request): void
    {
        $key = "suspicious:{$ip}";
        $count = Cache::get($key, 0) + 1;
        
        Cache::put($key, $count, 3600); // Track for 1 hour
        
        // Auto-blacklist after multiple suspicious requests
        if ($count >= 5) {
            Cache::put("blacklist:{$ip}", true, 3600); // Blacklist for 1 hour
            
            Log::alert("IP auto-blacklisted for suspicious activity", [
                'ip' => $ip,
                'suspicious_count' => $count,
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
        } else {
            Log::warning("Suspicious activity detected", [
                'ip' => $ip,
                'count' => $count,
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
        }
    }

    /**
     * Block request and return 429 response
     */
    private function blockRequest(string $ip, string $reason): Response
    {
        Log::warning("Request blocked", [
            'ip' => $ip,
            'reason' => $reason,
            'timestamp' => now()
        ]);

        return response()->json([
            'error' => 'Too Many Requests',
            'message' => 'Your request has been blocked due to suspicious activity.',
            'retry_after' => 300
        ], 429)->header('Retry-After', 300);
    }
}
