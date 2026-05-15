<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $agentRole = Role::create(['name' => 'agent']);

        User::create([
            'name' => 'Superviseur National',
            'email' => 'admin@taxtps.cd',
            'password' => bcrypt('password'),
        ])->assignRole($admin);

        User::create([
            'name' => 'Manager Katanga',
            'email' => 'manager.katanga@taxtps.cd',
            'password' => bcrypt('password'),
            'customs_office_id' => 1,
        ])->assignRole($manager);

        User::create([
            'name' => 'Agent Terrain',
            'email' => 'agent@taxtps.cd',
            'password' => bcrypt('password'),
            'customs_office_id' => 3,
        ])->assignRole($agentRole);
    }
}