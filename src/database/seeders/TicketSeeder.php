<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar usuários existentes
        $customers = User::role('customer')->get();
        $agents = User::role('agent')->get();
        $admins = User::role('admin')->get();

        if ($customers->isEmpty()) {
            $this->command->warn('Nenhum usuário com role "customer" encontrado. Criando tickets sem criar usuários adicionais.');
            $customers = collect();
        }

        if ($agents->isEmpty() && $admins->isEmpty()) {
            $this->command->warn('Nenhum agente ou admin encontrado para atribuir tickets.');
        }

        // Criar 50 tickets
        $tickets = collect();
        
        for ($i = 0; $i < 50; $i++) {
            $creator = $customers->isNotEmpty() 
                ? $customers->random() 
                : tap(User::factory()->create(), fn($u) => $u->assignRole('customer'));
            
            $assignee = null;
            if (fake()->boolean(60) && ($agents->isNotEmpty() || $admins->isNotEmpty())) {
                $assignee = $agents->isNotEmpty() ? $agents->random() : $admins->random();
            }

            $ticket = Ticket::factory()->create([
                'created_by' => $creator->id,
                'assigned_to' => $assignee?->id,
            ]);
            
            $tickets->push($ticket);
        }

        // Adicionar comentários aos tickets
        $tickets->each(function ($ticket) use ($agents, $admins) {
            $commentCount = fake()->numberBetween(0, 5);
            $baseDate = $ticket->created_at->copy();
            
            // Primeiro comentário sempre do criador
            if ($commentCount > 0) {
                TicketComment::factory()->create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $ticket->created_by,
                    'created_at' => $baseDate->copy()->addMinutes(fake()->numberBetween(5, 60)),
                ]);
            }

            // Comentários adicionais de agentes/admins
            for ($i = 1; $i < $commentCount; $i++) {
                $commenter = ($agents->isNotEmpty() || $admins->isNotEmpty())
                    ? ($agents->isNotEmpty() ? $agents->random() : $admins->random())
                    : User::all()->random();

                TicketComment::factory()->create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $commenter->id,
                    'created_at' => $baseDate->copy()->addDays(fake()->numberBetween(1, 10)),
                ]);
            }
        });

        $this->command->info('50 tickets criados com sucesso!');
        $this->command->info('Comentários adicionados aos tickets.');
    }
}

