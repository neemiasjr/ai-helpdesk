<?php

namespace App\Http\Controllers;

use App\Jobs\Ai\ClassifyTicketJob;
use App\Jobs\Ai\SuggestReplyJob;
use App\Jobs\Ai\SummarizeTicketJob;
use App\Models\AiRun;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AiTicketController extends Controller
{
    private function queueRun(Request $request, Ticket $ticket, string $type, string $jobClass)
    {
        $this->authorize('useAi', $ticket);

        $run = AiRun::create([
            'run_type' => $type,
            'entity_type' => Ticket::class,
            'entity_id' => $ticket->id,
            'requested_by' => $request->user()->id,
            'status' => 'queued',
            'provider' => config('ai.provider'),
            'model' => config('ai.model'),
        ]);

        dispatch(new $jobClass($run->id, $ticket->id))
            ->onQueue('ai');

        return back();
    }

    public function summarize(Request $request, Ticket $ticket)
    {
        return $this->queueRun($request, $ticket, 'summarize', SummarizeTicketJob::class);
    }

    public function classify(Request $request, Ticket $ticket)
    {
        return $this->queueRun($request, $ticket, 'classify', ClassifyTicketJob::class);
    }

    public function suggestReply(Request $request, Ticket $ticket)
    {
        return $this->queueRun($request, $ticket, 'suggest_reply', SuggestReplyJob::class);
    }
}
