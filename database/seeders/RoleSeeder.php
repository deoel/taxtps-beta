<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            ['guard_name' => 'web']
        );

        $agentRole = Role::firstOrCreate(
            ['name' => 'agent'],
            ['guard_name' => 'web']
        );

        $this->command->info('Rôles créés avec succès: admin, manager, agent');
    }
}
