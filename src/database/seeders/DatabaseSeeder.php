<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
        ]);

        // Criar usuários padrão
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $agent = User::create([
            'name' => 'Agent',
            'email' => 'agent@example.com',
            'password' => bcrypt('password'),
        ]);
        $agent->assignRole('agent');

        $customer = User::create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
        ]);
        $customer->assignRole('customer');

        // Criar usuários adicionais para testes
        $agents = User::factory()->count(3)->create();
        foreach ($agents as $a) {
            $a->assignRole('agent');
        }

        $customers = User::factory()->count(10)->create();
        foreach ($customers as $c) {
            $c->assignRole('customer');
        }

        // Criar tickets de teste
        $this->call([
            TicketSeeder::class,
        ]);
    }
}
