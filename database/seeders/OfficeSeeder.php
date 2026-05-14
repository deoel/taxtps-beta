<?php

namespace Database\Seeders;

use App\Models\CustomsOffice;
use App\Models\Province;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create provinces
        $hautKatanga = Province::firstOrCreate(
            ['name' => 'Haut-Katanga']
        );

        $ituri = Province::firstOrCreate(
            ['name' => 'Ituri']
        );

        // Create customs offices for Haut-Katanga
        CustomsOffice::firstOrCreate(
            ['code_bureau' => '701B'],
            [
                'province_id' => $hautKatanga->id,
                'name' => 'NIK INTERNATIONAL / SOCODAM / EP VILLE',
                'latitude' => -11.6692,
                'longitude' => 27.4992,
                'gps_required' => true,
            ]
        );

        CustomsOffice::firstOrCreate(
            ['code_bureau' => '702B'],
            [
                'province_id' => $hautKatanga->id,
                'name' => 'DHL / AERO LUANO',
                'latitude' => -11.6692,
                'longitude' => 27.4992,
                'gps_required' => true,
            ]
        );

        CustomsOffice::firstOrCreate(
            ['code_bureau' => '703B'],
            [
                'province_id' => $hautKatanga->id,
                'name' => 'SNCC',
                'latitude' => -11.6692,
                'longitude' => 27.4992,
                'gps_required' => true,
            ]
        );

        CustomsOffice::firstOrCreate(
            ['code_bureau' => '705B'],
            [
                'province_id' => $hautKatanga->id,
                'name' => 'KASUMBALESA (Frontière)',
                'latitude' => -11.2674,
                'longitude' => 26.8201,
                'gps_required' => true,
            ]
        );

        CustomsOffice::firstOrCreate(
            ['code_bureau' => '722B'],
            [
                'province_id' => $hautKatanga->id,
                'name' => 'WISKY / KBP EXPORT',
                'latitude' => -11.6692,
                'longitude' => 27.4992,
                'gps_required' => true,
            ]
        );

        // Create customs office for Ituri
        CustomsOffice::firstOrCreate(
            ['code_bureau' => '510B'],
            [
                'province_id' => $ituri->id,
                'name' => 'KASENYI',
                'latitude' => 2.9531,
                'longitude' => 29.2339,
                'gps_required' => true,
            ]
        );

        $this->command->info('Provinces et Bureaux de Douane créés avec succès!');
        $this->command->info('Bureaux créés:');
        $this->command->info('  - 701B (Haut-Katanga)');
        $this->command->info('  - 702B (Haut-Katanga)');
        $this->command->info('  - 703B (Haut-Katanga)');
        $this->command->info('  - 705B (Haut-Katanga)');
        $this->command->info('  - 722B (Haut-Katanga)');
        $this->command->info('  - 510B (Ituri)');
    }
}
