<?php

namespace App\Domain\Tickets\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->can('admin.manage')) return true;

        if ($user->hasRole('customer')) {
            return $ticket->created_by === $user->id;
        }

        return true; // agent
    }

    public function create(User $user): bool
    {
        return $user->can('tickets.create');
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->can('admin.manage')) return true;
        if ($user->hasRole('agent')) return true;

        return $ticket->created_by === $user->id && in_array($ticket->status, ['open', 'in_progress'], true);
    }

    public function useAi(User $user, Ticket $ticket): bool
    {
        return $user->can('ai.use') && $this->view($user, $ticket);
    }
}
