<?php

namespace App\Services\Ai;

use App\Services\Ai\Providers\OpenAiClient;

class AiManager
{
    public function client(): AiClientInterface
    {
        return match (config('ai.provider')) {
            'openai' => new OpenAiClient(),
            default => new OpenAiClient(),
        };
    }
}
