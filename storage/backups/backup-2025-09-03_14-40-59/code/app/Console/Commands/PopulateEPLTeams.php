<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Club;
use App\Models\Team;
use App\Models\Player;
use App\Models\TeamPlayer;
use Illuminate\Support\Facades\DB;

class PopulateEPLTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'epl:populate-teams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate all EPL first teams with their best players';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Populating EPL teams with players...');

        // Get all clubs
        $clubs = Club::all();
        
        foreach ($clubs as $club) {
            $this->info("Processing club: {$club->name}");
            
            // Get the first team for this club
            $team = Team::where('club_id', $club->id)
                ->where('type', 'first_team')
                ->first();
            
            if (!$team) {
                $this->error("No first team found for {$club->name}");
                continue;
            }
            
            // Clear existing players from this team
            $existingPlayers = $team->players()->count();
            if ($existingPlayers > 0) {
                $this->warn("Removing {$existingPlayers} existing players from {$club->name}");
                $team->players()->delete();
            }
            
            // Get the best 25 players from this club (first team squad)
            $players = Player::where('club_id', $club->id)
                ->orderBy('overall_rating', 'desc')
                ->take(25)
                ->get();
            
            $this->info("Found {$players->count()} players for {$club->name}");
            
            // Assign players to team with appropriate roles
            foreach ($players as $index => $player) {
                $role = $index < 11 ? 'starter' : 'substitute';
                $jerseyNumber = $index + 1;
                
                // Map player positions to team positions
                $position = $this->mapPosition($player->position);
                
                TeamPlayer::create([
                    'team_id' => $team->id,
                    'player_id' => $player->id,
                    'role' => $role,
                    'squad_number' => $jerseyNumber,
                    'joined_date' => now(),
                    'status' => 'active',
                    'position_preference' => $position
                ]);
            }
            
            $this->info("Assigned {$players->count()} players to {$club->name} first team");
        }
        
        $this->info('EPL teams population completed!');
        
        // Show summary
        $this->info('Summary:');
        $teams = Team::where('type', 'first_team')->with('club')->get();
        foreach ($teams as $team) {
            $playerCount = $team->players()->count();
            $this->line("- {$team->club->name}: {$playerCount} players");
        }
    }
    
    private function mapPosition($playerPosition)
    {
        // Map player positions to team positions
        $positionMap = [
            'GK' => 'GK',
            'CB' => 'CB',
            'RB' => 'RB',
            'LB' => 'LB',
            'CDM' => 'CDM',
            'CM' => 'CM',
            'CAM' => 'CAM',
            'RW' => 'RW',
            'LW' => 'LW',
            'ST' => 'ST'
        ];
        
        return $positionMap[$playerPosition] ?? 'CM';
    }
}
