<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DDoS Protection Configuration
    |--------------------------------------------------------------------------
    |
    | Configure DDoS protection settings for your application
    |
    */

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist
    |--------------------------------------------------------------------------
    |
    | IPs that should never be rate limited or blocked
    |
    */
    'whitelist' => [
        '127.0.0.1',    // Localhost
        '::1',          // IPv6 localhost
        // Add your office/server IPs here
        // '192.168.1.100',
        // '10.0.0.5',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permanent Blacklist
    |--------------------------------------------------------------------------
    |
    | IPs that should always be blocked
    |
    */
    'blacklist' => [
        // Add known malicious IPs here
        // '192.0.2.1',
        // '198.51.100.1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limits
    |--------------------------------------------------------------------------
    |
    | Configure rate limits for different types of requests
    |
    */
    'rate_limits' => [
        'web' => [
            'requests' => 60,   // Reduced for shared hosting
            'window' => 60,     // window in seconds
        ],
        'api' => [
            'requests' => 30,   // Reduced for shared hosting
            'window' => 60,
        ],
        'admin' => [
            'requests' => 60,   // Reduced for shared hosting
            'window' => 60,
        ],
        'login' => [
            'requests' => 3,    // Reduced for better security
            'window' => 300,    // 5 minutes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Blacklist Durations
    |--------------------------------------------------------------------------
    |
    | How long to blacklist IPs for different violations (in seconds)
    |
    */
    'blacklist_duration' => [
        'rate_limit' => 600,    // 10 minutes
        'suspicious' => 3600,   // 1 hour
        'login_abuse' => 900,   // 15 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Suspicious Activity Threshold
    |--------------------------------------------------------------------------
    |
    | Number of suspicious requests before auto-blacklisting
    |
    */
    'suspicious_threshold' => 5,

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Features
    |--------------------------------------------------------------------------
    */
    'enabled' => env('DDOS_PROTECTION_ENABLED', true),
    'log_blocked_requests' => env('DDOS_LOG_BLOCKED', true),
    'auto_blacklist' => env('DDOS_AUTO_BLACKLIST', true),

];
