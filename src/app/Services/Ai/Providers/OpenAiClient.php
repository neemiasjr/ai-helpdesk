<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\AiClientInterface;
use App\Services\Ai\Exceptions\InvalidApiKeyException;
use App\Services\Ai\Exceptions\InvalidResponseException;
use App\Services\Ai\Exceptions\MissingApiKeyException;
use App\Services\Ai\Exceptions\RateLimitException;
use App\Services\Ai\Exceptions\ServiceUnavailableException;
use App\Services\Ai\Exceptions\TimeoutException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiClient implements AiClientInterface
{
    /**
     * Send chat completion request to OpenAI API
     *
     * @param string $system System prompt
     * @param string $user User prompt
     * @param array $options Additional options (temperature, max_tokens, etc.)
     * @return array{content: string, raw: array}
     * @throws InvalidApiKeyException
     * @throws RateLimitException
     * @throws TimeoutException
     * @throws ServiceUnavailableException
     * @throws InvalidResponseException
     */
    public function chat(string $system, string $user, array $options = []): array
    {
        $apiKey = config('ai.api_key');
        if (empty($apiKey)) {
            throw new MissingApiKeyException(
                'API key não configurada. Configure a variável AI_API_KEY no arquivo .env para usar recursos de IA.'
            );
        }

        $model = config('ai.model', 'gpt-4o-mini');
        $timeout = (int) config('ai.timeout_seconds', 30);
        $defaultOptions = config('ai.default_options', []);

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ],
            'temperature' => $options['temperature'] ?? $defaultOptions['temperature'] ?? 0.2,
        ];

        if (isset($defaultOptions['max_tokens'])) {
            $payload['max_tokens'] = $defaultOptions['max_tokens'];
        }

        // Log request if enabled
        if (config('ai.log_requests', true)) {
            Log::info('AI API Request', [
                'provider' => 'openai',
                'model' => $model,
                'system_length' => strlen($system),
                'user_length' => strlen($user),
            ]);
        }

        try {
            $res = Http::timeout($timeout)
                ->withToken($apiKey)
                ->withHeaders([
                    'User-Agent' => 'AI-Helpdesk/1.0',
                ])
                ->post('https://api.openai.com/v1/chat/completions', $payload);

            $this->handleResponse($res, $payload);

            $json = $res->json();
            $content = data_get($json, 'choices.0.message.content', '');

            if (empty($content)) {
                throw new InvalidResponseException(
                    'Empty response from OpenAI API',
                    $json
                );
            }

            // Log response if enabled
            if (config('ai.log_responses', false)) {
                Log::debug('AI API Response', [
                    'provider' => 'openai',
                    'response_length' => strlen($content),
                    'usage' => data_get($json, 'usage'),
                ]);
            }

            return [
                'content' => (string) $content,
                'raw' => $json,
                'usage' => data_get($json, 'usage', []),
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new TimeoutException(
                'Connection timeout while communicating with OpenAI API',
                $e
            );
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $statusCode = $e->response?->status();
            throw $this->mapHttpException($e, $statusCode);
        }
    }

    /**
     * Handle HTTP response and throw appropriate exceptions
     */
    private function handleResponse($response, array $payload): void
    {
        if ($response->successful()) {
            return;
        }

        $statusCode = $response->status();
        $body = $response->body();
        $errorData = $response->json();

        throw match ($statusCode) {
            401 => new InvalidApiKeyException(
                'OpenAI API key is invalid or expired',
                $response->toException()
            ),
            429 => new RateLimitException(
                data_get($errorData, 'error.message', 'Rate limit exceeded'),
                $this->extractRetryAfter($response),
                $response->toException()
            ),
            408, 504 => new TimeoutException(
                'Request timeout while communicating with OpenAI API',
                $response->toException()
            ),
            503, 502, 500 => new ServiceUnavailableException(
                data_get($errorData, 'error.message', 'OpenAI service is temporarily unavailable'),
                $response->toException()
            ),
            default => new \App\Services\Ai\Exceptions\AiException(
                "OpenAI API error ({$statusCode}): " . data_get($errorData, 'error.message', $body),
                $statusCode,
                $response->toException(),
                $statusCode,
                data_get($errorData, 'error.type', 'unknown')
            ),
        };
    }

    /**
     * Map HTTP exceptions to custom AI exceptions
     */
    private function mapHttpException(\Throwable $e, ?int $statusCode): \App\Services\Ai\Exceptions\AiException
    {
        return match ($statusCode) {
            401 => new InvalidApiKeyException('Invalid API key', $e),
            429 => new RateLimitException('Rate limit exceeded', null, $e),
            408, 504 => new TimeoutException('Request timeout', $e),
            503, 502, 500 => new ServiceUnavailableException('Service unavailable', $e),
            default => new \App\Services\Ai\Exceptions\AiException(
                'Unknown error: ' . $e->getMessage(),
                0,
                $e,
                $statusCode
            ),
        };
    }

    /**
     * Extract Retry-After header value
     */
    private function extractRetryAfter($response): ?int
    {
        $retryAfter = $response->header('Retry-After');
        return $retryAfter ? (int) $retryAfter : null;
    }
}

