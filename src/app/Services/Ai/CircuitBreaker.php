<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Simple Circuit Breaker implementation for AI API calls
 * Prevents cascading failures by temporarily blocking requests after failures
 */
class CircuitBreaker
{
    private const CACHE_PREFIX = 'ai_circuit_breaker:';
    private const FAILURE_THRESHOLD = 5; // Open circuit after 5 failures
    private const TIMEOUT_SECONDS = 60; // Keep circuit open for 60 seconds
    private const HALF_OPEN_TIMEOUT = 30; // Try again after 30 seconds

    /**
     * Check if circuit is open (blocking requests)
     */
    public function isOpen(string $provider = 'default'): bool
    {
        $state = Cache::get(self::CACHE_PREFIX . $provider . ':state', 'closed');
        
        if ($state === 'open') {
            // Check if we should transition to half-open
            $openedAt = Cache::get(self::CACHE_PREFIX . $provider . ':opened_at');
            if ($openedAt && (time() - $openedAt) > self::HALF_OPEN_TIMEOUT) {
                Cache::put(self::CACHE_PREFIX . $provider . ':state', 'half-open', self::TIMEOUT_SECONDS);
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Record a successful request
     */
    public function recordSuccess(string $provider = 'default'): void
    {
        Cache::forget(self::CACHE_PREFIX . $provider . ':failures');
        Cache::put(self::CACHE_PREFIX . $provider . ':state', 'closed', self::TIMEOUT_SECONDS);
        Log::info('AI Circuit Breaker: Recorded success', ['provider' => $provider]);
    }

    /**
     * Record a failed request
     */
    public function recordFailure(string $provider = 'default'): void
    {
        $failures = Cache::get(self::CACHE_PREFIX . $provider . ':failures', 0) + 1;
        Cache::put(self::CACHE_PREFIX . $provider . ':failures', $failures, self::TIMEOUT_SECONDS);

        if ($failures >= self::FAILURE_THRESHOLD) {
            Cache::put(self::CACHE_PREFIX . $provider . ':state', 'open', self::TIMEOUT_SECONDS);
            Cache::put(self::CACHE_PREFIX . $provider . ':opened_at', time(), self::TIMEOUT_SECONDS);
            Log::warning('AI Circuit Breaker: Circuit opened due to failures', [
                'provider' => $provider,
                'failures' => $failures,
            ]);
        }
    }

    /**
     * Get current failure count
     */
    public function getFailureCount(string $provider = 'default'): int
    {
        return Cache::get(self::CACHE_PREFIX . $provider . ':failures', 0);
    }

    /**
     * Get current state
     */
    public function getState(string $provider = 'default'): string
    {
        return Cache::get(self::CACHE_PREFIX . $provider . ':state', 'closed');
    }
}

