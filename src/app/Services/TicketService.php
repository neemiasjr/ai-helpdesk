<?php

namespace App\Services;

use App\Domain\Tickets\Enums\TicketPriority;
use App\Domain\Tickets\Enums\TicketStatus;
use App\Domain\Tickets\Repositories\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketService
{
    public function __construct(
        private TicketRepositoryInterface $ticketRepository,
        private SearchService $searchService
    ) {
    }

    public function listTicketsForUser(User $user, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        // Preparar termos de busca (tokenizar e expandir se necessário)
        if (isset($filters['q']) && !empty($filters['q'])) {
            $searchTerms = $this->searchService->prepareSearchTerms($filters['q']);
            $filters['search_terms'] = $searchTerms;
        }

        $tickets = $this->ticketRepository->paginateForUser($user, $filters, $perPage);

        // Adicionar sumarizações aos tickets
        $tickets->getCollection()->transform(function ($ticket) {
            $summarizeRun = $ticket->aiRuns->first();
            $ticket->summary = $summarizeRun?->response;
            return $ticket;
        });

        return $tickets;
    }

    public function createTicket(User $user, array $data): Ticket
    {
        return $this->ticketRepository->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'created_by' => $user->id,
            'status' => TicketStatus::OPEN->value,
            'priority' => TicketPriority::MEDIUM->value,
        ]);
    }

    public function updateTicket(Ticket $ticket, array $data): bool
    {
        return $this->ticketRepository->update($ticket, $data);
    }

    public function findTicketWithRelations(int $id, array $relations = []): ?Ticket
    {
        return $this->ticketRepository->findWithRelations($id, $relations);
    }

    public function deleteTicket(Ticket $ticket): bool
    {
        return $this->ticketRepository->delete($ticket);
    }
}

