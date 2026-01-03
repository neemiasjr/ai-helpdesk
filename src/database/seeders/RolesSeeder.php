<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'tickets.view', 'tickets.create', 'tickets.update', 'tickets.assign', 'tickets.close',
            'tickets.comment',
            'ai.use',
            'admin.manage',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $agent = Role::firstOrCreate(['name' => 'agent']);
        $customer = Role::firstOrCreate(['name' => 'customer']);

        $admin->syncPermissions($perms);

        $agent->syncPermissions([
            'tickets.view','tickets.update','tickets.assign','tickets.close','tickets.comment','ai.use'
        ]);

        $customer->syncPermissions([
            'tickets.view','tickets.create','tickets.comment','ai.use'
        ]);
    }
}
