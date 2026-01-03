<?php

namespace App\Services\Ai\Exceptions;

/**
 * Exception thrown when API key is invalid or missing
 */
class InvalidApiKeyException extends AiException
{
    public function __construct(
        string $message = "Invalid or missing API key.",
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 401, $previous, 401, 'invalid_api_key');
    }
}

