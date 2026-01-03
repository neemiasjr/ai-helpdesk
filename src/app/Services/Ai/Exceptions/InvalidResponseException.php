<?php

namespace App\Services\Ai\Exceptions;

/**
 * Exception thrown when AI service returns an invalid or unexpected response
 */
class InvalidResponseException extends AiException
{
    public function __construct(
        string $message = "Invalid response from AI service.",
        public readonly ?array $responseData = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 500, $previous, 500, 'invalid_response');
    }
}

