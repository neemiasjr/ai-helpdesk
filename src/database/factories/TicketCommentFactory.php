<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketComment>
 */
class TicketCommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = \App\Models\TicketComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bodies = [
            'Obrigado pelo relatório. Vou investigar isso imediatamente.',
            'Consegui reproduzir o problema. Estou trabalhando em uma correção.',
            'Atualizei o status do ticket. A correção será incluída na próxima versão.',
            'Você poderia fornecer mais detalhes sobre quando isso acontece?',
            'Encontrei a causa raiz do problema. Está relacionado à configuração do servidor.',
            'A correção foi aplicada. Pode testar novamente?',
            'Vou escalar isso para a equipe de desenvolvimento.',
            'Consegui resolver temporariamente. A solução permanente está em desenvolvimento.',
            'Preciso de acesso ao seu ambiente para investigar melhor. Poderia me fornecer?',
            'Atualização: A correção está em teste. Deve ser liberada em breve.',
            'Enviei um email com instruções detalhadas. Confira sua caixa de entrada.',
            'O problema está relacionado a uma atualização recente. Estamos revertendo.',
            'Confirmado: Isso é um bug conhecido. Já está na nossa lista de prioridades.',
            'Solução temporária: Você pode contornar usando a funcionalidade X enquanto corrigimos.',
            'Status: Aguardando resposta do cliente para prosseguir.',
        ];

        $faker = $this->faker ?? app(\Faker\Generator::class);
        
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'body' => $faker->randomElement($bodies),
            'created_at' => fn (array $attributes) => $faker->dateTimeBetween('-20 days', 'now'),
            'updated_at' => fn (array $attributes) => $attributes['created_at'],
        ];
    }
}

