<?php

namespace App\Services;

use App\Domain\Tickets\Repositories\TicketCommentRepositoryInterface;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;

class TicketCommentService
{
    public function __construct(
        private TicketCommentRepositoryInterface $commentRepository
    ) {
    }

    public function createComment(Ticket $ticket, User $user, string $body): TicketComment
    {
        return $this->commentRepository->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => $body,
        ]);
    }

    public function getCommentsForTicket(Ticket $ticket): \Illuminate\Database\Eloquent\Collection
    {
        return $this->commentRepository->findByTicket($ticket);
    }

    public function deleteComment(TicketComment $comment): bool
    {
        return $this->commentRepository->delete($comment);
    }
}

