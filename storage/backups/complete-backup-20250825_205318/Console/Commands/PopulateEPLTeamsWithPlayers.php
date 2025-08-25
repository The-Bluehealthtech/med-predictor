<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Competition;
use App\Models\Club;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamPlayer;
use Illuminate\Support\Facades\DB;

class PopulateEPLTeamsWithPlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'epl:populate-teams-with-players';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate EPL teams with players from their respective clubs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Populating EPL teams with players...');

        // Get the EPL competition
        $epl = Competition::where('short_name', 'EPL')->first();
        
        if (!$epl) {
            $this->error('EPL competition not found!');
            return 1;
        }

        $epl->load('teams.club.players');
        
        $totalTeams = $epl->teams->count();
        $this->info("Found {$totalTeams} teams in EPL competition.");

        $teamsProcessed = 0;
        $playersAdded = 0;

        foreach ($epl->teams as $team) {
            $this->info("Processing {$team->club->name} - {$team->name}...");
            
            // Check if team already has players
            $existingPlayers = $team->players()->count();
            if ($existingPlayers > 0) {
                $this->info("  Team already has {$existingPlayers} players. Skipping...");
                $teamsProcessed++;
                continue;
            }

            // Get players from the club
            $clubPlayers = $team->club->players()->orderBy('overall_rating', 'desc')->get();
            
            if ($clubPlayers->count() == 0) {
                $this->warn("  No players found in club {$team->club->name}. Creating players...");
                $clubPlayers = $this->createPlayersForClub($team->club);
            }

            // Add players to team (limit to 25 players per team)
            $playersToAdd = $clubPlayers->take(25);
            
            foreach ($playersToAdd as $index => $player) {
                $role = $index < 11 ? 'starter' : ($index < 18 ? 'substitute' : 'reserve');
                $squadNumber = $index < 25 ? $index + 1 : null;
                
                TeamPlayer::create([
                    'team_id' => $team->id,
                    'player_id' => $player->id,
                    'role' => $role,
                    'squad_number' => $squadNumber,
                    'joined_date' => now()->subDays(rand(30, 365)),
                    'contract_end_date' => now()->addYears(rand(1, 5)),
                    'position_preference' => $player->position,
                    'status' => 'active'
                ]);
                
                $playersAdded++;
            }

            $this->info("  Added {$playersToAdd->count()} players to {$team->club->name} - {$team->name}");
            $teamsProcessed++;
        }

        $this->info("Successfully processed {$teamsProcessed} teams and added {$playersAdded} players!");
        
        // Verify the results
        $this->info("\nVerification:");
        $epl->load('teams.players');
        foreach ($epl->teams as $team) {
            $this->info("  {$team->club->name} - {$team->name}: {$team->players->count()} players");
        }

        return 0;
    }

    private function createPlayersForClub($club)
    {
        $this->info("    Creating 30 players for {$club->name}...");
        
        $positions = ['GK', 'CB', 'LB', 'RB', 'DM', 'CM', 'AM', 'LW', 'RW', 'ST'];
        $firstNames = [
            'James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard', 'Joseph', 'Thomas', 'Christopher',
            'Charles', 'Daniel', 'Matthew', 'Anthony', 'Mark', 'Donald', 'Steven', 'Paul', 'Andrew', 'Joshua',
            'Kenneth', 'Kevin', 'Brian', 'George', 'Edward', 'Ronald', 'Timothy', 'Jason', 'Jeffrey', 'Ryan'
        ];
        $lastNames = [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
            'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin',
            'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson'
        ];

        $players = collect();

        for ($i = 0; $i < 30; $i++) {
            $position = $positions[array_rand($positions)];
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            
            // Generate realistic ratings based on position
            $overallRating = $this->generatePositionBasedRating($position);
            
            $player = Player::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'date_of_birth' => now()->subYears(rand(18, 35))->subDays(rand(0, 365)),
                'nationality' => 'England',
                'position' => $position,
                'height_cm' => rand(165, 195),
                'weight_kg' => rand(65, 85),
                'preferred_foot' => rand(0, 1) ? 'right' : 'left',
                'overall_rating' => $overallRating,
                'potential_rating' => min(99, $overallRating + rand(0, 10)),
                'pace' => rand(40, 95),
                'shooting' => rand(40, 95),
                'passing' => rand(40, 95),
                'dribbling' => rand(40, 95),
                'defending' => rand(40, 95),
                'physical' => rand(40, 95),
                'stamina' => rand(40, 95),
                'strength' => rand(40, 95),
                'agility' => rand(40, 95),
                'balance' => rand(40, 95),
                'reactions' => rand(40, 95),
                'ball_control' => rand(40, 95),
                'composure' => rand(40, 95),
                'interceptions' => rand(40, 95),
                'heading_accuracy' => rand(40, 95),
                'marking' => rand(40, 95),
                'standing_tackle' => rand(40, 95),
                'sliding_tackle' => rand(40, 95),
                'vision' => rand(40, 95),
                'crossing' => rand(40, 95),
                'free_kick_accuracy' => rand(40, 95),
                'long_passing' => rand(40, 95),
                'curve' => rand(40, 95),
                'finishing' => rand(40, 95),
                'long_shots' => rand(40, 95),
                'penalties' => rand(40, 95),
                'volleys' => rand(40, 95),
                'acceleration' => rand(40, 95),
                'sprint_speed' => rand(40, 95),
                'aggression' => rand(40, 95),
                'jumping' => rand(40, 95),
                'club_id' => $club->id,
                'status' => 'active',
                'contract_start_date' => now()->subYears(rand(1, 5)),
                'contract_end_date' => now()->addYears(rand(1, 5)),
                'wage_eur' => rand(50000, 200000),
                'value_eur' => rand(1000000, 50000000),
                'fifa_connect_id' => $club->fifa_connect_id . '_P' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
            ]);

            $players->push($player);
        }

        return $players;
    }

    private function generatePositionBasedRating($position)
    {
        // Generate realistic ratings based on position
        switch ($position) {
            case 'GK':
                return rand(70, 85);
            case 'CB':
                return rand(72, 87);
            case 'LB':
            case 'RB':
                return rand(70, 86);
            case 'DM':
                return rand(71, 88);
            case 'CM':
                return rand(70, 89);
            case 'AM':
                return rand(71, 90);
            case 'LW':
            case 'RW':
                return rand(70, 91);
            case 'ST':
                return rand(72, 92);
            default:
                return rand(70, 85);
        }
    }
} 