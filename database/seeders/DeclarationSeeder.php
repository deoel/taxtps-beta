<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Declaration;
use App\Models\CustomsOffice;
use App\Models\Exemption;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;

class DeclarationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        $offices = CustomsOffice::all();
        $agents = User::role('agent')->pluck('id')->toArray();
        $exemptCodes = Exemption::query()->pluck('code_sh')->toArray();

        $statuts = ['conforme', 'alerte', 'fraude_suspectée', 'en_attente', 'suspect', 'valide', 'litige'];

        // On génère 20 itérations de 500 pour un total de 10 000 déclarations
        $totalBatches = 20;
        $recordsPerBatch = 500;

        $this->command->info("Génération de " . ($totalBatches * $recordsPerBatch) . " déclarations en Francs Congolais...");

        for ($j = 0; $j < $totalBatches; $j++) {
            $batch = [];
            for ($i = 0; $i < $recordsPerBatch; $i++) {
                $office = $offices->random();

                /** * MONTANTS EN CDF (Franc Congolais)
                 * Petit colis : 500 000 CDF
                 * Gros conteneur : 1 500 000 000 CDF (1.5 Milliard)
                 */
                $cif = $faker->randomFloat(2, 500000, 1500000000);

                $isExempt = $faker->boolean(15);
                $sh = $isExempt && !empty($exemptCodes)
                    ? $faker->randomElement($exemptCodes)
                    : $faker->numerify('####.##.##');

                // MODIFICATION : 70% de chance d'être 'conforme' pour avoir des points verts sur la carte
                $statut = $faker->boolean(70) ? 'conforme' : $faker->randomElement($statuts);

                // Score de priorité ajusté aux montants CDF
                $priority = 0;
                if ($cif > 500000000) $priority += 5; // > 500 millions CDF
                if (in_array($statut, ['alerte', 'fraude_suspectée', 'suspect'])) $priority += 10;

                $isSuspicious = in_array($statut, ['alerte', 'fraude_suspectée', 'suspect']);
                $gpsValidated = !$isSuspicious && $faker->boolean(85);

                $batch[] = [
                    'numero_dcl' => 'DCL-' . $faker->unique()->numberBetween(1000000, 9999999),
                    'customs_office_id' => $office->id,
                    'importateur' => $faker->company,
                    'code_sh' => $sh,
                    'montant_cif' => $cif,
                    'taxe_due' => $isExempt ? 0 : ($cif * 0.25),
                    'priority_score' => $priority,
                    'statut' => $statut,
                    'gps_validated' => $gpsValidated,
                    'latitude' => $isSuspicious ? $office->latitude + $faker->randomFloat(4, 0.1, 1.5) : $office->latitude,
                    'longitude' => $isSuspicious ? $office->longitude + $faker->randomFloat(4, 0.1, 1.5) : $office->longitude,
                    'agent_id' => !empty($agents) ? $faker->randomElement($agents) : null,
                    'created_at' => Carbon::now()->subDays($faker->numberBetween(0, 365))->subHours($faker->numberBetween(0, 23)),
                    'updated_at' => now(),
                ];
            }
            Declaration::insert($batch);
            $this->command->comment("Batch " . ($j + 1) . "/$totalBatches inséré...");
        }

        $this->command->info("Seeder terminé avec succès !");
    }
}