<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Club;
use App\Models\Competition;
use App\Models\MatchModel;
use App\Models\Performance;
use Carbon\Carbon;

class PerformanceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding performance data for FIT Dashboard...');
        
        // Create performance records for players
        $this->createPlayerPerformances();
        
        // Create match data for competitions
        $this->createMatchData();
        
        // Create club performance metrics
        $this->createClubPerformanceMetrics();
        
        $this->command->info('Performance data seeded successfully!');
    }

    private function createPlayerPerformances()
    {
        $players = Player::all();
        $performanceTypes = ['fitness', 'technical', 'tactical', 'mental', 'overall'];
        $metrics = [
            'fitness' => ['speed', 'stamina', 'strength', 'agility', 'recovery'],
            'technical' => ['passing', 'shooting', 'dribbling', 'defending', 'ball_control'],
            'tactical' => ['positioning', 'vision', 'decision_making', 'teamwork', 'leadership'],
            'mental' => ['confidence', 'focus', 'motivation', 'pressure_handling', 'adaptability'],
            'overall' => ['match_rating', 'consistency', 'impact', 'potential', 'form']
        ];

        foreach ($players as $player) {
            // Create performance records for the last 30 days
            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::now()->subDays($i);
                
                foreach ($performanceTypes as $type) {
                    $performanceData = [
                        'player_id' => $player->id,
                        'club_id' => $player->club_id,
                        'performance_type' => $type,
                        'date' => $date,
                        'match_date' => $date,
                        'distance_covered' => rand(8000, 12000),
                        'sprint_count' => rand(20, 60),
                        'max_speed' => rand(25, 35),
                        'avg_speed' => rand(8, 12),
                        'passes_completed' => rand(10, 80),
                        'passes_attempted' => rand(15, 100),
                        'tackles_won' => rand(0, 10),
                        'tackles_attempted' => rand(0, 15),
                        'shots_on_target' => rand(0, 5),
                        'shots_total' => rand(0, 8),
                        'goals_scored' => rand(0, 3),
                        'assists' => rand(0, 3),
                        'yellow_cards' => rand(0, 1),
                        'red_cards' => rand(0, 1),
                        'minutes_played' => rand(0, 90),
                        'rating' => rand(60, 95) / 10,
                        'overall_score' => rand(60, 95),
                        'trend' => $this->getTrend(),
                        'notes' => $this->getPerformanceNotes($type),
                        'created_at' => $date,
                        'updated_at' => $date,
                    ];

                    // Add specific metrics for each type
                    foreach ($metrics[$type] as $metric) {
                        $performanceData[$metric] = rand(50, 100);
                    }

                    // Add match-specific data if it's a match day
                    if ($i % 3 == 0) { // Every 3rd day is a match
                        $performanceData['match_id'] = rand(1, 100);
                        $performanceData['minutes_played'] = rand(0, 90);
                        $performanceData['goals_scored'] = rand(0, 3);
                        $performanceData['assists'] = rand(0, 3);
                        $performanceData['passes_completed'] = rand(10, 80);
                        $performanceData['pass_accuracy'] = rand(70, 95);
                        $performanceData['tackles_won'] = rand(0, 10);
                        $performanceData['interceptions'] = rand(0, 8);
                        $performanceData['distance_covered'] = rand(8000, 12000);
                        $performanceData['sprints'] = rand(20, 60);
                    }

                    Performance::create($performanceData);
                }
            }
        }
    }

    private function createMatchData()
    {
        $competitions = Competition::all();
        $clubs = Club::all();
        
        // Create matches for the last 3 months
        for ($i = 0; $i < 90; $i++) {
            $date = Carbon::now()->subDays($i);
            
            if ($i % 3 == 0) { // Every 3rd day has matches
                $numMatches = rand(3, 8);
                
                for ($j = 0; $j < $numMatches; $j++) {
                    $homeClub = $clubs->random();
                    $awayClub = $clubs->where('id', '!=', $homeClub->id)->random();
                    $competition = $competitions->random();
                    
                    $homeGoals = rand(0, 4);
                    $awayGoals = rand(0, 4);
                    
                    MatchModel::create([
                        'competition_id' => $competition->id,
                        'home_team_id' => $homeClub->id,
                        'away_team_id' => $awayClub->id,
                        'match_date' => $date,
                        'home_score' => $homeGoals,
                        'away_score' => $awayGoals,
                        'status' => 'completed',
                        'venue' => $homeClub->stadium ?? 'Home Stadium',
                        'attendance' => rand(20000, 80000),
                        'weather' => $this->getRandomWeather(),
                        'referee' => 'Referee ' . rand(1, 20),
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }
            }
        }
    }

    private function createClubPerformanceMetrics()
    {
        $clubs = Club::all();
        
        foreach ($clubs as $club) {
            // Update club with performance metrics
            $club->update([
                'reputation' => rand(70, 95),
                'facilities_level' => rand(3, 5),
                'youth_development' => rand(2, 5),
                'scouting_network' => rand(2, 5),
                'medical_team' => rand(3, 5),
                'coaching_staff' => rand(3, 5),
            ]);
        }
    }

    private function getTrend()
    {
        $trends = ['improving', 'stable', 'declining', 'fluctuating'];
        return $trends[array_rand($trends)];
    }

    private function getPerformanceNotes($type)
    {
        $notes = [
            'fitness' => [
                'Excellent physical condition maintained',
                'Recovery protocols followed effectively',
                'Strength training showing results',
                'Speed and agility improving',
                'Stamina levels optimal'
            ],
            'technical' => [
                'Technical skills at peak level',
                'Ball control exceptional',
                'Passing accuracy improved',
                'Shooting technique refined',
                'Defensive positioning excellent'
            ],
            'tactical' => [
                'Tactical awareness outstanding',
                'Team coordination improved',
                'Decision making under pressure',
                'Leadership qualities evident',
                'Positional discipline maintained'
            ],
            'mental' => [
                'Mental strength exceptional',
                'Pressure handling improved',
                'Confidence levels high',
                'Focus maintained throughout',
                'Motivation consistently high'
            ],
            'overall' => [
                'Overall performance excellent',
                'Consistent high-level displays',
                'Key player impact evident',
                'Potential being realized',
                'Form at peak level'
            ]
        ];
        
        return $notes[$type][array_rand($notes[$type])];
    }

    private function getRandomWeather()
    {
        $weather = ['Sunny', 'Cloudy', 'Rainy', 'Windy', 'Clear'];
        return $weather[array_rand($weather)];
    }
} 