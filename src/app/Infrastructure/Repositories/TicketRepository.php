<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Tickets\Repositories\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TicketRepository implements TicketRepositoryInterface
{
    public function find(int $id): ?Ticket
    {
        return Ticket::find($id);
    }

    public function create(array $data): Ticket
    {
        return Ticket::create($data);
    }

    public function update(Ticket $ticket, array $data): bool
    {
        return $ticket->update($data);
    }

    public function delete(Ticket $ticket): bool
    {
        return $ticket->delete();
    }

    public function paginateForUser(User $user, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Ticket::query()->with(['creator', 'assignee', 'aiRuns' => function ($q) {
            $q->where('run_type', 'summarize')
              ->where('status', 'success')
              ->latest()
              ->limit(1);
        }]);

        // Filtro de busca por título
        if (isset($filters['q']) && !empty($filters['q'])) {
            $query->where('title', 'like', "%{$filters['q']}%");
        }

        // Filtro por status
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtro por prioridade
        if (isset($filters['priority']) && !empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Clientes só veem seus próprios tickets
        if ($user->hasRole('customer')) {
            $query->where('created_by', $user->id);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findWithRelations(int $id, array $relations = []): ?Ticket
    {
        $defaultRelations = ['creator', 'assignee', 'comments.author', 'aiRuns'];
        $relations = array_merge($defaultRelations, $relations);

        return Ticket::with($relations)->find($id);
    }

    public function searchByTitle(string $search, ?User $user = null): Collection
    {
        $query = Ticket::query()->where('title', 'like', "%{$search}%");

        if ($user && $user->hasRole('customer')) {
            $query->where('created_by', $user->id);
        }

        return $query->get();
    }
}

