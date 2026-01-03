<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Tickets\Repositories\TicketCommentRepositoryInterface;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Database\Eloquent\Collection;

class TicketCommentRepository implements TicketCommentRepositoryInterface
{
    public function create(array $data): TicketComment
    {
        return TicketComment::create($data);
    }

    public function findByTicket(Ticket $ticket): Collection
    {
        return TicketComment::where('ticket_id', $ticket->id)
            ->with('author')
            ->latest()
            ->get();
    }

    public function find(int $id): ?TicketComment
    {
        return TicketComment::find($id);
    }

    public function delete(TicketComment $comment): bool
    {
        return $comment->delete();
    }
}

