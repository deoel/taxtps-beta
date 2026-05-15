<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Declaration;
use App\Models\CustomsOffice;
use App\Models\Exemption;
use App\Models\User;
use Faker\Factory as Faker;

class DeclarationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        $offices = CustomsOffice::all();
        $agent = User::role('agent')->first();
        $exemptCodes = Exemption::query()->pluck('code_sh')->toArray();

        for ($j = 0; $j < 4; $j++) {
            $batch = [];
            for ($i = 0; $i < 500; $i++) {
                $office = $offices->random();
                $cif = $faker->randomFloat(2, 2000, 500000);
                $isExempt = $faker->boolean(15);
                $sh = $isExempt ? $faker->randomElement($exemptCodes) : $faker->numerify('####.##.##');

                $isFraud = $faker->boolean(5);

                $priority = 0;
                if ($cif > 100000) $priority += 3;
                if ($isFraud) $priority += 3;
                
                $batch[] = [
                    'numero_dcl' => 'DCL-' . $faker->unique()->numberBetween(100000, 999999),
                    'customs_office_id' => $office->id,
                    'importateur' => $faker->company,
                    'code_sh' => $sh,
                    'montant_cif' => $cif,
                    'taxe_due' => $isExempt ? 0 : ($cif * 0.02),
                    'priority_score' => $priority,
                    'statut' => $isFraud ? 'alerte' : 'conforme',
                    'gps_validated' => !$isFraud,
                    'latitude' => $isFraud ? $office->latitude + 1.5 : $office->latitude,
                    'longitude' => $isFraud ? $office->longitude + 1.5 : $office->longitude,
                    'agent_id' => $agent->id,
                    'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                    'updated_at' => now(),
                ];
            }
            Declaration::insert($batch);
        }
    }
}