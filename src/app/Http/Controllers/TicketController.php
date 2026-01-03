<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tickets\StoreTicketRequest;
use App\Http\Requests\Tickets\UpdateTicketRequest;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function __construct(
        private TicketService $ticketService
    ) {
    }

    public function index(Request $request)
    {
        $filters = [
            'q' => $request->string('q')->toString(),
        ];

        $tickets = $this->ticketService->listTicketsForUser($request->user(), $filters, 10);

        return Inertia::render('Tickets/Index', [
            'filters' => $filters,
            'tickets' => $tickets,
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Ticket::class);

        return Inertia::render('Tickets/Create');
    }

    public function store(StoreTicketRequest $request)
    {
        $ticket = $this->ticketService->createTicket($request->user(), $request->validated());

        return redirect()->route('tickets.show', $ticket);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket = $this->ticketService->findTicketWithRelations($ticket->id);

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
            'can' => [
                'update' => $request->user()->can('update', $ticket),
                'useAi'  => $request->user()->can('useAi', $ticket),
            ],
        ]);
    }

    public function edit(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        return Inertia::render('Tickets/Edit', [
            'ticket' => $ticket,
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $this->ticketService->updateTicket($ticket, $request->validated());

        return redirect()->route('tickets.show', $ticket);
    }
}
