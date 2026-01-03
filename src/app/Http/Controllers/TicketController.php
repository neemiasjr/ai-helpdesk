<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query()->with(['creator', 'assignee', 'aiRuns' => function ($q) {
            $q->where('run_type', 'summarize')
              ->where('status', 'success')
              ->latest()
              ->limit(1);
        }]);

        if ($s = $request->string('q')->toString()) {
            $query->where('title', 'like', "%{$s}%");
        }

        if ($request->user()->hasRole('customer')) {
            $query->where('created_by', $request->user()->id);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        // Adicionar sumarizações aos tickets
        $tickets->getCollection()->transform(function ($ticket) {
            $summarizeRun = $ticket->aiRuns->first();
            $ticket->summary = $summarizeRun?->response;
            return $ticket;
        });

        return Inertia::render('Tickets/Index', [
            'filters' => ['q' => $request->string('q')->toString()],
            'tickets' => $tickets,
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Ticket::class);

        return Inertia::render('Tickets/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Ticket::class);

        $data = $request->validate([
            'title' => ['required','string','max:200'],
            'description' => ['required','string','max:20000'],
        ]);

        $ticket = Ticket::create([
            ...$data,
            'created_by' => $request->user()->id,
            'status' => 'open',
            'priority' => 'medium',
        ]);

        return redirect()->route('tickets.show', $ticket);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['creator','assignee','comments.author','aiRuns']);

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

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $data = $request->validate([
            'title' => ['required','string','max:200'],
            'description' => ['required','string','max:20000'],
            'status' => ['required','in:open,in_progress,resolved,closed'],
            'priority' => ['required','in:low,medium,high,urgent'],
            'category' => ['nullable','string','max:120'],
        ]);

        $ticket->update($data);

        return redirect()->route('tickets.show', $ticket);
    }
}
