<?php

namespace App\Services;

use App\Domain\Ai\Repositories\AiRunRepositoryInterface;
use App\Jobs\Ai\ClassifyTicketJob;
use App\Jobs\Ai\SuggestReplyJob;
use App\Jobs\Ai\SummarizeTicketJob;
use App\Models\Ticket;
use App\Models\User;

class AiTicketService
{
    public function __construct(
        private AiRunRepositoryInterface $aiRunRepository
    ) {
    }

    public function queueSummarize(Ticket $ticket, User $user): void
    {
        $this->queueAiRun($ticket, $user, 'summarize', SummarizeTicketJob::class);
    }

    public function queueClassify(Ticket $ticket, User $user): void
    {
        $this->queueAiRun($ticket, $user, 'classify', ClassifyTicketJob::class);
    }

    public function queueSuggestReply(Ticket $ticket, User $user): void
    {
        $this->queueAiRun($ticket, $user, 'suggest_reply', SuggestReplyJob::class);
    }

    private function queueAiRun(Ticket $ticket, User $user, string $type, string $jobClass): void
    {
        $run = $this->aiRunRepository->create([
            'run_type' => $type,
            'entity_type' => Ticket::class,
            'entity_id' => $ticket->id,
            'requested_by' => $user->id,
            'status' => 'queued',
            'provider' => config('ai.provider'),
            'model' => config('ai.model'),
        ]);

        dispatch(new $jobClass($run->id, $ticket->id))
            ->onQueue('ai');
    }
}

