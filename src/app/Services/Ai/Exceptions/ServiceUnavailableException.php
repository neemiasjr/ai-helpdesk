<?php

namespace App\Services\Ai\Exceptions;

/**
 * Exception thrown when AI service is temporarily unavailable
 */
class ServiceUnavailableException extends AiException
{
    public function __construct(
        string $message = "AI service is temporarily unavailable. Please try again later.",
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 503, $previous, 503, 'service_unavailable');
    }
}

