<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Player;
use App\Models\Club;
use App\Models\Team;
use App\Models\Competition;
use App\Models\PlayerLicense;
use App\Models\GameMatch;
use App\Models\MatchSheet;
use App\Models\MatchRoster;
use App\Models\MatchRosterPlayer;
use App\Models\MatchEvent;
use App\Models\FifaConnectId;
use Illuminate\Support\Facades\DB;

class CompleteSystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Complete System Seeder...');

        // 1. Create FIFA Connect IDs for all stakeholders
        $this->createFifaConnectIdsForStakeholders();

        // 2. Create player licenses for Premier League players
        $this->createPlayerLicensesForPlayers();

        // 3. Create players for Premier League clubs
        $this->createPlayersForPremierLeagueClubs();

        // 4. Create match rosters for all matches
        $this->createMatchRosters();

        // 5. Create match events for some matches
        $this->createMatchEvents();

        // 6. Update match sheets with stage data
        $this->updateMatchSheets();

        $this->command->info('Complete System Seeder finished successfully!');
    }

    private function createFifaConnectIdsForStakeholders(): void
    {
        $this->command->info('Creating FIFA Connect IDs for stakeholders...');

        $users = User::all();
        $createdCount = 0;

        foreach ($users as $user) {
            if (!$user->fifaConnectId) {
                $fifaId = FifaConnectId::create([
                    'fifa_id' => 'FIFA_' . strtoupper($user->role) . '_' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'entity_type' => 'user',
                    'entity_id' => $user->id,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $user->update(['fifa_connect_id' => $fifaId->id]);
                $createdCount++;
            }
        }

        $this->command->info("Created {$createdCount} FIFA Connect IDs for stakeholders");
    }

    private function createPlayerLicensesForPlayers(): void
    {
        $this->command->info('Creating player licenses for Premier League players...');

        $premierLeague = Competition::where('name', 'Premier League')->first();
        if (!$premierLeague) {
            $this->command->error('Premier League competition not found!');
            return;
        }

        $teams = $premierLeague->teams()->with('club')->get();
        $createdCount = 0;

        foreach ($teams as $team) {
            $club = $team->club;
            
            // Get players for this club
            $players = Player::where('club_id', $club->id)->get();
            
            foreach ($players as $player) {
                // Check if license already exists
                $existingLicense = PlayerLicense::where('player_id', $player->id)->first();
                if (!$existingLicense) {
                    PlayerLicense::create([
                        'player_id' => $player->id,
                        'club_id' => $club->id,
                        'license_type' => 'professional',
                        'license_category' => 'A',
                        'status' => 'active',
                        'approval_status' => 'approved',
                        'license_number' => 'ENG-' . date('Y') . '-' . str_pad($player->id, 6, '0', STR_PAD_LEFT),
                        'registration_number' => 'REG-' . str_pad($player->id, 8, '0', STR_PAD_LEFT),
                        'issue_date' => now()->subMonths(rand(1, 12)),
                        'expiry_date' => now()->addYears(rand(1, 3)),
                        'renewal_date' => now()->addYears(rand(1, 2)),
                        'issuing_authority' => 'The Football Association',
                        'transfer_status' => 'registered',
                        'contract_type' => 'permanent',
                        'contract_start_date' => now()->subYears(rand(1, 3)),
                        'contract_end_date' => now()->addYears(rand(1, 5)),
                        'wage_agreement' => rand(5000, 200000),
                        'release_clause' => rand(1000000, 50000000),
                        'medical_clearance' => 'Cleared for professional football',
                        'fitness_certificate' => true,
                        'disciplinary_record' => 'Clean record',
                        'international_clearance' => true,
                        'work_permit' => true,
                        'visa_status' => 'Valid',
                        'documentation_status' => 'Complete',
                        'approved_by' => User::where('role', 'association_admin')->first()?->id,
                        'approved_at' => now()->subDays(rand(1, 30)),
                        'notes' => 'Automatically approved for Premier League registration',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $createdCount++;
                }
            }
        }

        $this->command->info("Created {$createdCount} player licenses for Premier League players");
    }

    private function createPlayersForPremierLeagueClubs(): void
    {
        $this->command->info('Creating players for Premier League clubs...');

        $premierLeague = Competition::where('name', 'Premier League')->first();
        if (!$premierLeague) {
            $this->command->error('Premier League competition not found!');
            return;
        }

        $teams = $premierLeague->teams()->with('club')->get();
        $createdCount = 0;

        foreach ($teams as $team) {
            $club = $team->club;
            
            // Check if players already exist for this club
            $existingPlayers = Player::where('club_id', $club->id)->count();
            if ($existingPlayers > 0) {
                $this->command->info("Players already exist for {$club->name}, skipping...");
                continue;
            }
            
            // Create 30 players per team (25 squad + 5 reserves)
            for ($i = 1; $i <= 30; $i++) {
                $playerData = $this->generatePlayerData($club, $i);
                
                // Create FIFA Connect ID for player
                $fifaId = FifaConnectId::create([
                    'fifa_id' => 'FIFA_PLAYER_' . str_pad($club->id, 3, '0', STR_PAD_LEFT) . '_' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'entity_type' => 'player',
                    'entity_id' => null, // Will be updated after player creation
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create player
                $player = Player::create([
                    'fifa_connect_id' => $fifaId->id,
                    'name' => $playerData['name'],
                    'first_name' => $playerData['first_name'],
                    'last_name' => $playerData['last_name'],
                    'date_of_birth' => $playerData['date_of_birth'],
                    'nationality' => $playerData['nationality'],
                    'position' => $playerData['position'],
                    'club_id' => $club->id,
                    'association_id' => $club->association_id,
                    'height' => $playerData['height'],
                    'weight' => $playerData['weight'],
                    'preferred_foot' => $playerData['preferred_foot'],
                    'weak_foot' => $playerData['weak_foot'],
                    'skill_moves' => $playerData['skill_moves'],
                    'international_reputation' => $playerData['international_reputation'],
                    'work_rate' => $playerData['work_rate'],
                    'body_type' => $playerData['body_type'],
                    'real_face' => false,
                    'release_clause_eur' => $playerData['release_clause_eur'],
                    'overall_rating' => $playerData['overall_rating'],
                    'potential_rating' => $playerData['potential_rating'],
                    'value_eur' => $playerData['value_eur'],
                    'wage_eur' => $playerData['wage_eur'],
                    'age' => $playerData['age'],
                    'contract_valid_until' => now()->addYears(rand(1, 5)),
                    'fifa_version' => '2024',
                    'last_updated' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update FIFA Connect ID with player ID
                $fifaId->update(['entity_id' => $player->id]);

                $createdCount++;
            }
        }

        $this->command->info("Created {$createdCount} players for Premier League clubs");
    }

    private function generatePlayerData($club, $playerNumber): array
    {
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

        $positions = ['GK', 'CB', 'LB', 'RB', 'CDM', 'CM', 'CAM', 'LM', 'RM', 'LW', 'RW', 'ST'];
        $nationalities = ['England', 'Spain', 'France', 'Germany', 'Italy', 'Brazil', 'Argentina', 'Portugal', 'Netherlands', 'Belgium'];
        $preferredFeet = ['Right', 'Left'];
        $workRates = ['High/High', 'High/Medium', 'Medium/High', 'Medium/Medium', 'Medium/Low', 'Low/Medium', 'Low/Low'];
        $bodyTypes = ['Lean', 'Normal', 'Stocky'];

        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $position = $positions[array_rand($positions)];
        $nationality = $nationalities[array_rand($nationalities)];
        $age = rand(18, 35);
        $height = rand(165, 195);
        $weight = rand(65, 85);
        $overallRating = rand(65, 85);
        $potentialRating = min(95, $overallRating + rand(0, 10));

        return [
            'name' => $firstName . ' ' . $lastName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'date_of_birth' => now()->subYears($age)->subDays(rand(0, 365)),
            'nationality' => $nationality,
            'position' => $position,
            'height' => $height,
            'weight' => $weight,
            'preferred_foot' => $preferredFeet[array_rand($preferredFeet)],
            'weak_foot' => rand(1, 5),
            'skill_moves' => rand(1, 5),
            'international_reputation' => rand(1, 5),
            'work_rate' => $workRates[array_rand($workRates)],
            'body_type' => $bodyTypes[array_rand($bodyTypes)],
            'release_clause_eur' => rand(1000000, 50000000),
            'overall_rating' => $overallRating,
            'potential_rating' => $potentialRating,
            'value_eur' => rand(500000, 20000000),
            'wage_eur' => rand(5000, 200000),
            'age' => $age,
        ];
    }

    private function createMatchRosters(): void
    {
        $this->command->info('Creating match rosters...');

        $matches = GameMatch::with(['homeTeam', 'awayTeam'])->get();
        $createdCount = 0;

        foreach ($matches as $match) {
            // Check if rosters already exist for this match
            $existingRosters = MatchRoster::where('match_id', $match->id)->count();
            if ($existingRosters > 0) {
                continue;
            }

            // Create roster for home team
            $homeRoster = MatchRoster::create([
                'match_id' => $match->id,
                'team_id' => $match->home_team_id,
                'submitted_at' => now()->subDays(rand(1, 7)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add players to home team roster
            $homeTeamPlayers = Player::where('club_id', $match->homeTeam->club_id)
                ->inRandomOrder()
                ->limit(25)
                ->get();

            foreach ($homeTeamPlayers as $index => $player) {
                MatchRosterPlayer::create([
                    'match_roster_id' => $homeRoster->id,
                    'player_id' => $player->id,
                    'jersey_number' => $index < 11 ? $index + 1 : $index + 1,
                    'is_starter' => $index < 11,
                    'position' => $player->position,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Create roster for away team
            $awayRoster = MatchRoster::create([
                'match_id' => $match->id,
                'team_id' => $match->away_team_id,
                'submitted_at' => now()->subDays(rand(1, 7)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add players to away team roster
            $awayTeamPlayers = Player::where('club_id', $match->awayTeam->club_id)
                ->inRandomOrder()
                ->limit(25)
                ->get();

            foreach ($awayTeamPlayers as $index => $player) {
                MatchRosterPlayer::create([
                    'match_roster_id' => $awayRoster->id,
                    'player_id' => $player->id,
                    'jersey_number' => $index < 11 ? $index + 1 : $index + 1,
                    'is_starter' => $index < 11,
                    'position' => $player->position,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $createdCount += 2; // 2 rosters per match
        }

        $this->command->info("Created {$createdCount} match rosters");
    }

    private function createMatchEvents(): void
    {
        $this->command->info('Creating match events...');

        $matches = GameMatch::take(50)->get(); // Create events for first 50 matches
        $createdCount = 0;

        foreach ($matches as $match) {
            // Check if events already exist for this match
            $existingEvents = MatchEvent::where('match_id', $match->id)->count();
            if ($existingEvents > 0) {
                continue;
            }

            $homeTeam = $match->homeTeam;
            $awayTeam = $match->awayTeam;
            
            // Get players from both teams
            $homePlayers = Player::where('club_id', $homeTeam->club_id)->inRandomOrder()->limit(5)->get();
            $awayPlayers = Player::where('club_id', $awayTeam->club_id)->inRandomOrder()->limit(5)->get();

            // Create 5-15 events per match
            $eventCount = rand(5, 15);
            
            for ($i = 0; $i < $eventCount; $i++) {
                $minute = rand(1, 90);
                $team = rand(0, 1) == 0 ? $homeTeam : $awayTeam;
                $player = rand(0, 1) == 0 ? $homePlayers->random() : $awayPlayers->random();
                
                $eventTypes = ['goal', 'yellow_card', 'red_card', 'substitution_in', 'substitution_out'];
                $eventType = $eventTypes[array_rand($eventTypes)];

                MatchEvent::create([
                    'match_id' => $match->id,
                    'player_id' => $player->id,
                    'team_id' => $team->id,
                    'assisted_by_player_id' => $eventType === 'goal' ? ($homePlayers->random()->id ?? null) : null,
                    'substituted_player_id' => in_array($eventType, ['substitution_in', 'substitution_out']) ? ($awayPlayers->random()->id ?? null) : null,
                    'recorded_by_user_id' => User::where('role', 'referee')->first()?->id ?? User::first()->id,
                    'event_type' => $eventType,
                    'minute' => $minute,
                    'extra_time_minute' => $minute > 90 ? rand(1, 5) : null,
                    'period' => $minute <= 45 ? 'first_half' : 'second_half',
                    'description' => $this->generateEventDescription($eventType, $player, $team),
                    'location' => 'Field',
                    'severity' => $eventType === 'red_card' ? 'high' : ($eventType === 'yellow_card' ? 'medium' : 'low'),
                    'recorded_at' => now()->subDays(rand(1, 30)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $createdCount++;
            }
        }

        $this->command->info("Created {$createdCount} match events");
    }

    private function generateEventDescription(string $eventType, $player, $team): string
    {
        return match ($eventType) {
            'goal' => "Goal scored by {$player->name} for {$team->name}",
            'yellow_card' => "Yellow card shown to {$player->name} ({$team->name})",
            'red_card' => "Red card shown to {$player->name} ({$team->name})",
            'substitution_in' => "{$player->name} substituted in for {$team->name}",
            'substitution_out' => "{$player->name} substituted out for {$team->name}",
            default => "Event involving {$player->name} ({$team->name})",
        };
    }

    private function updateMatchSheets(): void
    {
        $this->command->info('Updating match sheets with stage data...');

        $matchSheets = MatchSheet::all();
        $updatedCount = 0;

        foreach ($matchSheets as $matchSheet) {
            // Randomly assign stages to match sheets
            $stages = ['in_progress', 'before_game_signed', 'after_game_signed', 'fa_validated'];
            $stage = $stages[array_rand($stages)];
            
            $updateData = [
                'stage' => $stage,
                'stage_in_progress_at' => now()->subDays(rand(1, 30)),
            ];

            // Add stage-specific data
            switch ($stage) {
                case 'before_game_signed':
                case 'after_game_signed':
                case 'fa_validated':
                    $updateData['stage_before_game_signed_at'] = now()->subDays(rand(1, 20));
                    $updateData['home_team_lineup_signature'] = 'Team Official - ' . now()->subDays(rand(1, 15))->format('Y-m-d H:i:s');
                    $updateData['away_team_lineup_signature'] = 'Team Official - ' . now()->subDays(rand(1, 15))->format('Y-m-d H:i:s');
                    $updateData['home_team_lineup_signed_at'] = now()->subDays(rand(1, 15));
                    $updateData['away_team_lineup_signed_at'] = now()->subDays(rand(1, 15));
                    $updateData['lineups_locked'] = true;
                    $updateData['lineups_locked_at'] = now()->subDays(rand(1, 15));
                    $updateData['assigned_referee_id'] = User::where('role', 'referee')->first()?->id;
                    $updateData['referee_assigned_at'] = now()->subDays(rand(1, 20));
                    
                    if (in_array($stage, ['after_game_signed', 'fa_validated'])) {
                        $updateData['stage_after_game_signed_at'] = now()->subDays(rand(1, 10));
                        $updateData['home_team_post_match_signature'] = 'Team Official - ' . now()->subDays(rand(1, 10))->format('Y-m-d H:i:s');
                        $updateData['away_team_post_match_signature'] = 'Team Official - ' . now()->subDays(rand(1, 10))->format('Y-m-d H:i:s');
                        $updateData['home_team_post_match_signed_at'] = now()->subDays(rand(1, 10));
                        $updateData['away_team_post_match_signed_at'] = now()->subDays(rand(1, 10));
                        $updateData['home_team_post_match_comments'] = 'Match completed successfully';
                        $updateData['away_team_post_match_comments'] = 'Good game, well played';
                        
                        if ($stage === 'fa_validated') {
                            $updateData['stage_fa_validated_at'] = now()->subDays(rand(1, 5));
                            $updateData['fa_validated_by'] = User::where('role', 'association_admin')->first()?->id;
                            $updateData['fa_validation_notes'] = 'Match sheet validated by FA';
                        }
                    }
                    break;
            }

            $matchSheet->update($updateData);
            $updatedCount++;
        }

        $this->command->info("Updated {$updatedCount} match sheets with stage data");
    }
} 