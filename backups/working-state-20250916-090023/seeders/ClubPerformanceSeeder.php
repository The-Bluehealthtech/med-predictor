<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamPlayer;
use Carbon\Carbon;

class ClubPerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding club performance data...');
        
        // Get existing clubs
        $clubs = Club::all();
        
        if ($clubs->isEmpty()) {
            $this->command->warn('No clubs found. Please run ClubSeeder first.');
            return;
        }

        // Performance metrics for each club
        $clubPerformanceData = [
            [
                'name' => 'Manchester United',
                'total_players' => 28,
                'average_rating' => 82.5,
                'total_value' => 850000000,
                'wins' => 18,
                'draws' => 8,
                'losses' => 6,
                'goals_scored' => 65,
                'goals_conceded' => 32,
                'points' => 62,
                'position' => 3,
                'performance_score' => 85.2
            ],
            [
                'name' => 'Liverpool FC',
                'total_players' => 26,
                'average_rating' => 83.2,
                'total_value' => 920000000,
                'wins' => 20,
                'draws' => 6,
                'losses' => 4,
                'goals_scored' => 72,
                'goals_conceded' => 28,
                'points' => 66,
                'position' => 2,
                'performance_score' => 88.7
            ],
            [
                'name' => 'Arsenal FC',
                'total_players' => 25,
                'average_rating' => 81.8,
                'total_value' => 780000000,
                'wins' => 19,
                'draws' => 7,
                'losses' => 4,
                'goals_scored' => 68,
                'goals_conceded' => 30,
                'points' => 64,
                'position' => 1,
                'performance_score' => 87.3
            ],
            [
                'name' => 'Chelsea FC',
                'total_players' => 27,
                'average_rating' => 82.1,
                'total_value' => 890000000,
                'wins' => 16,
                'draws' => 9,
                'losses' => 7,
                'goals_scored' => 58,
                'goals_conceded' => 35,
                'points' => 57,
                'position' => 4,
                'performance_score' => 82.1
            ],
            [
                'name' => 'Manchester City',
                'total_players' => 24,
                'average_rating' => 84.5,
                'total_value' => 950000000,
                'wins' => 17,
                'draws' => 8,
                'losses' => 5,
                'goals_scored' => 70,
                'goals_conceded' => 29,
                'points' => 59,
                'position' => 5,
                'performance_score' => 86.4
            ],
            [
                'name' => 'Tottenham Hotspur',
                'total_players' => 23,
                'average_rating' => 80.3,
                'total_value' => 720000000,
                'wins' => 15,
                'draws' => 6,
                'losses' => 9,
                'goals_scored' => 52,
                'goals_conceded' => 38,
                'points' => 51,
                'position' => 6,
                'performance_score' => 78.9
            ],
            [
                'name' => 'Barcelona',
                'total_players' => 26,
                'average_rating' => 83.7,
                'total_value' => 880000000,
                'wins' => 21,
                'draws' => 5,
                'losses' => 4,
                'goals_scored' => 75,
                'goals_conceded' => 25,
                'points' => 68,
                'position' => 1,
                'performance_score' => 91.2
            ],
            [
                'name' => 'Real Madrid',
                'total_players' => 25,
                'average_rating' => 84.2,
                'total_value' => 920000000,
                'wins' => 20,
                'draws' => 6,
                'losses' => 4,
                'goals_scored' => 73,
                'goals_conceded' => 27,
                'points' => 66,
                'position' => 2,
                'performance_score' => 89.5
            ]
        ];

        foreach ($clubPerformanceData as $index => $performanceData) {
            $club = $clubs->get($index);
            if (!$club) continue;

            // Update club with performance data
            $club->update([
                'reputation' => rand(70, 95),
                'facilities_level' => rand(3, 5),
                'youth_development' => rand(2, 5),
                'scouting_network' => rand(2, 5),
                'medical_team' => rand(3, 5),
                'coaching_staff' => rand(3, 5),
                'budget_eur' => $performanceData['total_value'],
                'wage_budget_eur' => $performanceData['total_value'] * 0.6,
                'transfer_budget_eur' => $performanceData['total_value'] * 0.2,
            ]);

            // Create or update team for the club
            $team = Team::firstOrCreate(
                ['club_id' => $club->id, 'type' => 'first_team'],
                [
                    'name' => $club->name . ' First Team',
                    'formation' => '4-3-3',
                    'tactical_style' => 'possession',
                    'playing_philosophy' => 'attacking',
                    'coach_name' => 'Head Coach',
                    'status' => 'active',
                    'season' => '2024/25',
                    'competition_level' => 'premier_league',
                    'budget_allocation' => $performanceData['total_value'] * 0.4,
                    'training_facility' => 'state_of_the_art',
                    'home_ground' => $club->stadium ?? 'Home Stadium',
                    'founded_year' => $club->founded_year,
                    'capacity' => rand(40000, 80000),
                    'colors' => json_encode(['primary' => '#000000', 'secondary' => '#FFFFFF']),
                    'logo_url' => $club->logo_url,
                    'website' => $club->website,
                    'description' => 'First team of ' . $club->name,
                ]
            );

            // Get players for this club
            $clubPlayers = Player::where('club_id', $club->id)->get();
            
            // If no players exist, create some
            if ($clubPlayers->isEmpty()) {
                $this->createPlayersForClub($club, $performanceData['total_players']);
                $clubPlayers = Player::where('club_id', $club->id)->get();
            }

            // Assign players to team
            $usedSquadNumbers = [];
            foreach ($clubPlayers as $player) {
                // Generate unique squad number
                do {
                    $squadNumber = rand(1, 99);
                } while (in_array($squadNumber, $usedSquadNumbers));
                $usedSquadNumbers[] = $squadNumber;
                
                TeamPlayer::firstOrCreate(
                    ['team_id' => $team->id, 'player_id' => $player->id],
                    [
                        'role' => $this->getRandomRole(),
                        'squad_number' => $squadNumber,
                        'joined_date' => Carbon::now()->subMonths(rand(1, 24)),
                        'contract_end_date' => Carbon::now()->addYears(rand(1, 5)),
                        'position_preference' => $player->position,
                        'status' => 'active',
                    ]
                );
            }

            $this->command->info("Updated {$club->name} with performance data");
        }

        $this->command->info('Club performance data seeded successfully!');
    }

    private function createPlayersForClub($club, $playerCount)
    {
        $positions = ['GK', 'CB', 'RB', 'LB', 'CDM', 'CM', 'CAM', 'RW', 'LW', 'ST'];
        $nationalities = ['England', 'Spain', 'France', 'Germany', 'Brazil', 'Argentina', 'Portugal', 'Netherlands', 'Belgium', 'Italy'];
        
        for ($i = 1; $i <= $playerCount; $i++) {
            $position = $positions[array_rand($positions)];
            $nationality = $nationalities[array_rand($nationalities)];
            $overallRating = rand(70, 90);
            
            Player::create([
                'name' => "Player {$i}",
                'first_name' => "First{$i}",
                'last_name' => "Last{$i}",
                'date_of_birth' => Carbon::now()->subYears(rand(18, 35)),
                'nationality' => $nationality,
                'position' => $position,
                'club_id' => $club->id,
                'association_id' => $club->association_id,
                'height' => rand(165, 195),
                'weight' => rand(65, 85),
                'preferred_foot' => rand(0, 1) ? 'Right' : 'Left',
                'weak_foot' => rand(1, 5),
                'skill_moves' => rand(1, 5),
                'international_reputation' => rand(1, 5),
                'work_rate' => 'Medium/Medium',
                'body_type' => 'Normal',
                'real_face' => false,
                'release_clause_eur' => rand(10000000, 100000000),
                'overall_rating' => $overallRating,
                'potential_rating' => $overallRating + rand(0, 5),
                'value_eur' => rand(5000000, 50000000),
                'wage_eur' => rand(50000, 200000),
                'age' => rand(18, 35),
                'contract_valid_until' => Carbon::now()->addYears(rand(1, 5)),
                'fifa_version' => 'FIFA 24',
                'last_updated' => Carbon::now(),
            ]);
        }
    }

    private function getRandomRole()
    {
        $roles = ['starter', 'substitute', 'reserve'];
        $weights = [40, 35, 25]; // 40% starters, 35% substitutes, 25% reserves
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $roles[$index];
            }
        }
        
        return 'substitute';
    }
} 