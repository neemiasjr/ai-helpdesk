<?php

namespace App\Services\Ai;

interface AiClientInterface
{
    /**
     * @return array{content:string, raw:mixed}
     */
    public function chat(string $system, string $user, array $options = []): array;
}
