<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MatchSheet;
use App\Models\MatchModel;
use App\Models\User;
use App\Models\Competition;

class MatchSheetSeeder extends Seeder
{
    public function run()
    {
        // Get the Premier League competition
        $competition = Competition::where('name', 'Premier League')->first();
        
        if (!$competition) {
            $this->command->error('Premier League competition not found!');
            return;
        }

        // Get some matches for the Premier League
        $matches = MatchModel::where('competition_id', $competition->id)
            ->limit(10) // Create match sheets for first 10 matches
            ->get();

        if ($matches->isEmpty()) {
            $this->command->error('No matches found for Premier League!');
            return;
        }

        // Get a user to assign as referee
        $referee = User::first();
        
        $createdCount = 0;
        
        foreach ($matches as $match) {
            // Check if match sheet already exists
            $existingSheet = MatchSheet::where('match_id', $match->id)->first();
            
            if ($existingSheet) {
                continue; // Skip if already exists
            }

            // Create match sheet with only available fields
            MatchSheet::create([
                'match_id' => $match->id,
                'match_number' => 'PL' . str_pad($match->id, 4, '0', STR_PAD_LEFT),
                'stadium_venue' => $match->stadium ?? 'Home Stadium',
                'weather_conditions' => 'Clear',
                'pitch_conditions' => 'Excellent',
                'kickoff_time' => $match->kickoff_time ?? now(),
                'home_team_score' => null,
                'away_team_score' => null,
                'main_referee_id' => $referee ? $referee->id : null,
                'assistant_referee_1_id' => $referee ? $referee->id : null,
                'assistant_referee_2_id' => $referee ? $referee->id : null,
                'fourth_official_id' => $referee ? $referee->id : null,
                'var_referee_id' => $referee ? $referee->id : null,
                'var_assistant_id' => $referee ? $referee->id : null,
                'match_observer_id' => $referee ? $referee->id : null,
                'stage' => 'pre_match',
                'status' => 'draft',
                'referee_report' => null,
                'crowd_issues' => null,
                'protests_incidents' => null,
                'suspension_reason' => null,
                'notes' => 'Match sheet created automatically',
                'assigned_referee_id' => $referee ? $referee->id : null,
                'referee_assigned_at' => now(),
                'home_team_lineup_signed_at' => null,
                'away_team_lineup_signed_at' => null,
                'stage_fa_validated_at' => null,
            ]);

            $createdCount++;
        }

        $this->command->info("Created {$createdCount} match sheets for Premier League matches!");
    }
} 