<?php

namespace App\Jobs\Ai;

use App\Models\Ticket;
use App\Services\Ai\Prompts\TicketPrompts;

class ClassifyTicketJob extends BaseAiTicketJob
{
    protected function systemPrompt(Ticket $ticket): string
    {
        return TicketPrompts::classifySystem();
    }

    protected function userPrompt(Ticket $ticket): string
    {
        return TicketPrompts::classifyUser($ticket);
    }
}
