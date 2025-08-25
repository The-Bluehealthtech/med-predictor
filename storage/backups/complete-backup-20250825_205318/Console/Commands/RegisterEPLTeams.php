<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Competition;
use App\Models\Club;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class RegisterEPLTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'epl:register-teams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register exactly 20 Premier League teams to the EPL competition';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Registering Premier League teams to EPL competition...');

        // Get the EPL competition
        $epl = Competition::where('short_name', 'EPL')->first();
        
        if (!$epl) {
            $this->error('EPL competition not found!');
            return 1;
        }

        // Get all Premier League clubs
        $premierLeagueClubs = Club::where('league', 'Premier League')
            ->whereIn('name', [
                'Manchester United',
                'Liverpool FC', 
                'Arsenal FC',
                'Chelsea FC',
                'Manchester City',
                'Tottenham Hotspur',
                'Newcastle United',
                'Brighton & Hove Albion',
                'Aston Villa',
                'West Ham United',
                'Crystal Palace',
                'Brentford',
                'Fulham',
                'Wolverhampton Wanderers',
                'Nottingham Forest',
                'Everton',
                'Burnley',
                'Luton Town',
                'Sheffield United',
                'AFC Bournemouth'
            ])
            ->get();

        if ($premierLeagueClubs->count() < 20) {
            $this->warn("Only found {$premierLeagueClubs->count()} Premier League clubs. Creating missing clubs...");
            
            // Create missing Premier League clubs
            $missingClubs = $this->createMissingPremierLeagueClubs();
            $premierLeagueClubs = $premierLeagueClubs->merge($missingClubs);
        }

        // Clear existing team registrations for EPL
        DB::table('competition_team')->where('competition_id', $epl->id)->delete();
        
        $this->info('Cleared existing team registrations for EPL.');

        $registeredCount = 0;

        foreach ($premierLeagueClubs->take(20) as $club) {
            // Get the first team for each club
            $team = $club->teams()->where('type', 'first_team')->first();
            
            if (!$team) {
                $this->warn("No first team found for {$club->name}. Creating one...");
                $team = $this->createFirstTeam($club);
            }

            // Register team to EPL competition
            DB::table('competition_team')->insert([
                'competition_id' => $epl->id,
                'team_id' => $team->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info("Registered {$club->name} ({$team->name}) to EPL");
            $registeredCount++;
        }

        $this->info("Successfully registered {$registeredCount} teams to EPL competition!");
        
        // Verify the count
        $actualCount = $epl->teams()->count();
        $this->info("EPL competition now has {$actualCount} teams registered.");

        return 0;
    }

    private function createMissingPremierLeagueClubs()
    {
        $englishFA = \App\Models\Association::where('name', 'The Football Association')->first();
        $associationId = $englishFA ? $englishFA->id : null;

        $missingClubs = [
            [
                'name' => 'Tottenham Hotspur',
                'short_name' => 'Spurs',
                'country' => 'England',
                'city' => 'London',
                'stadium' => 'Tottenham Hotspur Stadium',
                'founded_year' => 1882,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/b/b4/Tottenham_Hotspur.svg',
                'website' => 'https://www.tottenhamhotspur.com',
                'email' => 'info@tottenhamhotspur.com',
                'phone' => '+44 20 8365 5000',
                'address' => '782 High Road, London N17 0BX',
                'fifa_connect_id' => 'THFC001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 350000000,
                'wage_budget_eur' => 140000000,
                'transfer_budget_eur' => 60000000,
                'reputation' => 85,
                'facilities_level' => 5,
                'youth_development' => 4,
                'scouting_network' => 4,
                'medical_team' => 4,
                'coaching_staff' => 4,
            ],
            [
                'name' => 'Newcastle United',
                'short_name' => 'Newcastle',
                'country' => 'England',
                'city' => 'Newcastle',
                'stadium' => 'St James\' Park',
                'founded_year' => 1892,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/56/Newcastle_United_Logo.svg',
                'website' => 'https://www.nufc.co.uk',
                'email' => 'info@nufc.co.uk',
                'phone' => '+44 191 201 8400',
                'address' => 'St James\' Park, Newcastle NE1 4ST',
                'fifa_connect_id' => 'NUFC001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 400000000,
                'wage_budget_eur' => 160000000,
                'transfer_budget_eur' => 80000000,
                'reputation' => 82,
                'facilities_level' => 4,
                'youth_development' => 3,
                'scouting_network' => 4,
                'medical_team' => 4,
                'coaching_staff' => 4,
            ],
            [
                'name' => 'Brighton & Hove Albion',
                'short_name' => 'Brighton',
                'country' => 'England',
                'city' => 'Brighton',
                'stadium' => 'Amex Stadium',
                'founded_year' => 1901,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fd/Brighton_%26_Hove_Albion_logo.svg',
                'website' => 'https://www.brightonandhovealbion.com',
                'email' => 'info@brightonandhovealbion.com',
                'phone' => '+44 1273 878 272',
                'address' => 'Village Way, Brighton BN1 9BL',
                'fifa_connect_id' => 'BHA001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 200000000,
                'wage_budget_eur' => 80000000,
                'transfer_budget_eur' => 40000000,
                'reputation' => 75,
                'facilities_level' => 4,
                'youth_development' => 4,
                'scouting_network' => 5,
                'medical_team' => 4,
                'coaching_staff' => 4,
            ],
            [
                'name' => 'Aston Villa',
                'short_name' => 'Villa',
                'country' => 'England',
                'city' => 'Birmingham',
                'stadium' => 'Villa Park',
                'founded_year' => 1874,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Aston_Villa_logo.svg',
                'website' => 'https://www.avfc.co.uk',
                'email' => 'info@avfc.co.uk',
                'phone' => '+44 121 327 2299',
                'address' => 'Trinity Road, Birmingham B6 6HE',
                'fifa_connect_id' => 'AVFC001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 250000000,
                'wage_budget_eur' => 100000000,
                'transfer_budget_eur' => 50000000,
                'reputation' => 78,
                'facilities_level' => 4,
                'youth_development' => 4,
                'scouting_network' => 4,
                'medical_team' => 4,
                'coaching_staff' => 4,
            ],
            [
                'name' => 'West Ham United',
                'short_name' => 'West Ham',
                'country' => 'England',
                'city' => 'London',
                'stadium' => 'London Stadium',
                'founded_year' => 1895,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/c2/West_Ham_United_FC_logo.svg',
                'website' => 'https://www.whufc.com',
                'email' => 'info@whufc.com',
                'phone' => '+44 20 8548 2748',
                'address' => 'Queen Elizabeth Olympic Park, London E20 2ST',
                'fifa_connect_id' => 'WHUFC001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 220000000,
                'wage_budget_eur' => 90000000,
                'transfer_budget_eur' => 45000000,
                'reputation' => 76,
                'facilities_level' => 4,
                'youth_development' => 3,
                'scouting_network' => 4,
                'medical_team' => 4,
                'coaching_staff' => 4,
            ],
            [
                'name' => 'Crystal Palace',
                'short_name' => 'Palace',
                'country' => 'England',
                'city' => 'London',
                'stadium' => 'Selhurst Park',
                'founded_year' => 1905,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/a/a2/Crystal_Palace_FC_logo_%282022%29.svg',
                'website' => 'https://www.cpfc.co.uk',
                'email' => 'info@cpfc.co.uk',
                'phone' => '+44 20 8768 6000',
                'address' => 'Selhurst Park, London SE25 6PU',
                'fifa_connect_id' => 'CPFC001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 180000000,
                'wage_budget_eur' => 70000000,
                'transfer_budget_eur' => 35000000,
                'reputation' => 72,
                'facilities_level' => 3,
                'youth_development' => 3,
                'scouting_network' => 3,
                'medical_team' => 3,
                'coaching_staff' => 3,
            ],
            [
                'name' => 'Brentford',
                'short_name' => 'Brentford',
                'country' => 'England',
                'city' => 'London',
                'stadium' => 'Gtech Community Stadium',
                'founded_year' => 1889,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/2/2a/Brentford_FC_crest.svg',
                'website' => 'https://www.brentfordfc.com',
                'email' => 'info@brentfordfc.com',
                'phone' => '+44 20 8847 2511',
                'address' => 'Lionel Road South, London TW8 0RU',
                'fifa_connect_id' => 'BFC001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 150000000,
                'wage_budget_eur' => 60000000,
                'transfer_budget_eur' => 30000000,
                'reputation' => 68,
                'facilities_level' => 3,
                'youth_development' => 3,
                'scouting_network' => 4,
                'medical_team' => 3,
                'coaching_staff' => 3,
            ],
            [
                'name' => 'Fulham',
                'short_name' => 'Fulham',
                'country' => 'England',
                'city' => 'London',
                'stadium' => 'Craven Cottage',
                'founded_year' => 1879,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Fulham_FC_%28shield%29.svg',
                'website' => 'https://www.fulhamfc.com',
                'email' => 'info@fulhamfc.com',
                'phone' => '+44 20 8336 7555',
                'address' => 'Craven Cottage, London SW6 6HH',
                'fifa_connect_id' => 'FFC001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 160000000,
                'wage_budget_eur' => 65000000,
                'transfer_budget_eur' => 32000000,
                'reputation' => 70,
                'facilities_level' => 3,
                'youth_development' => 3,
                'scouting_network' => 3,
                'medical_team' => 3,
                'coaching_staff' => 3,
            ],
            [
                'name' => 'Wolverhampton Wanderers',
                'short_name' => 'Wolves',
                'country' => 'England',
                'city' => 'Wolverhampton',
                'stadium' => 'Molineux Stadium',
                'founded_year' => 1877,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fc/Wolverhampton_Wanderers.svg',
                'website' => 'https://www.wolves.co.uk',
                'email' => 'info@wolves.co.uk',
                'phone' => '+44 1902 622 647',
                'address' => 'Waterloo Road, Wolverhampton WV1 4QR',
                'fifa_connect_id' => 'WWFC001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 170000000,
                'wage_budget_eur' => 70000000,
                'transfer_budget_eur' => 35000000,
                'reputation' => 71,
                'facilities_level' => 3,
                'youth_development' => 3,
                'scouting_network' => 3,
                'medical_team' => 3,
                'coaching_staff' => 3,
            ],
            [
                'name' => 'Nottingham Forest',
                'short_name' => 'Forest',
                'country' => 'England',
                'city' => 'Nottingham',
                'stadium' => 'City Ground',
                'founded_year' => 1865,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/e5/Nottingham_Forest_F.C._logo.svg',
                'website' => 'https://www.nottinghamforest.co.uk',
                'email' => 'info@nottinghamforest.co.uk',
                'phone' => '+44 115 982 4444',
                'address' => 'Pavilion Road, Nottingham NG2 5FJ',
                'fifa_connect_id' => 'NFFC001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 140000000,
                'wage_budget_eur' => 55000000,
                'transfer_budget_eur' => 28000000,
                'reputation' => 65,
                'facilities_level' => 3,
                'youth_development' => 3,
                'scouting_network' => 3,
                'medical_team' => 3,
                'coaching_staff' => 3,
            ],
            [
                'name' => 'Everton',
                'short_name' => 'Everton',
                'country' => 'England',
                'city' => 'Liverpool',
                'stadium' => 'Goodison Park',
                'founded_year' => 1878,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7c/Everton_FC_logo.svg',
                'website' => 'https://www.evertonfc.com',
                'email' => 'info@evertonfc.com',
                'phone' => '+44 151 556 1878',
                'address' => 'Goodison Road, Liverpool L4 4EL',
                'fifa_connect_id' => 'EFC001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 200000000,
                'wage_budget_eur' => 80000000,
                'transfer_budget_eur' => 40000000,
                'reputation' => 74,
                'facilities_level' => 3,
                'youth_development' => 3,
                'scouting_network' => 3,
                'medical_team' => 3,
                'coaching_staff' => 3,
            ],
            [
                'name' => 'Burnley',
                'short_name' => 'Burnley',
                'country' => 'England',
                'city' => 'Burnley',
                'stadium' => 'Turf Moor',
                'founded_year' => 1882,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/6/62/Burnley_F.C._Logo.svg',
                'website' => 'https://www.burnleyfootballclub.com',
                'email' => 'info@burnleyfootballclub.com',
                'phone' => '+44 1282 700 007',
                'address' => 'Harry Potts Way, Burnley BB10 4BX',
                'fifa_connect_id' => 'BFC002',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 120000000,
                'wage_budget_eur' => 45000000,
                'transfer_budget_eur' => 25000000,
                'reputation' => 62,
                'facilities_level' => 2,
                'youth_development' => 2,
                'scouting_network' => 2,
                'medical_team' => 2,
                'coaching_staff' => 2,
            ],
            [
                'name' => 'Luton Town',
                'short_name' => 'Luton',
                'country' => 'England',
                'city' => 'Luton',
                'stadium' => 'Kenilworth Road',
                'founded_year' => 1885,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Luton_Town_logo.svg',
                'website' => 'https://www.lutontown.co.uk',
                'email' => 'info@lutontown.co.uk',
                'phone' => '+44 1582 411 622',
                'address' => '1 Maple Road, Luton LU4 8AW',
                'fifa_connect_id' => 'LTFC001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 80000000,
                'wage_budget_eur' => 30000000,
                'transfer_budget_eur' => 15000000,
                'reputation' => 55,
                'facilities_level' => 2,
                'youth_development' => 2,
                'scouting_network' => 2,
                'medical_team' => 2,
                'coaching_staff' => 2,
            ],
            [
                'name' => 'Sheffield United',
                'short_name' => 'Sheffield Utd',
                'country' => 'England',
                'city' => 'Sheffield',
                'stadium' => 'Bramall Lane',
                'founded_year' => 1889,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9c/Sheffield_United_FC_logo.svg',
                'website' => 'https://www.sufc.co.uk',
                'email' => 'info@sufc.co.uk',
                'phone' => '+44 114 253 7200',
                'address' => 'Bramall Lane, Sheffield S2 4SU',
                'fifa_connect_id' => 'SUFC001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 90000000,
                'wage_budget_eur' => 35000000,
                'transfer_budget_eur' => 18000000,
                'reputation' => 58,
                'facilities_level' => 2,
                'youth_development' => 2,
                'scouting_network' => 2,
                'medical_team' => 2,
                'coaching_staff' => 2,
            ],
            [
                'name' => 'AFC Bournemouth',
                'short_name' => 'Bournemouth',
                'country' => 'England',
                'city' => 'Bournemouth',
                'stadium' => 'Vitality Stadium',
                'founded_year' => 1899,
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/e5/AFC_Bournemouth_%282013%29.svg',
                'website' => 'https://www.afcb.co.uk',
                'email' => 'info@afcb.co.uk',
                'phone' => '+44 1202 726 300',
                'address' => 'Dean Court, Bournemouth BH7 7AF',
                'fifa_connect_id' => 'AFCB001',
                'association_id' => $associationId,
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 100000000,
                'wage_budget_eur' => 40000000,
                'transfer_budget_eur' => 20000000,
                'reputation' => 60,
                'facilities_level' => 2,
                'youth_development' => 2,
                'scouting_network' => 2,
                'medical_team' => 2,
                'coaching_staff' => 2,
            ]
        ];

        $createdClubs = collect();
        
        foreach ($missingClubs as $clubData) {
            $club = Club::create($clubData);
            $createdClubs->push($club);
            $this->info("Created club: {$club->name}");
        }

        return $createdClubs;
    }

    private function createFirstTeam($club)
    {
        $team = $club->teams()->create([
            'name' => 'First Team',
            'type' => 'first_team',
            'formation' => '4-3-3',
            'tactical_style' => 'Balanced approach with focus on results',
            'playing_philosophy' => 'Organized defending, quick transitions, clinical finishing',
            'coach_name' => 'Head Coach',
            'assistant_coach' => 'Assistant Coach',
            'fitness_coach' => 'Fitness Coach',
            'goalkeeper_coach' => 'Goalkeeper Coach',
            'scout' => 'Chief Scout',
            'medical_staff' => 'Medical Team',
            'status' => 'active',
            'season' => '2024/25',
            'competition_level' => 'Premier League',
            'budget_allocation' => 50000000,
            'training_facility' => 'Training Ground',
            'home_ground' => $club->stadium
        ]);

        return $team;
    }
} 