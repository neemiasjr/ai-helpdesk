<?php

namespace App\Domain\Ai\Repositories;

use App\Models\AiRun;
use App\Models\Ticket;

interface AiRunRepositoryInterface
{
    public function create(array $data): AiRun;

    public function find(int $id): ?AiRun;

    public function update(AiRun $run, array $data): bool;

    public function findByTicketAndType(Ticket $ticket, string $type): ?AiRun;

    public function getSuccessfulSummariesForTicket(Ticket $ticket): \Illuminate\Database\Eloquent\Collection;
}

