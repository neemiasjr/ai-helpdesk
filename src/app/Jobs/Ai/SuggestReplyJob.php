<?php

namespace App\Jobs\Ai;

use App\Models\Ticket;
use App\Services\Ai\Prompts\TicketPrompts;

class SuggestReplyJob extends BaseAiTicketJob
{
    protected function systemPrompt(Ticket $ticket): string
    {
        return TicketPrompts::suggestReplySystem();
    }

    protected function userPrompt(Ticket $ticket): string
    {
        return TicketPrompts::suggestReplyUser($ticket);
    }
}
