<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DDoSManagement extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ddos:manage {action} {ip?}';

    /**
     * The console command description.
     */
    protected $description = 'Manage DDoS protection - blacklist, whitelist, and monitor IPs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');
        $ip = $this->argument('ip');

        return match($action) {
            'status' => $this->showStatus(),
            'blacklist' => $this->blacklistIp($ip),
            'whitelist' => $this->whitelistIp($ip),
            'unblock' => $this->unblockIp($ip),
            'clear' => $this->clearAll(),
            'stats' => $this->showStats(),
            default => $this->showHelp()
        };
    }

    /**
     * Show current DDoS protection status
     */
    private function showStatus(): int
    {
        $this->info('DDoS Protection Status');
        $this->line('========================');
        
        $enabled = config('ddos.enabled', true);
        $this->line('Status: ' . ($enabled ? 'ENABLED' : 'DISABLED'));
        
        if ($enabled) {
            $this->line('Rate Limits:');
            foreach (config('ddos.rate_limits', []) as $type => $config) {
                $this->line("  {$type}: {$config['requests']} requests per {$config['window']} seconds");
            }
        }
        
        return 0;
    }

    /**
     * Blacklist an IP address
     */
    private function blacklistIp(?string $ip): int
    {
        if (!$ip) {
            $this->error('IP address required for blacklist action');
            return 1;
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->error('Invalid IP address format');
            return 1;
        }

        $duration = config('ddos.blacklist_duration.manual', 86400); // 24 hours default
        Cache::put("blacklist:{$ip}", true, $duration);
        
        Log::warning("IP manually blacklisted", [
            'ip' => $ip,
            'duration' => $duration,
            'admin' => 'console',
            'timestamp' => now()
        ]);

        $this->info("IP {$ip} has been blacklisted for {$duration} seconds");
        return 0;
    }

    /**
     * Add IP to whitelist
     */
    private function whitelistIp(?string $ip): int
    {
        if (!$ip) {
            $this->error('IP address required for whitelist action');
            return 1;
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->error('Invalid IP address format');
            return 1;
        }

        // Remove from blacklist if present
        Cache::forget("blacklist:{$ip}");
        
        // Add to temporary whitelist (you should add permanent ones to config)
        Cache::put("whitelist:{$ip}", true, 86400);

        $this->info("IP {$ip} has been temporarily whitelisted");
        $this->line("For permanent whitelist, add to config/ddos.php");
        return 0;
    }

    /**
     * Unblock an IP address
     */
    private function unblockIp(?string $ip): int
    {
        if (!$ip) {
            $this->error('IP address required for unblock action');
            return 1;
        }

        // Remove from blacklist and rate limit caches
        $removed = 0;
        $patterns = ["blacklist:{$ip}", "rate_limit:*:{$ip}", "suspicious:{$ip}"];
        
        foreach ($patterns as $pattern) {
            if (strpos($pattern, '*') !== false) {
                // Handle wildcard patterns
                $keys = Cache::getRedis()->keys(str_replace('*', '*', $pattern));
                foreach ($keys as $key) {
                    Cache::forget($key);
                    $removed++;
                }
            } else {
                if (Cache::has($pattern)) {
                    Cache::forget($pattern);
                    $removed++;
                }
            }
        }

        $this->info("IP {$ip} has been unblocked ({$removed} cache entries removed)");
        return 0;
    }

    /**
     * Clear all DDoS caches
     */
    private function clearAll(): int
    {
        if (!$this->confirm('This will clear all DDoS protection caches. Continue?')) {
            return 0;
        }

        $patterns = ['blacklist:*', 'rate_limit:*', 'suspicious:*', 'whitelist:*'];
        $removed = 0;

        foreach ($patterns as $pattern) {
            try {
                $keys = Cache::getRedis()->keys($pattern);
                foreach ($keys as $key) {
                    Cache::forget($key);
                    $removed++;
                }
            } catch (\Exception $e) {
                // If Redis is not available, try with cache tags or other methods
                $this->warn("Could not clear pattern {$pattern}: " . $e->getMessage());
            }
        }

        $this->info("Cleared {$removed} DDoS cache entries");
        return 0;
    }

    /**
     * Show DDoS statistics
     */
    private function showStats(): int
    {
        $this->info('DDoS Protection Statistics');
        $this->line('===========================');

        // This is a simplified version - in production you'd want to store more detailed stats
        try {
            $blacklistedCount = count(Cache::getRedis()->keys('blacklist:*'));
            $rateLimitedCount = count(Cache::getRedis()->keys('rate_limit:*'));
            $suspiciousCount = count(Cache::getRedis()->keys('suspicious:*'));

            $this->line("Currently blacklisted IPs: {$blacklistedCount}");
            $this->line("IPs with rate limit tracking: {$rateLimitedCount}");
            $this->line("IPs flagged as suspicious: {$suspiciousCount}");
        } catch (\Exception $e) {
            $this->warn('Could not retrieve statistics: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Show help information
     */
    private function showHelp(): int
    {
        $this->line('DDoS Protection Management Commands:');
        $this->line('');
        $this->line('php artisan ddos:manage status          - Show protection status');
        $this->line('php artisan ddos:manage blacklist <ip>  - Blacklist an IP');
        $this->line('php artisan ddos:manage whitelist <ip>  - Whitelist an IP');
        $this->line('php artisan ddos:manage unblock <ip>    - Unblock an IP');
        $this->line('php artisan ddos:manage clear           - Clear all caches');
        $this->line('php artisan ddos:manage stats           - Show statistics');
        
        return 0;
    }
}
