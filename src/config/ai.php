<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Supported providers: openai
    */
    'provider' => env('AI_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */
    'api_key' => env('AI_API_KEY'),
    'model' => env('AI_MODEL', 'gpt-4o-mini'),
    'timeout_seconds' => (int) env('AI_TIMEOUT_SECONDS', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Number of retry attempts for failed requests
    | Retry backoff strategy: exponential (1s, 2s, 4s, ...)
    */
    'max_retries' => (int) env('AI_MAX_RETRIES', 3),
    'retry_delay_seconds' => (int) env('AI_RETRY_DELAY_SECONDS', 1),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Prevent exceeding API rate limits
    | Requests per window: 60 requests per 60 seconds by default
    */
    'rate_limit_enabled' => env('AI_RATE_LIMIT_ENABLED', true),
    'rate_limit_max_requests' => (int) env('AI_RATE_LIMIT_MAX_REQUESTS', 60),
    'rate_limit_window_seconds' => (int) env('AI_RATE_LIMIT_WINDOW_SECONDS', 60),

    /*
    |--------------------------------------------------------------------------
    | Circuit Breaker
    |--------------------------------------------------------------------------
    |
    | Circuit breaker prevents cascading failures
    */
    'circuit_breaker_enabled' => env('AI_CIRCUIT_BREAKER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Response Validation
    |--------------------------------------------------------------------------
    |
    | Validate AI responses before saving
    */
    'validate_responses' => env('AI_VALIDATE_RESPONSES', true),
    'min_response_length' => (int) env('AI_MIN_RESPONSE_LENGTH', 10),
    'max_response_length' => (int) env('AI_MAX_RESPONSE_LENGTH', 50000),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Log AI API calls for debugging and monitoring
    */
    'log_requests' => env('AI_LOG_REQUESTS', true),
    'log_responses' => env('AI_LOG_RESPONSES', false), // Set to true for full response logging

    /*
    |--------------------------------------------------------------------------
    | Default Options
    |--------------------------------------------------------------------------
    |
    | Default options passed to AI provider
    */
    'default_options' => [
        'temperature' => (float) env('AI_TEMPERATURE', 0.2),
        'max_tokens' => (int) env('AI_MAX_TOKENS', 2000),
    ],
];
