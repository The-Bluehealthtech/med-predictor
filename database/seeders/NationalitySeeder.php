<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nationality;

class NationalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nationalities = [
            'Tunisie', 'Maroc', 'Algérie', 'Nigeria', 'Sénégal',
            'Côte d\'Ivoire', 'Mali', 'Brésil', 'France', 'Angleterre',
            'Espagne', 'Portugal', 'Allemagne', 'Italie', 'Belgique',
            'Pays-Bas', 'Argentine', 'Égypte', 'Ghana', 'Cameroun',
        ];

        foreach ($nationalities as $name) {
            Nationality::updateOrCreate(['name' => $name]);
        }

        $this->command->info('✅ ' . count($nationalities) . ' nationalités créées.');
    }
}
