<?php

namespace Database\Seeders;

use App\Models\CustomsOffice;
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

        $realAgents = [
            ['name' => 'MISSA MBALATINI', 'office' => '701B'],
            ['name' => 'TIBYOBEBA BINTI IDI SYLVIE', 'office' => '702B'],
            ['name' => 'FAIDA RASHIDI', 'office' => '726B'],
            ['name' => 'MATATA MUTOKE', 'office' => '725B'],
            ['name' => 'MALOBA KALALA', 'office' => '729B'],
            ['name' => 'KONGOLO KASONGO', 'office' => '717B'],
            ['name' => 'LIFENYYA ISSIAKA', 'office' => '721B'],
            ['name' => 'KAPENGA MFIMBO JULES', 'office' => '706B'],
            ['name' => 'PUNGU BEYA', 'office' => '705B'],
            ['name' => 'BUKABA WA BENGA BLAISE', 'office' => '722B'],
        ];

        foreach ($realAgents as $data) {
            $office = CustomsOffice::where('code_bureau', 'LIKE', '%' . $data['office'] . '%')->first();

            User::create([
                'name' => $data['name'],
                'email' => strtolower(str_replace(' ', '.', $data['name'])) . '@taxtps.cd',
                'password' => bcrypt('password'),
                'customs_office_id' => $office ? $office->id : null,
            ])->assignRole($agentRole);
        }
    }
}