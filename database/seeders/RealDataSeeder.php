<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\CustomsOffice;
use App\Models\Exemption;

class RealDataSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = ['Haut-Katanga', 'Lualaba', 'Kinshasa', 'Kongo-Central', 'Nord-Kivu', 'Ituri'];
        foreach ($provinces as $name) {
            Province::create(['name' => $name]);
        }

        $hkId = Province::query()->where('name', 'Haut-Katanga')->first()->id;
        $kcId = Province::query()->where('name', 'Kongo-Central')->first()->id;

        $bureaux = [
            ['province_id' => $hkId, 'code_bureau' => '701B', 'name' => 'NIK INTERNATIONAL / SOCODAM', 'latitude' => -11.670, 'longitude' => 27.475],
            ['province_id' => $hkId, 'code_bureau' => '702B', 'name' => 'DHL / AERO LUANO', 'latitude' => -11.591, 'longitude' => 27.530],
            ['province_id' => $hkId, 'code_bureau' => '705B', 'name' => 'KASUMBALESA DOUANE', 'latitude' => -12.253, 'longitude' => 27.801],
            ['province_id' => $kcId, 'code_bureau' => '201B', 'name' => 'MATADI PORT', 'latitude' => -5.826, 'longitude' => 13.450],
        ];

        foreach ($bureaux as $b) {
            CustomsOffice::create($b + ['gps_required' => true]);
        }

        $exemptions = [
            ['code_sh' => '1108.11.10', 'designation' => 'Amidon de froment'],
            ['code_sh' => '2936.21.00', 'designation' => 'Vitamine A'],
            ['code_sh' => '3004.10.00', 'designation' => 'Pénicillines'],
        ];

        foreach ($exemptions as $ex) {
            Exemption::create($ex);
        }
    }
}