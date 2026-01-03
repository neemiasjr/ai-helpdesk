<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Rate limiter for AI API calls
 * Prevents exceeding API rate limits
 */
class RateLimiter
{
    private const CACHE_PREFIX = 'ai_rate_limit:';
    
    /**
     * Check if request is allowed and record it
     */
    public function attempt(string $provider = 'default', int $maxRequests = 60, int $windowSeconds = 60): bool
    {
        $key = self::CACHE_PREFIX . $provider . ':' . (int)(time() / $windowSeconds);
        $count = Cache::get($key, 0);

        if ($count >= $maxRequests) {
            Log::warning('AI Rate Limiter: Rate limit exceeded', [
                'provider' => $provider,
                'count' => $count,
                'max' => $maxRequests,
            ]);
            return false;
        }

        Cache::put($key, $count + 1, $windowSeconds + 10);
        return true;
    }

    /**
     * Get remaining requests in current window
     */
    public function remaining(string $provider = 'default', int $maxRequests = 60, int $windowSeconds = 60): int
    {
        $key = self::CACHE_PREFIX . $provider . ':' . (int)(time() / $windowSeconds);
        $count = Cache::get($key, 0);
        return max(0, $maxRequests - $count);
    }
}

