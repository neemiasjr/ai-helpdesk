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

        // Busca inteligente em múltiplos campos
        if (isset($filters['q']) && !empty($filters['q'])) {
            // Se houver termos preparados (tokenizados/expandidos), usar eles
            if (isset($filters['search_terms']) && !empty($filters['search_terms'])) {
                $searchTerms = $filters['search_terms'];
                
                // Busca AND: todas as palavras devem estar presentes (em qualquer campo)
                $query->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $term = trim($term);
                        if (empty($term)) {
                            continue;
                        }
                        
                        // Cada palavra deve aparecer em pelo menos um campo (OR entre campos)
                        $q->where(function ($subQuery) use ($term) {
                            $subQuery->where('title', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%")
                                ->orWhere('category', 'like', "%{$term}%")
                                ->orWhereHas('comments', function ($commentQuery) use ($term) {
                                    $commentQuery->where('body', 'like', "%{$term}%");
                                });
                        });
                    }
                });
            } else {
                // Fallback: busca simples pela query original
                $searchTerm = trim($filters['q']);
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhere('category', 'like', "%{$searchTerm}%")
                        ->orWhereHas('comments', function ($commentQuery) use ($searchTerm) {
                            $commentQuery->where('body', 'like', "%{$searchTerm}%");
                        });
                });
            }
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

