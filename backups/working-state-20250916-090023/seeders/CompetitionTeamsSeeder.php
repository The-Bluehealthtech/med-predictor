<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Team;
use App\Models\Player;
use App\Models\TeamPlayer;
use App\Models\Association;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CompetitionTeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get the English Football Association
        $englishFA = Association::where('name', 'The Football Association')->first();
        $associationId = $englishFA ? $englishFA->id : null;

        // Create 20 additional clubs for competition
        $clubs = [];
        for ($i = 1; $i <= 20; $i++) {
            $club = Club::create([
                'name' => $faker->unique()->company . ' FC',
                'short_name' => $faker->unique()->lexify('???') . ' FC',
                'country' => 'England',
                'city' => $faker->city,
                'stadium' => $faker->city . ' Stadium',
                'stadium_name' => $faker->city . ' Stadium',
                'stadium_capacity' => $faker->numberBetween(15000, 80000),
                'founded_year' => $faker->numberBetween(1880, 2000),
                'logo_url' => null,
                'website' => $faker->url,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'fifa_connect_id' => 'COMP' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'league' => 'Premier League',
                'division' => '1',
                'status' => 'active',
                'budget_eur' => $faker->numberBetween(50000000, 300000000),
                'wage_budget_eur' => $faker->numberBetween(20000000, 100000000),
                'transfer_budget_eur' => $faker->numberBetween(10000000, 50000000),
                'reputation' => $faker->numberBetween(60, 85),
                'facilities_level' => $faker->numberBetween(3, 5),
                'youth_development' => $faker->numberBetween(3, 5),
                'scouting_network' => $faker->numberBetween(3, 5),
                'medical_team' => $faker->numberBetween(3, 5),
                'coaching_staff' => $faker->numberBetween(3, 5)
            ]);
            $clubs[] = $club;
        }

        // Create teams for each club
        $teams = [];
        foreach ($clubs as $club) {
            $team = Team::create([
                'name' => $club->name . ' First Team',
                'type' => 'first_team',
                'formation' => $faker->randomElement(['4-3-3', '4-4-2', '3-5-2', '4-2-3-1']),
                'tactical_style' => $faker->randomElement([
                    'Possession-based attacking football',
                    'Counter-attacking football',
                    'High pressing football',
                    'Defensive solidity with quick transitions'
                ]),
                'playing_philosophy' => $faker->randomElement([
                    'High pressing, quick transitions, technical excellence',
                    'Solid defense, clinical counter-attacks',
                    'Possession-based, patient build-up',
                    'Aggressive pressing, direct attacking'
                ]),
                'coach_name' => $faker->firstName . ' ' . $faker->lastName,
                'assistant_coach' => $faker->firstName . ' ' . $faker->lastName,
                'fitness_coach' => $faker->firstName . ' ' . $faker->lastName,
                'goalkeeper_coach' => $faker->firstName . ' ' . $faker->lastName,
                'scout' => $faker->firstName . ' ' . $faker->lastName,
                'medical_staff' => 'Dr. ' . $faker->firstName . ' ' . $faker->lastName,
                'status' => 'active',
                'season' => '2024/25',
                'competition_level' => 'Premier League',
                'budget_allocation' => $faker->numberBetween(5000000, 50000000),
                'training_facility' => $club->name . ' Training Complex',
                'home_ground' => $club->stadium,
                'club_id' => $club->id
            ]);
            $teams[] = $team;
        }

        // Create 30 players for each team (600 total players)
        $positions = ['GK', 'CB', 'LB', 'RB', 'DM', 'CM', 'AM', 'LW', 'RW', 'ST'];
        $preferredFeet = ['Right', 'Left', 'Both'];
        $workRates = ['Low/Low', 'Medium/Medium', 'High/High', 'High/Medium', 'Medium/High'];
        $genders = ['Male', 'Female'];

        foreach ($teams as $team) {
            for ($j = 1; $j <= 30; $j++) {
                $birthDate = $faker->dateTimeBetween('-35 years', '-16 years');
                $age = date('Y') - $birthDate->format('Y');
                
                $player = Player::create([
                    'name' => $faker->firstName . ' ' . $faker->lastName,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'birth_date' => $birthDate->format('Y-m-d'),
                    'date_of_birth' => $birthDate->format('Y-m-d'),
                    'nationality' => $faker->country,
                    'fifa_connect_id' => 'FIFA_' . $team->id . '_' . $j,
                    'team_id' => $team->id,
                    'club_id' => $team->club_id,
                    'association_id' => $associationId,
                    'position' => $faker->randomElement($positions),
                    'jersey_number' => $j,
                    'status' => 'active',
                    'overall_rating' => $faker->numberBetween(60, 95),
                    'potential_rating' => $faker->numberBetween(65, 99),
                    'age' => $age,
                    'height' => $faker->numberBetween(160, 200),
                    'weight' => $faker->numberBetween(60, 95),
                    'preferred_foot' => $faker->randomElement($preferredFeet),
                    'work_rate' => $faker->randomElement($workRates),
                    'player_face_url' => null,
                    'medical_data_payload' => null,
                    'gender' => $faker->randomElement($genders)
                ]);

                // Create team player relationship
                TeamPlayer::create([
                    'team_id' => $team->id,
                    'player_id' => $player->id,
                    'joined_date' => $faker->dateTimeBetween('-2 years', 'now'),
                    'status' => 'active'
                ]);
            }
        }

        $this->command->info('Created 20 clubs with 30 players each (600 total players) for competition!');

        // Register only the 20 most recent unique first_team teams (one per club) to the Premier League (EPL)
        $epl = \App\Models\Competition::where('name', 'Premier League')->first();
        if ($epl) {
            // Get 20 most recent unique first_team teams (one per club)
            $firstTeams = \App\Models\Team::where('type', 'first_team')->orderBy('created_at', 'desc')->get()->unique('club_id')->take(20);
            $eplTeamIds = $firstTeams->pluck('id')->toArray();
            $epl->teams()->sync($eplTeamIds);
            $this->command->info('Registered only 20 unique first teams to the Premier League (EPL).');

            // Generate full fixture list for EPL using only these teams
            $schedulingService = new \App\Services\LeagueSchedulingService();
            $startDate = now()->format('Y-m-d');
            $epl->setRelation('teams', $firstTeams);
            $schedulingService->generateFullSchedule($epl, $startDate, 7);
            $this->command->info('Generated full EPL fixture list.');
        } else {
            $this->command->warn('Premier League (EPL) competition not found. Teams not registered.');
        }
    }
} 