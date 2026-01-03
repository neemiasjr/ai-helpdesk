<?php

namespace App\Services\Ai\Exceptions;

/**
 * Exception thrown when API request times out
 */
class TimeoutException extends AiException
{
    public function __construct(
        string $message = "Request timeout. The AI service took too long to respond.",
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 408, $previous, 408, 'timeout');
    }
}

