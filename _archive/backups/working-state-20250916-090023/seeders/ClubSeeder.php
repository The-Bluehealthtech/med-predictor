<?php

namespace Database\Seeders;

use App\Models\Association;
use App\Models\Club;
use App\Models\Competition;
use App\Models\MatchModel;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('team_players')->truncate();
        DB::table('teams')->truncate();
        DB::table('clubs')->truncate();
        DB::table('game_matches')->truncate();

        // Get the English Football Association
        $englishFA = \App\Models\Association::where('name', 'The Football Association')->first();
        $associationId = $englishFA ? $englishFA->id : null;

        // Create Premier League Clubs (2023/24)
        $premierLeagueClubs = [
            [ 'name' => 'Arsenal FC', 'short_name' => 'Arsenal', 'country' => 'England', 'city' => 'London', 'stadium' => 'Emirates Stadium', 'founded_year' => 1886, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/53/Arsenal_FC.svg', 'website' => 'https://www.arsenal.com', 'email' => 'info@arsenal.com', 'phone' => '+44 20 7619 5003', 'address' => 'Hornsey Road, London N7 7AJ', 'fifa_connect_id' => 'AFC001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 400000000, 'wage_budget_eur' => 160000000, 'transfer_budget_eur' => 70000000, 'reputation' => 90, 'facilities_level' => 5, 'youth_development' => 5, 'scouting_network' => 4, 'medical_team' => 5, 'coaching_staff' => 4 ],
            [ 'name' => 'Aston Villa', 'short_name' => 'Aston Villa', 'country' => 'England', 'city' => 'Birmingham', 'stadium' => 'Villa Park', 'founded_year' => 1874, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/9/9f/Aston_Villa_logo.svg', 'website' => 'https://www.avfc.co.uk', 'email' => 'info@avfc.co.uk', 'phone' => '+44 121 327 2299', 'address' => 'Trinity Road, Birmingham B6 6HE', 'fifa_connect_id' => 'AVFC001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 150000000, 'wage_budget_eur' => 60000000, 'transfer_budget_eur' => 30000000, 'reputation' => 75, 'facilities_level' => 4, 'youth_development' => 4, 'scouting_network' => 3, 'medical_team' => 4, 'coaching_staff' => 4 ],
            [ 'name' => 'AFC Bournemouth', 'short_name' => 'Bournemouth', 'country' => 'England', 'city' => 'Bournemouth', 'stadium' => 'Vitality Stadium', 'founded_year' => 1899, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/e5/AFC_Bournemouth.svg', 'website' => 'https://www.afcb.co.uk', 'email' => 'enquiries@afcb.co.uk', 'phone' => '+44 344 576 1910', 'address' => 'Dean Court, Bournemouth BH7 7AF', 'fifa_connect_id' => 'BOU001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 90000000, 'wage_budget_eur' => 35000000, 'transfer_budget_eur' => 15000000, 'reputation' => 65, 'facilities_level' => 3, 'youth_development' => 3, 'scouting_network' => 2, 'medical_team' => 3, 'coaching_staff' => 3 ],
            [ 'name' => 'Brentford FC', 'short_name' => 'Brentford', 'country' => 'England', 'city' => 'Brentford', 'stadium' => 'Gtech Community Stadium', 'founded_year' => 1889, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/2/2a/Brentford_FC_crest.svg', 'website' => 'https://www.brentfordfc.com', 'email' => 'enquiries@brentfordfc.com', 'phone' => '+44 20 8847 2511', 'address' => 'Lionel Road South, Brentford TW8 0RU', 'fifa_connect_id' => 'BRE001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 95000000, 'wage_budget_eur' => 40000000, 'transfer_budget_eur' => 20000000, 'reputation' => 68, 'facilities_level' => 3, 'youth_development' => 3, 'scouting_network' => 3, 'medical_team' => 3, 'coaching_staff' => 3 ],
            [ 'name' => 'Brighton & Hove Albion', 'short_name' => 'Brighton', 'country' => 'England', 'city' => 'Brighton', 'stadium' => 'Amex Stadium', 'founded_year' => 1901, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fd/Brighton_%26_Hove_Albion_logo.svg', 'website' => 'https://www.brightonandhovealbion.com', 'email' => 'seagulls@bhafc.co.uk', 'phone' => '+44 1273 878288', 'address' => 'Village Way, Brighton BN1 9BL', 'fifa_connect_id' => 'BHA001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 120000000, 'wage_budget_eur' => 50000000, 'transfer_budget_eur' => 25000000, 'reputation' => 70, 'facilities_level' => 4, 'youth_development' => 4, 'scouting_network' => 3, 'medical_team' => 4, 'coaching_staff' => 4 ],
            [ 'name' => 'Burnley FC', 'short_name' => 'Burnley', 'country' => 'England', 'city' => 'Burnley', 'stadium' => 'Turf Moor', 'founded_year' => 1882, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/6/62/Burnley_FC_logo.svg', 'website' => 'https://www.burnleyfootballclub.com', 'email' => 'info@burnleyfc.com', 'phone' => '+44 1282 446800', 'address' => 'Harry Potts Way, Burnley BB10 4BX', 'fifa_connect_id' => 'BUR001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 80000000, 'wage_budget_eur' => 30000000, 'transfer_budget_eur' => 10000000, 'reputation' => 60, 'facilities_level' => 3, 'youth_development' => 3, 'scouting_network' => 2, 'medical_team' => 3, 'coaching_staff' => 3 ],
            [ 'name' => 'Chelsea FC', 'short_name' => 'Chelsea', 'country' => 'England', 'city' => 'London', 'stadium' => 'Stamford Bridge', 'founded_year' => 1905, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg', 'website' => 'https://www.chelseafc.com', 'email' => 'info@chelseafc.com', 'phone' => '+44 20 7386 9373', 'address' => 'Fulham Road, London SW6 1HS', 'fifa_connect_id' => 'CFC001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 600000000, 'wage_budget_eur' => 250000000, 'transfer_budget_eur' => 150000000, 'reputation' => 88, 'facilities_level' => 5, 'youth_development' => 4, 'scouting_network' => 5, 'medical_team' => 5, 'coaching_staff' => 5 ],
            [ 'name' => 'Crystal Palace', 'short_name' => 'Crystal Palace', 'country' => 'England', 'city' => 'London', 'stadium' => 'Selhurst Park', 'founded_year' => 1905, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/0/0c/Crystal_Palace_FC_logo.svg', 'website' => 'https://www.cpfc.co.uk', 'email' => 'info@cpfc.co.uk', 'phone' => '+44 20 8768 6000', 'address' => 'Holmesdale Road, London SE25 6PU', 'fifa_connect_id' => 'CRY001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 95000000, 'wage_budget_eur' => 40000000, 'transfer_budget_eur' => 20000000, 'reputation' => 68, 'facilities_level' => 3, 'youth_development' => 3, 'scouting_network' => 3, 'medical_team' => 3, 'coaching_staff' => 3 ],
            [ 'name' => 'Everton FC', 'short_name' => 'Everton', 'country' => 'England', 'city' => 'Liverpool', 'stadium' => 'Goodison Park', 'founded_year' => 1878, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7c/Everton_FC_logo.svg', 'website' => 'https://www.evertonfc.com', 'email' => 'everton@evertonfc.com', 'phone' => '+44 151 556 1878', 'address' => 'Goodison Road, Liverpool L4 4EL', 'fifa_connect_id' => 'EVE001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 120000000, 'wage_budget_eur' => 50000000, 'transfer_budget_eur' => 25000000, 'reputation' => 70, 'facilities_level' => 4, 'youth_development' => 4, 'scouting_network' => 3, 'medical_team' => 4, 'coaching_staff' => 4 ],
            [ 'name' => 'Fulham FC', 'short_name' => 'Fulham', 'country' => 'England', 'city' => 'London', 'stadium' => 'Craven Cottage', 'founded_year' => 1879, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Fulham_FC_%28shield%29.svg', 'website' => 'https://www.fulhamfc.com', 'email' => 'enquiries@fulhamfc.com', 'phone' => '+44 20 8336 7529', 'address' => 'Stevenage Road, London SW6 6HH', 'fifa_connect_id' => 'FUL001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 90000000, 'wage_budget_eur' => 35000000, 'transfer_budget_eur' => 15000000, 'reputation' => 65, 'facilities_level' => 3, 'youth_development' => 3, 'scouting_network' => 2, 'medical_team' => 3, 'coaching_staff' => 3 ],
            [ 'name' => 'Luton Town', 'short_name' => 'Luton', 'country' => 'England', 'city' => 'Luton', 'stadium' => 'Kenilworth Road', 'founded_year' => 1885, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8b/Luton_Town_FC_logo.svg', 'website' => 'https://www.lutontown.co.uk', 'email' => 'info@lutontown.co.uk', 'phone' => '+44 1582 411622', 'address' => '1 Maple Road East, Luton LU4 8AW', 'fifa_connect_id' => 'LUT001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 60000000, 'wage_budget_eur' => 20000000, 'transfer_budget_eur' => 8000000, 'reputation' => 55, 'facilities_level' => 2, 'youth_development' => 2, 'scouting_network' => 1, 'medical_team' => 2, 'coaching_staff' => 2 ],
            [ 'name' => 'Liverpool FC', 'short_name' => 'Liverpool', 'country' => 'England', 'city' => 'Liverpool', 'stadium' => 'Anfield', 'founded_year' => 1892, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/0/0c/Liverpool_FC.svg', 'website' => 'https://www.liverpoolfc.com', 'email' => 'info@liverpoolfc.com', 'phone' => '+44 151 260 6677', 'address' => 'Anfield Road, Liverpool L4 0TH', 'fifa_connect_id' => 'LFC001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 450000000, 'wage_budget_eur' => 180000000, 'transfer_budget_eur' => 80000000, 'reputation' => 92, 'facilities_level' => 5, 'youth_development' => 4, 'scouting_network' => 5, 'medical_team' => 5, 'coaching_staff' => 5 ],
            [ 'name' => 'Manchester City', 'short_name' => 'Man City', 'country' => 'England', 'city' => 'Manchester', 'stadium' => 'Etihad Stadium', 'founded_year' => 1880, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Manchester_City_FC_badge.svg', 'website' => 'https://www.mancity.com', 'email' => 'info@mancity.com', 'phone' => '+44 161 444 1894', 'address' => 'Etihad Stadium, Manchester M11 3FF', 'fifa_connect_id' => 'MCFC001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 800000000, 'wage_budget_eur' => 300000000, 'transfer_budget_eur' => 200000000, 'reputation' => 96, 'facilities_level' => 5, 'youth_development' => 5, 'scouting_network' => 5, 'medical_team' => 5, 'coaching_staff' => 5 ],
            [ 'name' => 'Manchester United', 'short_name' => 'Man Utd', 'country' => 'England', 'city' => 'Manchester', 'stadium' => 'Old Trafford', 'founded_year' => 1878, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/7/7a/Manchester_United_FC_crest.svg', 'website' => 'https://www.manutd.com', 'email' => 'info@manutd.com', 'phone' => '+44 161 868 8000', 'address' => 'Sir Matt Busby Way, Manchester M16 0RA', 'fifa_connect_id' => 'MUFC001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 500000000, 'wage_budget_eur' => 200000000, 'transfer_budget_eur' => 100000000, 'reputation' => 95, 'facilities_level' => 5, 'youth_development' => 5, 'scouting_network' => 5, 'medical_team' => 5, 'coaching_staff' => 5 ],
            [ 'name' => 'Newcastle United', 'short_name' => 'Newcastle', 'country' => 'England', 'city' => 'Newcastle', 'stadium' => "St James' Park", 'founded_year' => 1892, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/5/56/Newcastle_United_Logo.svg', 'website' => 'https://www.nufc.co.uk', 'email' => 'admin@nufc.co.uk', 'phone' => '+44 191 201 8400', 'address' => "St James' Park, Newcastle upon Tyne NE1 4ST", 'fifa_connect_id' => 'NEW001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 200000000, 'wage_budget_eur' => 80000000, 'transfer_budget_eur' => 40000000, 'reputation' => 80, 'facilities_level' => 4, 'youth_development' => 4, 'scouting_network' => 4, 'medical_team' => 4, 'coaching_staff' => 4 ],
            [ 'name' => 'Nottingham Forest', 'short_name' => 'Nottm Forest', 'country' => 'England', 'city' => 'Nottingham', 'stadium' => 'City Ground', 'founded_year' => 1865, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/1/13/Nottingham_Forest_FC_logo.svg', 'website' => 'https://www.nottinghamforest.co.uk', 'email' => 'info@nottinghamforest.co.uk', 'phone' => '+44 115 982 4444', 'address' => 'City Ground, Nottingham NG2 5FJ', 'fifa_connect_id' => 'NFO001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 90000000, 'wage_budget_eur' => 35000000, 'transfer_budget_eur' => 15000000, 'reputation' => 65, 'facilities_level' => 3, 'youth_development' => 3, 'scouting_network' => 2, 'medical_team' => 3, 'coaching_staff' => 3 ],
            [ 'name' => 'Sheffield United', 'short_name' => 'Sheffield Utd', 'country' => 'England', 'city' => 'Sheffield', 'stadium' => 'Bramall Lane', 'founded_year' => 1889, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/3/3e/Sheffield_United_FC_logo.svg', 'website' => 'https://www.sufc.co.uk', 'email' => 'info@sufc.co.uk', 'phone' => '+44 114 253 7200', 'address' => 'Bramall Lane, Sheffield S2 4SU', 'fifa_connect_id' => 'SHU001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 70000000, 'wage_budget_eur' => 25000000, 'transfer_budget_eur' => 10000000, 'reputation' => 60, 'facilities_level' => 2, 'youth_development' => 2, 'scouting_network' => 1, 'medical_team' => 2, 'coaching_staff' => 2 ],
            [ 'name' => 'Tottenham Hotspur', 'short_name' => 'Tottenham', 'country' => 'England', 'city' => 'London', 'stadium' => 'Tottenham Hotspur Stadium', 'founded_year' => 1882, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/b/b4/Tottenham_Hotspur.svg', 'website' => 'https://www.tottenhamhotspur.com', 'email' => 'contactus@tottenhamhotspur.com', 'phone' => '+44 344 499 5000', 'address' => '782 High Road, London N17 0BX', 'fifa_connect_id' => 'TOT001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 350000000, 'wage_budget_eur' => 140000000, 'transfer_budget_eur' => 60000000, 'reputation' => 85, 'facilities_level' => 5, 'youth_development' => 4, 'scouting_network' => 4, 'medical_team' => 5, 'coaching_staff' => 5 ],
            [ 'name' => 'West Ham United', 'short_name' => 'West Ham', 'country' => 'England', 'city' => 'London', 'stadium' => 'London Stadium', 'founded_year' => 1895, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/c2/West_Ham_United_FC_logo.svg', 'website' => 'https://www.whufc.com', 'email' => 'info@westhamunited.co.uk', 'phone' => '+44 20 8548 2748', 'address' => 'London Stadium, London E20 2ST', 'fifa_connect_id' => 'WHU001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 120000000, 'wage_budget_eur' => 50000000, 'transfer_budget_eur' => 25000000, 'reputation' => 70, 'facilities_level' => 4, 'youth_development' => 4, 'scouting_network' => 3, 'medical_team' => 4, 'coaching_staff' => 4 ],
            [ 'name' => 'Wolverhampton Wanderers', 'short_name' => 'Wolves', 'country' => 'England', 'city' => 'Wolverhampton', 'stadium' => 'Molineux Stadium', 'founded_year' => 1877, 'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/fc/Wolverhampton_Wanderers.svg', 'website' => 'https://www.wolves.co.uk', 'email' => 'info@wolves.co.uk', 'phone' => '+44 871 222 2220', 'address' => 'Waterloo Road, Wolverhampton WV1 4QR', 'fifa_connect_id' => 'WOL001', 'association_id' => $associationId, 'league' => 'Premier League', 'division' => '1', 'status' => 'active', 'budget_eur' => 95000000, 'wage_budget_eur' => 40000000, 'transfer_budget_eur' => 20000000, 'reputation' => 68, 'facilities_level' => 3, 'youth_development' => 3, 'scouting_network' => 3, 'medical_team' => 3, 'coaching_staff' => 3 ]
        ];

        foreach ($premierLeagueClubs as $clubData) {
            $club = Club::create($clubData);

            // Create teams for each club
            $teams = [
                [
                    'name' => 'First Team',
                    'type' => 'first_team',
                    'formation' => '4-3-3',
                    'tactical_style' => 'Possession-based attacking football',
                    'playing_philosophy' => 'High pressing, quick transitions, technical excellence',
                    'coach_name' => 'Erik ten Hag',
                    'assistant_coach' => 'Steve McClaren',
                    'fitness_coach' => 'Richard Hawkins',
                    'goalkeeper_coach' => 'Richard Hartis',
                    'scout' => 'Marcel Bout',
                    'medical_staff' => 'Dr. Gary O\'Driscoll',
                    'status' => 'active',
                    'season' => '2024/25',
                    'competition_level' => 'Premier League',
                    'budget_allocation' => 200000000,
                    'training_facility' => 'Carrington Training Complex',
                    'home_ground' => 'Old Trafford'
                ],
                [
                    'name' => 'Reserve Team',
                    'type' => 'reserve',
                    'formation' => '4-4-2',
                    'tactical_style' => 'Balanced approach with focus on development',
                    'playing_philosophy' => 'Technical development, tactical understanding',
                    'coach_name' => 'Neil Wood',
                    'assistant_coach' => 'Quinton Fortune',
                    'fitness_coach' => 'Tom Hughes',
                    'goalkeeper_coach' => 'Alan Fettis',
                    'scout' => 'Tommy Martin',
                    'medical_staff' => 'Dr. Sarah Lindsay',
                    'status' => 'active',
                    'season' => '2024/25',
                    'competition_level' => 'Premier League 2',
                    'budget_allocation' => 5000000,
                    'training_facility' => 'Carrington Training Complex',
                    'home_ground' => 'Leigh Sports Village'
                ],
                [
                    'name' => 'Youth Academy',
                    'type' => 'youth',
                    'formation' => '4-3-3',
                    'tactical_style' => 'Development-focused with emphasis on skills',
                    'playing_philosophy' => 'Technical excellence, creative play, character development',
                    'coach_name' => 'Travis Binnion',
                    'assistant_coach' => 'Colin Little',
                    'fitness_coach' => 'Mark Dempsey',
                    'goalkeeper_coach' => 'Richard Hartis',
                    'scout' => 'Jim Lawlor',
                    'medical_staff' => 'Dr. Tony Gill',
                    'status' => 'active',
                    'season' => '2024/25',
                    'competition_level' => 'U18 Premier League',
                    'budget_allocation' => 2000000,
                    'training_facility' => 'Carrington Training Complex',
                    'home_ground' => 'Carrington Training Ground'
                ]
            ];

            foreach ($teams as $teamData) {
                $team = $club->teams()->create($teamData);

                // Assign players to teams based on their existing club
                $players = \App\Models\Player::where('club_id', $club->id)->get();
                
                foreach ($players as $index => $player) {
                    $role = $index < 11 ? 'starter' : ($index < 18 ? 'substitute' : 'reserve');
                    $squadNumber = $index < 25 ? $index + 1 : null;
                    
                    \App\Models\TeamPlayer::create([
                        'team_id' => $team->id,
                        'player_id' => $player->id,
                        'role' => $role,
                        'squad_number' => $squadNumber,
                        'joined_date' => now()->subDays(rand(30, 365)),
                        'contract_end_date' => now()->addYears(rand(1, 5)),
                        'position_preference' => $player->position,
                        'status' => 'active'
                    ]);
                }
            }
        }

        // Create competitions
        $competitions = [
            [
                'name' => 'Premier League',
                'short_name' => 'EPL',
                'type' => 'league',
                'country' => 'England',
                'region' => 'Europe',
                'season' => '2024/25',
                'start_date' => '2024-08-10',
                'end_date' => '2025-05-25',
                'registration_deadline' => '2024-07-31',
                'max_teams' => 20,
                'min_teams' => 20,
                'format' => 'round_robin',
                'prize_pool' => 2500000000,
                'entry_fee' => 0,
                'status' => 'active',
                'description' => 'The top tier of English football',
                'rules' => 'Standard Premier League rules apply',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/f2/Premier_League_Logo.png',
                'website' => 'https://www.premierleague.com',
                'organizer' => 'Premier League',
                'sponsors' => 'Barclays, EA Sports, Nike',
                'broadcast_partners' => 'Sky Sports, BT Sport, Amazon Prime'
            ],
            [
                'name' => 'FA Cup',
                'short_name' => 'FA Cup',
                'type' => 'cup',
                'country' => 'England',
                'region' => 'Europe',
                'season' => '2024/25',
                'start_date' => '2024-08-15',
                'end_date' => '2025-05-17',
                'registration_deadline' => '2024-07-31',
                'max_teams' => 736,
                'min_teams' => 736,
                'format' => 'knockout',
                'prize_pool' => 20000000,
                'entry_fee' => 0,
                'status' => 'active',
                'description' => 'The oldest football competition in the world',
                'rules' => 'Single elimination knockout tournament',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/f/f2/The_F.A._Cup.svg',
                'website' => 'https://www.thefa.com/competitions/thefacup',
                'organizer' => 'The Football Association',
                'sponsors' => 'Emirates',
                'broadcast_partners' => 'BBC, ITV'
            ],
            [
                'name' => 'UEFA Champions League',
                'short_name' => 'UCL',
                'type' => 'tournament',
                'country' => 'Europe',
                'region' => 'Europe',
                'season' => '2024/25',
                'start_date' => '2024-09-17',
                'end_date' => '2025-06-01',
                'registration_deadline' => '2024-08-31',
                'max_teams' => 32,
                'min_teams' => 32,
                'format' => 'group_stage',
                'prize_pool' => 2000000000,
                'entry_fee' => 0,
                'status' => 'active',
                'description' => 'The most prestigious club competition in European football',
                'rules' => 'Group stage followed by knockout rounds',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/b/bf/UEFA_Champions_League_logo_2.svg',
                'website' => 'https://www.uefa.com/uefachampionsleague',
                'organizer' => 'UEFA',
                'sponsors' => 'Gazprom, Mastercard, PlayStation',
                'broadcast_partners' => 'BT Sport, CBS Sports'
            ]
        ];

        foreach ($competitions as $competitionData) {
            \App\Models\Competition::create($competitionData);
        }

        // Create some sample matches
        $clubs = \App\Models\Club::all();
        $competition = \App\Models\Competition::where('name', 'Premier League')->first();

        if ($clubs->count() >= 2 && $competition) {
            for ($i = 0; $i < 10; $i++) {
                $homeClub = $clubs->random();
                $awayClub = $clubs->where('id', '!=', $homeClub->id)->random();
                
                $homeTeam = $homeClub->teams()->where('type', 'first_team')->first();
                $awayTeam = $awayClub->teams()->where('type', 'first_team')->first();

                if ($homeTeam && $awayTeam) {
                    \App\Models\MatchModel::create([
                        'competition_id' => $competition->id,
                        'home_team_id' => $homeTeam->id,
                        'away_team_id' => $awayTeam->id,
                        'home_club_id' => $homeClub->id,
                        'away_club_id' => $awayClub->id,
                        'match_date' => now()->addDays(rand(1, 30)),
                        'kickoff_time' => now()->addDays(rand(1, 30))->setTime(15, 0),
                        'venue' => 'home',
                        'stadium' => $homeClub->stadium,
                        'capacity' => rand(40000, 75000),
                        'weather_conditions' => 'Clear',
                        'pitch_condition' => 'Excellent',
                        'referee' => 'Michael Oliver',
                        'assistant_referee_1' => 'Stuart Burt',
                        'assistant_referee_2' => 'Simon Bennett',
                        'fourth_official' => 'David Coote',
                        'var_referee' => 'Paul Tierney',
                        'match_status' => 'scheduled',
                        'home_score' => null,
                        'away_score' => null,
                        'home_possession' => null,
                        'away_possession' => null,
                        'home_shots' => null,
                        'away_shots' => null,
                        'home_shots_on_target' => null,
                        'away_shots_on_target' => null,
                        'home_corners' => null,
                        'away_corners' => null,
                        'home_fouls' => null,
                        'away_fouls' => null,
                        'home_offsides' => null,
                        'away_offsides' => null
                    ]);
                }
            }
        }

        $testAssociation = \App\Models\Association::where('name', 'Test Association')->first();
        Club::updateOrCreate(
            ['name' => 'Test Club'],
            [
                'short_name' => 'TClub',
                'country' => 'Testland',
                'city' => 'Test City',
                'stadium' => 'Test Stadium',
                'founded_year' => 2020,
                'logo_url' => 'https://via.placeholder.com/100x100.png?text=Test+Club',
                'website' => 'https://testclub.com',
                'email' => 'info@testclub.com',
                'phone' => '+1234567890',
                'address' => '123 Test St',
                'fifa_connect_id' => 'TEST_CLUB_001',
                'association_id' => $testAssociation ? $testAssociation->id : null,
                'league' => 'Test League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => 1000000,
                'wage_budget_eur' => 500000,
                'transfer_budget_eur' => 200000,
                'reputation' => 50,
                'facilities_level' => 2,
                'youth_development' => 2,
                'scouting_network' => 1,
                'medical_team' => 1,
                'coaching_staff' => 1,
            ]
        );

        $this->command->info('Club management system seeded successfully!');
        $this->command->info('Created ' . count($premierLeagueClubs) . ' clubs with teams and sample matches.');
    }
}
