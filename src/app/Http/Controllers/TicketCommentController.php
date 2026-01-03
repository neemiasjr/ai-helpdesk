<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tickets\StoreTicketCommentRequest;
use App\Models\Ticket;
use App\Services\TicketCommentService;

class TicketCommentController extends Controller
{
    public function __construct(
        private TicketCommentService $commentService
    ) {
    }

    public function store(StoreTicketCommentRequest $request, Ticket $ticket)
    {
        $this->commentService->createComment($ticket, $request->user(), $request->validated()['body']);

        return back();
    }
}
