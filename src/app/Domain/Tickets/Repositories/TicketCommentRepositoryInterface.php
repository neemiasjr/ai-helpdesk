<?php

namespace App\Domain\Tickets\Repositories;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;

interface TicketCommentRepositoryInterface
{
    public function create(array $data): TicketComment;

    public function findByTicket(Ticket $ticket): \Illuminate\Database\Eloquent\Collection;

    public function find(int $id): ?TicketComment;

    public function delete(TicketComment $comment): bool;
}

