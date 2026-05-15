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
        // 1. Création des Provinces
        $provinces = ['Haut-Katanga', 'Lualaba', 'Kinshasa', 'Kongo-Central', 'Nord-Kivu', 'Ituri'];
        foreach ($provinces as $name) {
            Province::firstOrCreate(['name' => $name]);
        }

        $hkId = Province::where('name', 'Haut-Katanga')->first()->id;
        $kcId = Province::where('name', 'Kongo-Central')->first()->id;

        // 2. Bureaux de Douane (Extraits de "Bureaux de douane.pdf")
        // Les coordonnées sont simulées sur base des localisations standards de ces bureaux
        $bureaux = [
            ['province_id' => $hkId, 'code_bureau' => '701B', 'name' => 'NIK INTERNATIONAL / SOCODAM / EP VILLE', 'latitude' => -11.670, 'longitude' => 27.475],
            ['province_id' => $hkId, 'code_bureau' => '702B', 'name' => 'DHL / AERO LUANO', 'latitude' => -11.591, 'longitude' => 27.530],
            ['province_id' => $hkId, 'code_bureau' => '705B', 'name' => 'KASUMBALESA DOUANE', 'latitude' => -12.253, 'longitude' => 27.801],
            ['province_id' => $hkId, 'code_bureau' => '726B', 'name' => 'BOLLORE LOGISTICS', 'latitude' => -11.675, 'longitude' => 27.480],
            ['province_id' => $hkId, 'code_bureau' => '725B', 'name' => 'CONNEX AFRICA / COMIKA', 'latitude' => -11.680, 'longitude' => 27.485],
            ['province_id' => $hkId, 'code_bureau' => '729B', 'name' => 'CARGO CONGO', 'latitude' => -11.685, 'longitude' => 27.490],
            ['province_id' => $hkId, 'code_bureau' => '735B', 'name' => 'AMICONGO', 'latitude' => -11.690, 'longitude' => 27.495],
            ['province_id' => $hkId, 'code_bureau' => '717B', 'name' => 'PETROLE (MOGAS / INTERPETROL / SEP CONGO)', 'latitude' => -11.700, 'longitude' => 27.500],
            ['province_id' => $kcId, 'code_bureau' => '201B', 'name' => 'MATADI PORT', 'latitude' => -5.826, 'longitude' => 13.450],
        ];

        foreach ($bureaux as $b) {
            CustomsOffice::updateOrCreate(['code_bureau' => $b['code_bureau']], $b + ['gps_required' => true]);
        }

        // 3. Exonérations (Extraites de "ARRETE_INTERMINISTERIEL...0001.pdf")
        $exemptions = [
            // Produits de première nécessité (Annexe I)
            ['code_sh' => '02.01', 'designation' => 'Viandes des animaux de l\'espèce bovine, fraîche ou réfrigérés'],
            ['code_sh' => '02.02', 'designation' => 'Viandes des animaux de l\'espèce bovine, congelées'],
            ['code_sh' => '0303.23.00', 'designation' => 'Tilapias Oreochromis s, congelés'],
            ['code_sh' => '0303.55.00', 'designation' => 'Chinchards, congelés'],
            ['code_sh' => '04.02', 'designation' => 'Lait en granulés ou sous formes solides'],
            ['code_sh' => '10.06', 'designation' => 'Riz (sauf riz en paille)'],
            ['code_sh' => '15.11', 'designation' => 'Huile de palme et ses fractions'],
            ['code_sh' => '17.01.99.00', 'designation' => 'Autres sucres'],
            ['code_sh' => '1901.90.91', 'designation' => 'Préparations alimentaires à base de lait'],
            ['code_sh' => '2501.00.10', 'designation' => 'Sel iodé'],
            ['code_sh' => '3401.19.10', 'designation' => 'Savons ordinaires de ménage'],
            ['code_sh' => '2521.00.00', 'designation' => 'Castines; pierres à chaux ou à ciment'],
            ['code_sh' => '27.01', 'designation' => 'Houilles et combustibles solides similaires'],

            // Intrants et équipements agricoles (Annexe II)
            ['code_sh' => '0101.30.10', 'designation' => 'Anes reproducteurs de race pure'],
            ['code_sh' => '0102.21.00', 'designation' => 'Bovins domestiques reproducteurs de race pure'],
            ['code_sh' => '0105.11.10', 'designation' => 'Poussins d\'un jour'],
            ['code_sh' => '0701.10.00', 'designation' => 'Pomme de terre de semence'],
            ['code_sh' => '1005.10.00', 'designation' => 'Maïs de semence'],
            ['code_sh' => '3002.42.00', 'designation' => 'Vaccins pour la médecine vétérinaire'],
            ['code_sh' => '3102.10.00', 'designation' => 'Urée (engrais azotés)'],
            ['code_sh' => '3808.92.00', 'designation' => 'Fongicides'],
            ['code_sh' => '3808.93.00', 'designation' => 'Herbicides et régulateurs de croissance'],
            ['code_sh' => '8201.10.00', 'designation' => 'Bêches et pelles'],
            ['code_sh' => '8201.30.00', 'designation' => 'Pioches, pics, houes et râteaux'],
            ['code_sh' => '8701.10.10', 'designation' => 'Motoculteurs'],
            ['code_sh' => '8701.92.10', 'designation' => 'Tracteurs agricoles (> 18 kW)'],
        ];

        foreach ($exemptions as $ex) {
            Exemption::updateOrCreate(['code_sh' => $ex['code_sh']], $ex);
        }
    }
}