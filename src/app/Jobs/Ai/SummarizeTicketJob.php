<?php

namespace App\Jobs\Ai;

use App\Models\Ticket;
use App\Services\Ai\Prompts\TicketPrompts;

class SummarizeTicketJob extends BaseAiTicketJob
{
    protected function systemPrompt(Ticket $ticket): string
    {
        return TicketPrompts::summarizeSystem();
    }

    protected function userPrompt(Ticket $ticket): string
    {
        return TicketPrompts::summarizeUser($ticket);
    }
}
