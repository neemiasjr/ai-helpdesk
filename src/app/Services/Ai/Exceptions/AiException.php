<?php

namespace App\Services\Ai\Exceptions;

use RuntimeException;

/**
 * Base exception for AI service errors
 */
class AiException extends RuntimeException
{
    public function __construct(
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null,
        public readonly ?int $statusCode = null,
        public readonly ?string $errorType = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

