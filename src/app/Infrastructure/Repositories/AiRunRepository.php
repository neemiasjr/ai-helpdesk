<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Ai\Repositories\AiRunRepositoryInterface;
use App\Models\AiRun;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;

class AiRunRepository implements AiRunRepositoryInterface
{
    public function create(array $data): AiRun
    {
        return AiRun::create($data);
    }

    public function find(int $id): ?AiRun
    {
        return AiRun::find($id);
    }

    public function update(AiRun $run, array $data): bool
    {
        return $run->update($data);
    }

    public function findByTicketAndType(Ticket $ticket, string $type): ?AiRun
    {
        return AiRun::where('entity_type', Ticket::class)
            ->where('entity_id', $ticket->id)
            ->where('run_type', $type)
            ->latest()
            ->first();
    }

    public function getSuccessfulSummariesForTicket(Ticket $ticket): Collection
    {
        return AiRun::where('entity_type', Ticket::class)
            ->where('entity_id', $ticket->id)
            ->where('run_type', 'summarize')
            ->where('status', 'success')
            ->latest()
            ->get();
    }
}

