<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\AiTicketController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('tickets.index'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('tickets.index'))->name('dashboard');
    
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');

    Route::post('/tickets/{ticket}/comments', [TicketCommentController::class, 'store'])
        ->name('tickets.comments.store');

    Route::post('/tickets/{ticket}/ai/summarize', [AiTicketController::class, 'summarize'])
        ->middleware(\App\Http\Middleware\CheckAiConfiguration::class)
        ->name('tickets.ai.summarize');

    Route::post('/tickets/{ticket}/ai/classify', [AiTicketController::class, 'classify'])
        ->middleware(\App\Http\Middleware\CheckAiConfiguration::class)
        ->name('tickets.ai.classify');

    Route::post('/tickets/{ticket}/ai/suggest-reply', [AiTicketController::class, 'suggestReply'])
        ->middleware(\App\Http\Middleware\CheckAiConfiguration::class)
        ->name('tickets.ai.suggestReply');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
