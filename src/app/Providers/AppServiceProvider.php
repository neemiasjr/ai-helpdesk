<?php

namespace App\Providers;

use App\Domain\Ai\Repositories\AiRunRepositoryInterface;
use App\Domain\Tickets\Policies\TicketPolicy;
use App\Domain\Tickets\Repositories\TicketCommentRepositoryInterface;
use App\Domain\Tickets\Repositories\TicketRepositoryInterface;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Infrastructure\Repositories\AiRunRepository;
use App\Infrastructure\Repositories\TicketCommentRepository;
use App\Infrastructure\Repositories\TicketRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Models\Ticket;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(TicketRepositoryInterface::class, TicketRepository::class);
        $this->app->bind(TicketCommentRepositoryInterface::class, TicketCommentRepository::class);
        $this->app->bind(AiRunRepositoryInterface::class, AiRunRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Gate::policy(Ticket::class, TicketPolicy::class);
    }
}
