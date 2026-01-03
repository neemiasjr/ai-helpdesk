<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $categories = [
            'Suporte Técnico',
            'Bug Report',
            'Feature Request',
            'Dúvida',
            'Problema de Acesso',
            'Integração',
            'Performance',
            null,
        ];

        $titles = [
            'Não consigo fazer login no sistema',
            'Erro ao processar pagamento',
            'Sugestão de melhoria na interface',
            'Problema com upload de arquivos',
            'Site está lento',
            'Preciso de ajuda para configurar integração',
            'Bug no relatório de vendas',
            'Como cancelar minha assinatura?',
            'Erro 500 ao acessar dashboard',
            'Dúvida sobre funcionalidade de exportação',
            'Problema com notificações por email',
            'Solicitação de novo recurso',
            'Falha na sincronização de dados',
            'Interface não está responsiva',
            'Erro ao gerar relatório PDF',
        ];

        $descriptions = [
            'Quando tento fazer login, recebo uma mensagem de erro dizendo que minhas credenciais estão incorretas, mas tenho certeza de que estão corretas. Já tentei resetar a senha mas o problema persiste.',
            'O processo de pagamento está falhando na etapa final. O cartão é válido e tem saldo, mas o sistema retorna erro genérico. Isso acontece há 3 dias.',
            'Seria muito útil se pudéssemos exportar os dados em formato Excel além do CSV. Isso facilitaria muito nossa análise de dados.',
            'Quando tento fazer upload de arquivos maiores que 5MB, o sistema retorna erro de timeout. Arquivos menores funcionam normalmente.',
            'O site está extremamente lento, especialmente nas páginas de relatórios. Isso está impactando nossa produtividade diária.',
            'Preciso integrar o sistema com nossa API interna. Existe alguma documentação disponível? Como devo proceder?',
            'O relatório de vendas está mostrando valores incorretos para o mês de dezembro. Os totais não batem com os dados individuais.',
            'Gostaria de saber como posso cancelar minha assinatura. Não encontrei essa opção nas configurações da conta.',
            'Ao acessar o dashboard principal, recebo erro 500. Isso acontece esporadicamente, mas está se tornando frequente.',
            'Tenho dúvidas sobre como funciona a exportação de dados. Os campos exportados são todos os disponíveis ou há alguma limitação?',
            'Não estou recebendo as notificações por email que deveria. Verifiquei a pasta de spam e as configurações, mas não encontrei o problema.',
            'Seria interessante ter uma funcionalidade de busca avançada com múltiplos filtros. Isso ajudaria muito na análise de dados.',
            'A sincronização de dados com o sistema externo está falhando há 2 dias. Os dados não estão sendo atualizados corretamente.',
            'A interface não está funcionando bem em dispositivos móveis. Os elementos ficam sobrepostos e é difícil navegar.',
            'Ao tentar gerar um relatório em PDF, o processo falha e retorna erro. Isso acontece apenas com relatórios grandes (mais de 100 páginas).',
        ];

        $faker = $this->faker ?? app(\Faker\Generator::class);
        
        return [
            'created_by' => User::factory(),
            'assigned_to' => null,
            'title' => $faker->randomElement($titles),
            'description' => $faker->randomElement($descriptions),
            'status' => $faker->randomElement($statuses),
            'priority' => $faker->randomElement($priorities),
            'category' => $faker->randomElement($categories),
            'created_at' => $faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => fn (array $attributes) => $faker->dateTimeBetween($attributes['created_at'], 'now'),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }
}

