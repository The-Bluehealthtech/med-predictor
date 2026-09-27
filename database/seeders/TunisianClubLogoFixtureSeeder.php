<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TunisianClubLogoFixtureSeeder extends Seeder
{
    public function run(): void
    {
        $logos = [
            'Club Africain' => 'club-africain',
            'CS Sfaxien' => 'cs-sfaxien',
            'CA Bizertin' => 'club-athletique-bizertin',
            'JS Kairouan' => 'js-kairouanaise',
            'AS Gabès' => 'avenir-sportif-de-gabes',
            'Olympique de Béja' => 'olympique-beja',
            'AS Marsa' => 'avenir-sportif-de-la-marsa',
            'ES Métlaoui' => 'etoile-sportive-de-metlaoui',
            'Espérance Sportive de Tunis' => 'esperance-sportive-de-tunis',
            'Etoile Sportive du Sahel' => 'etoile-sportive-du-sahel',
            'Etoile Sportive de Metlaoui' => 'etoile-sportive-de-metlaoui',
            'Stade Tunisien' => 'stade-tunisien',
            'US Monastir' => 'us-monastir',
            'US Ben Guerdane' => 'us-ben-guerdane',
            'JS Kairouanaise' => 'js-kairouanaise',
            'Olympique Béja' => 'olympique-beja',
            'ES Zarzis' => 'es-zarzis',
            'Club Athlétique Bizertin' => 'club-athletique-bizertin',
            'Avenir Sportif de Gabès' => 'avenir-sportif-de-gabes',
            'Avenir Sportif de La Marsa' => 'avenir-sportif-de-la-marsa',
            'Jeunesse Sportive d’El Omrane' => 'jeunesse-sportive-d-el-omrane',
            'AS Soliman' => 'as-soliman',
        ];
        $updated = 0;
        foreach ($logos as $name => $slug) {
            $updated += DB::table('clubs')->where('name', $name)->update([
                'logo_url' => "https://assets.footylogos.com/previews/{$slug}/{$slug}-logo-footylogos-320.webp",
                'updated_at' => now(),
            ]);
        }
        $this->command?->info("Logos des clubs tunisiens mis à jour : {$updated}.");
    }
}
