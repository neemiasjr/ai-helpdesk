<?php

namespace App\Services\Ai\Exceptions;

/**
 * Exception thrown when API key is missing (not configured)
 */
class MissingApiKeyException extends InvalidApiKeyException
{
    public function __construct(
        string $message = "API key não configurada. Configure a variável AI_API_KEY no arquivo .env",
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $previous);
    }
}

