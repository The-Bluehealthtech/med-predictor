<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\Player;
use App\Models\Association;
use App\Models\Competition;
use App\Models\Season;
use App\Models\MatchModel;
use App\Models\HealthRecord;
use App\Models\PCMA;
use App\Models\User;
use App\Models\MatchEvent;
use App\Models\MatchRoster;
use App\Models\MatchOfficial;
use Carbon\Carbon;

class PremierLeagueUK2023Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏴󠁧󠁢󠁥󠁮󠁧󠁿 Création du dataset Premier League UK 2023/2024...');

        // Étape 1: Créer l'association FA
        $this->createFA();
        
        // Étape 2: Créer les 20 clubs
        $this->createPremierLeagueClubs();
        
        // Étape 3: Créer la saison 2023/2024
        $this->createSeason2023();
        
        // Étape 4: Créer les 600 joueurs
        $this->createPremierLeaguePlayers();
        
        // Étape 5: Créer les matchs
        $this->createPremierLeagueMatches();
        
        // Étape 6: Créer les données médicales
        $this->createMedicalData();
        
        // Étape 7: Créer les arbitres
        $this->createReferees();
        
        $this->command->info('✅ Dataset Premier League UK 2023/2024 créé avec succès !');
    }

    private function createFA()
    {
        $this->command->info('🏴󠁧󠁢󠁥󠁮󠁧󠁿 Création de la Football Association...');
        
        $fa = Association::updateOrCreate(
            ['name' => 'The Football Association'],
            [
                'short_name' => 'FA',
                'country' => 'England',
                'confederation' => 'UEFA',
                'fifa_ranking' => 1,
                'association_logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9c/The_Football_Association_logo.svg',
                'nation_flag_url' => 'https://upload.wikimedia.org/wikipedia/en/b/be/Flag_of_England.svg',
                'fifa_version' => '24',
                'fifa_id' => 'FA_ENG_001',
                'fifa_sync_status' => 'synced',
                'fifa_sync_date' => now(),
                'last_updated' => now(),
            ]
        );
        
        $this->command->info("✅ FA créée avec ID: {$fa->id}");
        return $fa;
    }

    private function createPremierLeagueClubs()
    {
        $this->command->info('🏟️ Création des 20 clubs Premier League...');
        
        $clubs = [
            [
                'name' => 'Arsenal',
                'short_name' => 'ARS',
                'city' => 'London',
                'stadium' => 'Emirates Stadium',
                'founded_year' => 1886,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/53/Arsenal_FC.svg',
                'website' => 'https://www.arsenal.com',
                'budget_eur' => 500000000,
                'wage_budget_eur' => 200000000,
                'transfer_budget_eur' => 100000000,
                'reputation' => 85,
                'facilities_level' => 90,
                'youth_development' => 85,
                'scouting_network' => 80,
                'medical_team' => 85,
                'coaching_staff' => 85,
            ],
            [
                'name' => 'Aston Villa',
                'short_name' => 'AVL',
                'city' => 'Birmingham',
                'stadium' => 'Villa Park',
                'founded_year' => 1874,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Aston_Villa_logo.svg',
                'website' => 'https://www.avfc.co.uk',
                'budget_eur' => 300000000,
                'wage_budget_eur' => 120000000,
                'transfer_budget_eur' => 80000000,
                'reputation' => 75,
                'facilities_level' => 80,
                'youth_development' => 75,
                'scouting_network' => 75,
                'medical_team' => 80,
                'coaching_staff' => 80,
            ],
            [
                'name' => 'Bournemouth',
                'short_name' => 'BOU',
                'city' => 'Bournemouth',
                'stadium' => 'Vitality Stadium',
                'founded_year' => 1899,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/e5/AFC_Bournemouth_%282013%29.svg',
                'website' => 'https://www.afcb.co.uk',
                
                
                'budget_eur' => 150000000,
                'wage_budget_eur' => 60000000,
                'transfer_budget_eur' => 40000000,
                'reputation' => 65,
                'facilities_level' => 70,
                'youth_development' => 65,
                'scouting_network' => 65,
                'medical_team' => 70,
                'coaching_staff' => 70,
            ],
            [
                'name' => 'Brentford',
                'short_name' => 'BRE',
                'city' => 'London',
                'stadium' => 'Gtech Community Stadium',
                'founded_year' => 1889,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/2/2a/Brentford_FC_crest.svg',
                'website' => 'https://www.brentfordfc.com',
                
                
                'budget_eur' => 120000000,
                'wage_budget_eur' => 50000000,
                'transfer_budget_eur' => 30000000,
                'reputation' => 60,
                'facilities_level' => 65,
                'youth_development' => 60,
                'scouting_network' => 70,
                'medical_team' => 65,
                'coaching_staff' => 65,
            ],
            [
                'name' => 'Brighton & Hove Albion',
                'short_name' => 'BHA',
                'city' => 'Brighton',
                'stadium' => 'Amex Stadium',
                'founded_year' => 1901,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fd/Brighton_%26_Hove_Albion_logo.svg',
                'website' => 'https://www.brightonandhovealbion.com',
                
                
                'budget_eur' => 200000000,
                'wage_budget_eur' => 80000000,
                'transfer_budget_eur' => 60000000,
                'reputation' => 70,
                'facilities_level' => 75,
                'youth_development' => 70,
                'scouting_network' => 75,
                'medical_team' => 75,
                'coaching_staff' => 75,
            ],
            [
                'name' => 'Burnley',
                'short_name' => 'BUR',
                'city' => 'Burnley',
                'stadium' => 'Turf Moor',
                'founded_year' => 1882,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/6/6d/Burnley_FC_Logo.svg',
                'website' => 'https://www.burnleyfootballclub.com',
                
                
                'budget_eur' => 100000000,
                'wage_budget_eur' => 40000000,
                'transfer_budget_eur' => 25000000,
                'reputation' => 55,
                'facilities_level' => 60,
                'youth_development' => 55,
                'scouting_network' => 55,
                'medical_team' => 60,
                'coaching_staff' => 60,
            ],
            [
                'name' => 'Chelsea',
                'short_name' => 'CHE',
                'city' => 'London',
                'stadium' => 'Stamford Bridge',
                'founded_year' => 1905,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg',
                'website' => 'https://www.chelseafc.com',
                
                
                'budget_eur' => 800000000,
                'wage_budget_eur' => 300000000,
                'transfer_budget_eur' => 200000000,
                'reputation' => 90,
                'facilities_level' => 95,
                'youth_development' => 90,
                'scouting_network' => 90,
                'medical_team' => 95,
                'coaching_staff' => 95,
            ],
            [
                'name' => 'Crystal Palace',
                'short_name' => 'CRY',
                'city' => 'London',
                'stadium' => 'Selhurst Park',
                'founded_year' => 1905,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/a/a2/Crystal_Palace_FC_logo.svg',
                'website' => 'https://www.cpfc.co.uk',
                
                
                'budget_eur' => 200000000,
                'wage_budget_eur' => 80000000,
                'transfer_budget_eur' => 50000000,
                'reputation' => 70,
                'facilities_level' => 75,
                'youth_development' => 70,
                'scouting_network' => 70,
                'medical_team' => 75,
                'coaching_staff' => 75,
            ],
            [
                'name' => 'Everton',
                'short_name' => 'EVE',
                'city' => 'Liverpool',
                'stadium' => 'Goodison Park',
                'founded_year' => 1878,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7c/Everton_FC_logo.svg',
                'website' => 'https://www.evertonfc.com',
                
                
                'budget_eur' => 250000000,
                'wage_budget_eur' => 100000000,
                'transfer_budget_eur' => 60000000,
                'reputation' => 75,
                'facilities_level' => 80,
                'youth_development' => 75,
                'scouting_network' => 75,
                'medical_team' => 80,
                'coaching_staff' => 80,
            ],
            [
                'name' => 'Fulham',
                'short_name' => 'FUL',
                'city' => 'London',
                'stadium' => 'Craven Cottage',
                'founded_year' => 1879,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Fulham_FC_%28shield%29.svg',
                'website' => 'https://www.fulhamfc.com',
                
                
                'budget_eur' => 150000000,
                'wage_budget_eur' => 60000000,
                'transfer_budget_eur' => 40000000,
                'reputation' => 65,
                'facilities_level' => 70,
                'youth_development' => 65,
                'scouting_network' => 65,
                'medical_team' => 70,
                'coaching_staff' => 70,
            ],
            [
                'name' => 'Liverpool',
                'short_name' => 'LIV',
                'city' => 'Liverpool',
                'stadium' => 'Anfield',
                'founded_year' => 1892,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/0/0c/Liverpool_FC.svg',
                'website' => 'https://www.liverpoolfc.com',
                
                
                'budget_eur' => 600000000,
                'wage_budget_eur' => 250000000,
                'transfer_budget_eur' => 150000000,
                'reputation' => 95,
                'facilities_level' => 95,
                'youth_development' => 90,
                'scouting_network' => 95,
                'medical_team' => 95,
                'coaching_staff' => 95,
            ],
            [
                'name' => 'Luton Town',
                'short_name' => 'LUT',
                'city' => 'Luton',
                'stadium' => 'Kenilworth Road',
                'founded_year' => 1885,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9d/Luton_Town_logo.svg',
                'website' => 'https://www.lutontown.co.uk',
                
                
                'budget_eur' => 80000000,
                'wage_budget_eur' => 30000000,
                'transfer_budget_eur' => 20000000,
                'reputation' => 50,
                'facilities_level' => 55,
                'youth_development' => 50,
                'scouting_network' => 50,
                'medical_team' => 55,
                'coaching_staff' => 55,
            ],
            [
                'name' => 'Manchester City',
                'short_name' => 'MCI',
                'city' => 'Manchester',
                'stadium' => 'Etihad Stadium',
                'founded_year' => 1880,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Manchester_City_FC_badge.svg',
                'website' => 'https://www.mancity.com',
                
                
                'budget_eur' => 1000000000,
                'wage_budget_eur' => 400000000,
                'transfer_budget_eur' => 300000000,
                'reputation' => 95,
                'facilities_level' => 100,
                'youth_development' => 95,
                'scouting_network' => 100,
                'medical_team' => 100,
                'coaching_staff' => 100,
            ],
            [
                'name' => 'Manchester United',
                'short_name' => 'MUN',
                'city' => 'Manchester',
                'stadium' => 'Old Trafford',
                'founded_year' => 1878,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7a/Manchester_United_FC_crest.svg',
                'website' => 'https://www.manutd.com',
                
                
                'budget_eur' => 700000000,
                'wage_budget_eur' => 300000000,
                'transfer_budget_eur' => 200000000,
                'reputation' => 95,
                'facilities_level' => 95,
                'youth_development' => 90,
                'scouting_network' => 95,
                'medical_team' => 95,
                'coaching_staff' => 95,
            ],
            [
                'name' => 'Newcastle United',
                'short_name' => 'NEW',
                'city' => 'Newcastle',
                'stadium' => 'St James\' Park',
                'founded_year' => 1892,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/56/Newcastle_United_Logo.svg',
                'website' => 'https://www.nufc.co.uk',
                
                
                'budget_eur' => 400000000,
                'wage_budget_eur' => 150000000,
                'transfer_budget_eur' => 100000000,
                'reputation' => 80,
                'facilities_level' => 85,
                'youth_development' => 80,
                'scouting_network' => 80,
                'medical_team' => 85,
                'coaching_staff' => 85,
            ],
            [
                'name' => 'Nottingham Forest',
                'short_name' => 'NFO',
                'city' => 'Nottingham',
                'stadium' => 'City Ground',
                'founded_year' => 1865,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/e5/Nottingham_Forest_F.C._logo.svg',
                'website' => 'https://www.nottinghamforest.co.uk',
                
                
                'budget_eur' => 150000000,
                'wage_budget_eur' => 60000000,
                'transfer_budget_eur' => 40000000,
                'reputation' => 65,
                'facilities_level' => 70,
                'youth_development' => 65,
                'scouting_network' => 65,
                'medical_team' => 70,
                'coaching_staff' => 70,
            ],
            [
                'name' => 'Sheffield United',
                'short_name' => 'SHU',
                'city' => 'Sheffield',
                'stadium' => 'Bramall Lane',
                'founded_year' => 1889,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9c/Sheffield_United_FC_logo.svg',
                'website' => 'https://www.sufc.co.uk',
                
                
                'budget_eur' => 100000000,
                'wage_budget_eur' => 40000000,
                'transfer_budget_eur' => 25000000,
                'reputation' => 55,
                'facilities_level' => 60,
                'youth_development' => 55,
                'scouting_network' => 55,
                'medical_team' => 60,
                'coaching_staff' => 60,
            ],
            [
                'name' => 'Tottenham Hotspur',
                'short_name' => 'TOT',
                'city' => 'London',
                'stadium' => 'Tottenham Hotspur Stadium',
                'founded_year' => 1882,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/b/b4/Tottenham_Hotspur.svg',
                'website' => 'https://www.tottenhamhotspur.com',
                
                
                'budget_eur' => 400000000,
                'wage_budget_eur' => 150000000,
                'transfer_budget_eur' => 100000000,
                'reputation' => 85,
                'facilities_level' => 90,
                'youth_development' => 85,
                'scouting_network' => 85,
                'medical_team' => 90,
                'coaching_staff' => 90,
            ],
            [
                'name' => 'West Ham United',
                'short_name' => 'WHU',
                'city' => 'London',
                'stadium' => 'London Stadium',
                'founded_year' => 1895,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/c2/West_Ham_United_FC_logo.svg',
                'website' => 'https://www.whufc.com',
                
                
                'budget_eur' => 250000000,
                'wage_budget_eur' => 100000000,
                'transfer_budget_eur' => 60000000,
                'reputation' => 75,
                'facilities_level' => 80,
                'youth_development' => 75,
                'scouting_network' => 75,
                'medical_team' => 80,
                'coaching_staff' => 80,
            ],
            [
                'name' => 'Wolverhampton Wanderers',
                'short_name' => 'WOL',
                'city' => 'Wolverhampton',
                'stadium' => 'Molineux Stadium',
                'founded_year' => 1877,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fc/Wolverhampton_Wanderers.svg',
                'website' => 'https://www.wolves.co.uk',
                
                
                'budget_eur' => 200000000,
                'wage_budget_eur' => 80000000,
                'transfer_budget_eur' => 50000000,
                'reputation' => 70,
                'facilities_level' => 75,
                'youth_development' => 70,
                'scouting_network' => 70,
                'medical_team' => 75,
                'coaching_staff' => 75,
            ],
        ];

        $fa = Association::where('name', 'The Football Association')->first();
        
        foreach ($clubs as $clubData) {
            $club = Club::updateOrCreate(
                ['name' => $clubData['name']],
                array_merge($clubData, [
                    'association_id' => $fa->id,
                    'country' => 'England',
                    'status' => 'active',
                    'fifa_connect_id' => 'CLUB_' . $clubData['short_name'] . '_001',
                    'fifa_sync_status' => 'synced',
                    'fifa_sync_date' => now(),
                ])
            );
            
            $this->command->info("✅ Club créé: {$club->name}");
        }
        
        $this->command->info('✅ Tous les 20 clubs Premier League créés !');
    }

    private function createSeason2023()
    {
        $this->command->info('📅 Création de la saison 2023/2024...');
        
        $season = Season::updateOrCreate(
            ['name' => '2023/2024'],
            [
                'short_name' => '2023/24',
                'start_date' => '2023-08-11',
                'end_date' => '2024-05-19',
                'registration_start_date' => '2023-06-01',
                'registration_end_date' => '2023-08-10',
                'status' => 'completed',
                'description' => 'Premier League 2023/2024 Season',
                'is_current' => false,
                'created_by' => 1,
                'updated_by' => 1,
            ]
        );
        
        $this->command->info("✅ Saison 2023/2024 créée avec ID: {$season->id}");
        return $season;
    }

    private function createPremierLeaguePlayers()
    {
        $this->command->info('👥 Création des 600 joueurs Premier League...');
        
        // Je vais créer un fichier séparé pour les données des joueurs
        // car c'est trop volumineux pour un seul fichier
        $this->call(PremierLeaguePlayersSeeder::class);
    }

    private function createPremierLeagueMatches()
    {
        $this->command->info('⚽ Création des 380 matchs Premier League...');
        
        // Je vais créer un fichier séparé pour les matchs
        $this->call(PremierLeagueMatchesSeeder::class);
    }

    private function createMedicalData()
    {
        $this->command->info('🏥 Création des données médicales...');
        
        // Je vais créer un fichier séparé pour les données médicales
        $this->call(PremierLeagueMedicalDataSeeder::class);
    }

    private function createReferees()
    {
        $this->command->info('👨‍⚖️ Création des arbitres...');
        
        // Je vais créer un fichier séparé pour les arbitres
        $this->call(PremierLeagueRefereesSeeder::class);
    }
}
