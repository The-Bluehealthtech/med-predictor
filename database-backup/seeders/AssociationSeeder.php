<?php

namespace Database\Seeders;

use App\Models\Association;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssociationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('associations')->truncate();

        $associations = [
            [
                'name' => 'The Football Association',
                'short_name' => 'FA',
                'country' => 'England',
                'confederation' => 'UEFA',
                'fifa_ranking' => 4,
                'association_logo_url' => 'https://cdn.thefa.com/thefawebsite/assets/images/the-fa-logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg',
                'fifa_version' => 'FIFA 24',
                'last_updated' => now(),
            ],
            [
                'name' => 'Fédération Française de Football',
                'short_name' => 'FFF',
                'country' => 'France',
                'confederation' => 'UEFA',
                'fifa_ranking' => 2,
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/French_Football_Federation_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/c/c3/Flag_of_France.svg',
                'fifa_version' => 'FIFA 24',
                'last_updated' => now(),
            ],
            [
                'name' => 'Deutscher Fußball-Bund',
                'short_name' => 'DFB',
                'country' => 'Germany',
                'confederation' => 'UEFA',
                'fifa_ranking' => 16,
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/5a/DFB_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/b/ba/Flag_of_Germany.svg',
                'fifa_version' => 'FIFA 24',
                'last_updated' => now(),
            ],
            [
                'name' => 'Real Federación Española de Fútbol',
                'short_name' => 'RFEF',
                'country' => 'Spain',
                'confederation' => 'UEFA',
                'fifa_ranking' => 8,
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9a/RFEF_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9a/Flag_of_Spain.svg',
                'fifa_version' => 'FIFA 24',
                'last_updated' => now(),
            ],
            [
                'name' => 'Federazione Italiana Giuoco Calcio',
                'short_name' => 'FIGC',
                'country' => 'Italy',
                'confederation' => 'UEFA',
                'fifa_ranking' => 9,
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/FIGC_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/0/03/Flag_of_Italy.svg',
                'fifa_version' => 'FIFA 24',
                'last_updated' => now(),
            ],
            [
                'name' => 'Fédération Royale Belge de Football',
                'short_name' => 'KBVB',
                'country' => 'Belgium',
                'confederation' => 'UEFA',
                'fifa_ranking' => 3,
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/6/6a/Belgian_Football_Association_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/6/65/Flag_of_Belgium.svg',
                'fifa_version' => 'FIFA 24',
                'last_updated' => now(),
            ],
            [
                'name' => 'Koninklijke Nederlandse Voetbalbond',
                'short_name' => 'KNVB',
                'country' => 'Netherlands',
                'confederation' => 'UEFA',
                'fifa_ranking' => 6,
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7a/KNVB_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/2/20/Flag_of_the_Netherlands.svg',
                'fifa_version' => 'FIFA 24',
                'last_updated' => now(),
            ],
            [
                'name' => 'Federação Portuguesa de Futebol',
                'short_name' => 'FPF',
                'country' => 'Portugal',
                'confederation' => 'UEFA',
                'fifa_ranking' => 7,
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Portuguese_Football_Federation_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Flag_of_Portugal.svg',
                'fifa_version' => 'FIFA 24',
                'last_updated' => now(),
            ],
            [
                'name' => 'Confederação Brasileira de Futebol',
                'short_name' => 'CBF',
                'country' => 'Brazil',
                'confederation' => 'CONMEBOL',
                'fifa_ranking' => 5,
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/4/4a/Brazilian_Football_Confederation_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/0/05/Flag_of_Brazil.svg',
                'fifa_version' => 'FIFA 24',
                'last_updated' => now(),
            ],
            [
                'name' => 'Asociación del Fútbol Argentino',
                'short_name' => 'AFA',
                'country' => 'Argentina',
                'confederation' => 'CONMEBOL',
                'fifa_ranking' => 1,
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8a/Argentine_Football_Association_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/1/1a/Flag_of_Argentina.svg',
                'fifa_version' => 'FIFA 24',
                'last_updated' => now(),
            ],
        ];

        foreach ($associations as $associationData) {
            Association::create($associationData);
        }

        Association::updateOrCreate(
            ['name' => 'Test Association'],
            [
                'country' => 'Testland',
                'fifa_connect_id' => 'TEST_ASSOC_001',
                'status' => 'active',
            ]
        );

        $this->command->info('Associations seeded successfully!');
        $this->command->info('Created ' . count($associations) . ' football associations.');
    }
}
