<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\AiTicketService;
use Illuminate\Http\Request;

class AiTicketController extends Controller
{
    public function __construct(
        private AiTicketService $aiTicketService
    ) {
    }

    public function summarize(Request $request, Ticket $ticket)
    {
        $this->authorize('useAi', $ticket);

        $this->aiTicketService->queueSummarize($ticket, $request->user());

        return back();
    }

    public function classify(Request $request, Ticket $ticket)
    {
        $this->authorize('useAi', $ticket);

        $this->aiTicketService->queueClassify($ticket, $request->user());

        return back();
    }

    public function suggestReply(Request $request, Ticket $ticket)
    {
        $this->authorize('useAi', $ticket);

        $this->aiTicketService->queueSuggestReply($ticket, $request->user());

        return back();
    }
}
