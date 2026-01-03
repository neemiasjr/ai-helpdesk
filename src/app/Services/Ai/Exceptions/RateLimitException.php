<?php

namespace App\Services\Ai\Exceptions;

/**
 * Exception thrown when API rate limit is exceeded
 */
class RateLimitException extends AiException
{
    public function __construct(
        string $message = "Rate limit exceeded. Please try again later.",
        public readonly ?int $retryAfter = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 429, $previous, 429, 'rate_limit');
    }
}

