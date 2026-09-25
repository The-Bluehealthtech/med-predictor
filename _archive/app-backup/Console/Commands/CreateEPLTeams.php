<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Club;
use App\Models\Team;
use App\Models\Player;
use App\Models\TeamPlayer;
use Illuminate\Support\Facades\DB;

class CreateEPLTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'epl:create-teams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create first teams for all EPL clubs and assign their best players';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating EPL teams and assigning players...');

        // Get all clubs
        $clubs = Club::all();
        
        foreach ($clubs as $club) {
            $this->info("Processing club: {$club->name}");
            
            // Check if first team already exists
            $existingTeam = Team::where('club_id', $club->id)
                ->where('type', 'first_team')
                ->first();
            
            if ($existingTeam) {
                $this->warn("First team already exists for {$club->name}, skipping...");
                continue;
            }
            
            // Create first team
            $team = Team::create([
                'club_id' => $club->id,
                'name' => 'First Team',
                'type' => 'first_team',
                'formation' => '4-3-3',
                'tactical_style' => 'Possession-based attacking football',
                'playing_philosophy' => 'High pressing, quick transitions, technical excellence',
                'coach_name' => 'Head Coach',
                'budget_allocation' => 200000000,
                'season' => '2024/25',
                'status' => 'active'
            ]);
            
            $this->info("Created first team for {$club->name}");
            
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
        
        $this->info('EPL teams creation completed!');
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
