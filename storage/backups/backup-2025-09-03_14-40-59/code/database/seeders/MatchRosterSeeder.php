<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GameMatch;
use App\Models\Team;
use App\Models\Player;
use App\Models\MatchRoster;

class MatchRosterSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing rosters
        MatchRoster::truncate();

        $matches = GameMatch::with(['homeTeam', 'awayTeam'])->get();

        foreach ($matches as $match) {
            foreach (['homeTeam', 'awayTeam'] as $side) {
                $team = $match->$side;
                if (!$team) continue;
                // Get all players for this team (by club)
                $players = Player::where('club_id', $team->club_id)->take(30)->get();
                if ($players->count() < 11) continue;
                foreach ($players as $i => $player) {
                    MatchRoster::create([
                        'match_id' => $match->id,
                        'team_id' => $team->id,
                        'player_id' => $player->id,
                        'is_starter' => $i < 11,
                        'position' => $player->position,
                        'jersey_number' => $i+1,
                    ]);
                    if ($i >= 17) break; // Only 18 per team
                }
            }
        }
    }
} 