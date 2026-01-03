<?php

namespace App\Jobs\Ai;

use App\Models\AiRun;
use App\Models\Ticket;
use App\Services\Ai\AiManager;
use App\Services\Ai\CircuitBreaker;
use App\Services\Ai\Exceptions\AiException;
use App\Services\Ai\Exceptions\InvalidApiKeyException;
use App\Services\Ai\Exceptions\MissingApiKeyException;
use App\Services\Ai\Exceptions\RateLimitException;
use App\Services\Ai\RateLimiter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

abstract class BaseAiTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff;

    public function __construct(public int $aiRunId, public int $ticketId)
    {
        $this->tries = config('ai.max_retries', 3) + 1;
        $this->backoff = config('ai.retry_delay_seconds', 1);
    }

    abstract protected function systemPrompt(Ticket $ticket): string;
    abstract protected function userPrompt(Ticket $ticket): string;

    public function handle(AiManager $ai, CircuitBreaker $circuitBreaker, RateLimiter $rateLimiter): void
    {
        $run = AiRun::findOrFail($this->aiRunId);
        $ticket = Ticket::findOrFail($this->ticketId);
        $provider = config('ai.provider', 'openai');

        // Check circuit breaker
        if (config('ai.circuit_breaker_enabled', true) && $circuitBreaker->isOpen($provider)) {
            $run->update([
                'status' => 'failed',
                'error_message' => 'Circuit breaker is open. Service temporarily unavailable.',
            ]);
            Log::warning('AI Job: Circuit breaker is open', [
                'run_id' => $run->id,
                'provider' => $provider,
            ]);
            return;
        }

        // Check rate limit
        if (config('ai.rate_limit_enabled', true)) {
            $maxRequests = config('ai.rate_limit_max_requests', 60);
            $windowSeconds = config('ai.rate_limit_window_seconds', 60);
            
            if (!$rateLimiter->attempt($provider, $maxRequests, $windowSeconds)) {
                $run->update([
                    'status' => 'failed',
                    'error_message' => 'Rate limit exceeded. Please try again later.',
                ]);
                Log::warning('AI Job: Rate limit exceeded', [
                    'run_id' => $run->id,
                    'provider' => $provider,
                ]);
                return;
            }
        }

        $start = hrtime(true);
        $attempt = (int)$run->attempt + 1;
        $run->update(['status' => 'running', 'attempt' => $attempt]);

        $system = $this->systemPrompt($ticket);
        $user = $this->userPrompt($ticket);

        $run->update(['prompt' => "SYSTEM:\n{$system}\n\nUSER:\n{$user}"]);

        try {
            $out = $ai->client()->chat($system, $user);

            // Validate response
            $this->validateResponse($out['content']);

            $durationMs = (int) ((hrtime(true) - $start) / 1_000_000);

            $run->update([
                'status' => 'success',
                'response' => $out['content'],
                'duration_ms' => $durationMs,
                'provider' => config('ai.provider'),
                'model' => config('ai.model'),
                'error_message' => null,
            ]);

            // Record success in circuit breaker
            if (config('ai.circuit_breaker_enabled', true)) {
                $circuitBreaker->recordSuccess($provider);
            }

            Log::info('AI Job: Success', [
                'run_id' => $run->id,
                'run_type' => $run->run_type,
                'duration_ms' => $durationMs,
                'attempt' => $attempt,
            ]);
        } catch (AiException $e) {
            $durationMs = (int) ((hrtime(true) - $start) / 1_000_000);

            $run->update([
                'status' => 'failed',
                'duration_ms' => $durationMs,
                'error_message' => $e->getMessage(),
            ]);

            // Record failure in circuit breaker
            if (config('ai.circuit_breaker_enabled', true)) {
                $circuitBreaker->recordFailure($provider);
            }

            Log::error('AI Job: Failed', [
                'run_id' => $run->id,
                'run_type' => $run->run_type,
                'error_type' => $e->errorType,
                'status_code' => $e->statusCode,
                'message' => $e->getMessage(),
                'attempt' => $attempt,
            ]);

            // Don't retry on API key errors (missing or invalid)
            if ($e instanceof InvalidApiKeyException || $e instanceof MissingApiKeyException) {
                $run->update([
                    'error_message' => $e->getMessage() . ' Configure a chave de API no arquivo .env.',
                ]);
                $this->fail($e);
                return;
            }

            // For rate limits, release back to queue with delay
            if ($e instanceof RateLimitException && $e->retryAfter) {
                $this->release($e->retryAfter);
                return;
            }

            throw $e;
        } catch (\Throwable $e) {
            $durationMs = (int) ((hrtime(true) - $start) / 1_000_000);

            $run->update([
                'status' => 'failed',
                'duration_ms' => $durationMs,
                'error_message' => $e->getMessage(),
            ]);

            if (config('ai.circuit_breaker_enabled', true)) {
                $circuitBreaker->recordFailure($provider);
            }

            Log::error('AI Job: Unexpected error', [
                'run_id' => $run->id,
                'run_type' => $run->run_type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attempt' => $attempt,
            ]);

            throw $e;
        }
    }

    /**
     * Calculate backoff delay in seconds (exponential backoff)
     */
    public function backoff(): array
    {
        $baseDelay = config('ai.retry_delay_seconds', 1);
        $delays = [];
        
        for ($i = 0; $i < $this->tries - 1; $i++) {
            $delays[] = $baseDelay * pow(2, $i);
        }
        
        return $delays;
    }

    /**
     * Validate AI response
     */
    protected function validateResponse(string $content): void
    {
        if (!config('ai.validate_responses', true)) {
            return;
        }

        $minLength = config('ai.min_response_length', 10);
        $maxLength = config('ai.max_response_length', 50000);

        if (strlen($content) < $minLength) {
            throw new \App\Services\Ai\Exceptions\InvalidResponseException(
                "Response too short (minimum {$minLength} characters)"
            );
        }

        if (strlen($content) > $maxLength) {
            throw new \App\Services\Ai\Exceptions\InvalidResponseException(
                "Response too long (maximum {$maxLength} characters)"
            );
        }
    }
}

