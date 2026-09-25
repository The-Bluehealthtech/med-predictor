<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompetitionRanking;
use App\Models\Competition;

class CompetitionRankingSeeder extends Seeder
{
    public function run()
    {
        // Get the Premier League competition
        $competition = Competition::where('name', 'Premier League')->first();
        
        if (!$competition) {
            $this->command->error('Premier League competition not found!');
            return;
        }

        // Sample standings data for Premier League
        $standings = [
            1 => [
                'club_name' => 'Manchester City',
                'played' => 8,
                'won' => 8,
                'drawn' => 5,
                'lost' => 5,
                'goals_for' => 94,
                'goals_against' => 33,
                'goal_difference' => 61,
                'points' => 89
            ],
            2 => [
                'club_name' => 'Arsenal',
                'played' => 8,
                'won' => 6,
                'drawn' => 6,
                'lost' => 6,
                'goals_for' => 88,
                'goals_against' => 43,
                'goal_difference' => 45,
                'points' => 84
            ],
            3 => [
                'club_name' => 'Manchester United',
                'played' => 8,
                'won' => 3,
                'drawn' => 6,
                'lost' => 9,
                'goals_for' => 58,
                'goals_against' => 43,
                'goal_difference' => 15,
                'points' => 75
            ],
            4 => [
                'club_name' => 'Newcastle United',
                'played' => 8,
                'won' => 9,
                'drawn' => 4,
                'lost' => 5,
                'goals_for' => 68,
                'goals_against' => 33,
                'goal_difference' => 35,
                'points' => 71
            ],
            5 => [
                'club_name' => 'Liverpool',
                'played' => 8,
                'won' => 9,
                'drawn' => 0,
                'lost' => 9,
                'goals_for' => 75,
                'goals_against' => 47,
                'goal_difference' => 28,
                'points' => 67
            ],
            6 => [
                'club_name' => 'Brighton & Hove Albion',
                'played' => 8,
                'won' => 8,
                'drawn' => 8,
                'lost' => 2,
                'goals_for' => 72,
                'goals_against' => 53,
                'goal_difference' => 19,
                'points' => 62
            ],
            7 => [
                'club_name' => 'Aston Villa',
                'played' => 8,
                'won' => 8,
                'drawn' => 7,
                'lost' => 3,
                'goals_for' => 51,
                'goals_against' => 46,
                'goal_difference' => 5,
                'points' => 61
            ],
            8 => [
                'club_name' => 'Tottenham Hotspur',
                'played' => 8,
                'won' => 8,
                'drawn' => 6,
                'lost' => 4,
                'goals_for' => 70,
                'goals_against' => 63,
                'goal_difference' => 7,
                'points' => 60
            ],
            9 => [
                'club_name' => 'Fulham',
                'played' => 8,
                'won' => 5,
                'drawn' => 4,
                'lost' => 9,
                'goals_for' => 58,
                'goals_against' => 46,
                'goal_difference' => 12,
                'points' => 59
            ],
            10 => [
                'club_name' => 'West Ham United',
                'played' => 8,
                'won' => 5,
                'drawn' => 7,
                'lost' => 6,
                'goals_for' => 55,
                'goals_against' => 53,
                'goal_difference' => 2,
                'points' => 52
            ],
            11 => [
                'club_name' => 'Crystal Palace',
                'played' => 8,
                'won' => 1,
                'drawn' => 2,
                'lost' => 5,
                'goals_for' => 40,
                'goals_against' => 49,
                'goal_difference' => -9,
                'points' => 45
            ],
            12 => [
                'club_name' => 'Chelsea',
                'played' => 8,
                'won' => 1,
                'drawn' => 1,
                'lost' => 6,
                'goals_for' => 38,
                'goals_against' => 47,
                'goal_difference' => -9,
                'points' => 44
            ],
            13 => [
                'club_name' => 'Wolverhampton Wanderers',
                'played' => 8,
                'won' => 1,
                'drawn' => 8,
                'lost' => 9,
                'goals_for' => 31,
                'goals_against' => 58,
                'goal_difference' => -27,
                'points' => 41
            ],
            14 => [
                'club_name' => 'West Ham United',
                'played' => 8,
                'won' => 1,
                'drawn' => 7,
                'lost' => 0,
                'goals_for' => 42,
                'goals_against' => 55,
                'goal_difference' => -13,
                'points' => 40
            ],
            15 => [
                'club_name' => 'Bournemouth',
                'played' => 8,
                'won' => 1,
                'drawn' => 6,
                'lost' => 1,
                'goals_for' => 37,
                'goals_against' => 71,
                'goal_difference' => -34,
                'points' => 39
            ],
            16 => [
                'club_name' => 'Nottingham Forest',
                'played' => 8,
                'won' => 9,
                'drawn' => 1,
                'lost' => 8,
                'goals_for' => 38,
                'goals_against' => 68,
                'goal_difference' => -30,
                'points' => 38
            ],
            17 => [
                'club_name' => 'Everton',
                'played' => 8,
                'won' => 8,
                'drawn' => 2,
                'lost' => 8,
                'goals_for' => 34,
                'goals_against' => 57,
                'goal_difference' => -23,
                'points' => 36
            ],
            18 => [
                'club_name' => 'Leicester City',
                'played' => 8,
                'won' => 9,
                'drawn' => 7,
                'lost' => 2,
                'goals_for' => 51,
                'goals_against' => 68,
                'goal_difference' => -17,
                'points' => 34
            ],
            19 => [
                'club_name' => 'Leeds United',
                'played' => 8,
                'won' => 7,
                'drawn' => 0,
                'lost' => 1,
                'goals_for' => 48,
                'goals_against' => 78,
                'goal_difference' => -30,
                'points' => 31
            ],
            20 => [
                'club_name' => 'Southampton',
                'played' => 8,
                'won' => 6,
                'drawn' => 7,
                'lost' => 5,
                'goals_for' => 36,
                'goals_against' => 73,
                'goal_difference' => -37,
                'points' => 25
            ]
        ];

        // Create the competition ranking
        CompetitionRanking::create([
            'competition_id' => $competition->id,
            'round' => 38,
            'standings' => json_encode($standings),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->command->info('Competition ranking created successfully for Premier League!');
    }
} 