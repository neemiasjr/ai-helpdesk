<?php

namespace App\Domain\Tickets\Repositories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TicketRepositoryInterface
{
    public function find(int $id): ?Ticket;

    public function create(array $data): Ticket;

    public function update(Ticket $ticket, array $data): bool;

    public function delete(Ticket $ticket): bool;

    public function paginateForUser(User $user, array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findWithRelations(int $id, array $relations = []): ?Ticket;

    public function searchByTitle(string $search, ?User $user = null): Collection;
}

