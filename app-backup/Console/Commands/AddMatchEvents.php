<?php

namespace App\Console\Commands;

use App\Models\GameMatch;
use App\Models\MatchEvent;
use App\Models\MatchSheet;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddMatchEvents extends Command
{
    protected $signature = 'matches:add-events {--match-id= : Specific match ID to add events to} {--all : Add events to all completed matches}';
    protected $description = 'Add realistic match events (substitutions, cards, penalties) to matches';

    public function handle()
    {
        $this->info('Adding realistic match events...');

        if ($matchId = $this->option('match-id')) {
            $matches = GameMatch::where('id', $matchId)->where('match_status', 'completed')->get();
        } elseif ($this->option('all')) {
            $matches = GameMatch::where('match_status', 'completed')->get();
        } else {
            $this->error('Please specify --match-id or --all option');
            return 1;
        }

        if ($matches->isEmpty()) {
            $this->error('No completed matches found');
            return 1;
        }

        $this->info("Found {$matches->count()} completed matches");

        foreach ($matches as $match) {
            $this->info("Processing match {$match->id}: {$match->homeTeam->name} vs {$match->awayTeam->name}");
            
            $matchSheet = MatchSheet::where('match_id', $match->id)->first();
            if (!$matchSheet) {
                $this->warn("No match sheet found for match {$match->id}, skipping...");
                continue;
            }

            // Clear existing events for this match
            MatchEvent::where('match_sheet_id', $matchSheet->id)->delete();

            $this->addMatchEvents($match, $matchSheet);
        }

        $this->info('Match events added successfully!');
        return 0;
    }

    private function addMatchEvents(GameMatch $match, MatchSheet $matchSheet)
    {
        $homeTeam = $match->homeTeam;
        $awayTeam = $match->awayTeam;

        // Get players for both teams through TeamPlayer relationship
        $homeTeamPlayers = \App\Models\TeamPlayer::where('team_id', $homeTeam->id)->with('player')->take(18)->get();
        $awayTeamPlayers = \App\Models\TeamPlayer::where('team_id', $awayTeam->id)->with('player')->take(18)->get();

        if ($homeTeamPlayers->isEmpty() || $awayTeamPlayers->isEmpty()) {
            $this->warn("Not enough players for match {$match->id}, skipping...");
            return;
        }

        // Extract player models
        $homePlayers = $homeTeamPlayers->pluck('player');
        $awayPlayers = $awayTeamPlayers->pluck('player');

        // Add substitutions (3-5 per team)
        $this->addSubstitutions($matchSheet, $homeTeam, $homePlayers, 'home');
        $this->addSubstitutions($matchSheet, $awayTeam, $awayPlayers, 'away');

        // Add yellow cards (2-6 per match)
        $this->addYellowCards($matchSheet, $homeTeam, $homePlayers, $awayTeam, $awayPlayers);

        // Add red cards (0-2 per match, 15% chance)
        if (rand(1, 100) <= 15) {
            $this->addRedCards($matchSheet, $homeTeam, $homePlayers, $awayTeam, $awayPlayers);
        }

        // Add penalties (0-2 per match, 25% chance)
        if (rand(1, 100) <= 25) {
            $this->addPenalties($matchSheet, $homeTeam, $homePlayers, $awayTeam, $awayPlayers);
        }

        // Add goals during substitution time (45+ and 90+ minutes)
        $this->addSubstitutionTimeGoals($matchSheet, $homeTeam, $homePlayers, $awayTeam, $awayPlayers);

        // Add other realistic events
        $this->addOtherEvents($matchSheet, $homeTeam, $homePlayers, $awayTeam, $awayPlayers);
    }

    private function addSubstitutions(MatchSheet $matchSheet, Team $team, $players, $teamType)
    {
        $numSubstitutions = rand(3, 5);
        $substitutionMinutes = $this->generateSubstitutionMinutes($numSubstitutions);

        for ($i = 0; $i < $numSubstitutions; $i++) {
            $minute = $substitutionMinutes[$i];
            $subOutPlayer = $players->random();
            $subInPlayer = $players->where('id', '!=', $subOutPlayer->id)->random();

            // Substitution out event
            MatchEvent::create([
                'match_sheet_id' => $matchSheet->id,
                'type' => 'substitution',
                'minute' => $minute,
                'player_id' => $subOutPlayer->id,
                'team_id' => $team->id,
                'description' => "Substitution: {$subOutPlayer->name} OFF"
            ]);

            // Substitution in event
            MatchEvent::create([
                'match_sheet_id' => $matchSheet->id,
                'type' => 'substitution',
                'minute' => $minute,
                'player_id' => $subInPlayer->id,
                'team_id' => $team->id,
                'description' => "Substitution: {$subInPlayer->name} ON"
            ]);

            $this->line("  Added substitution at {$minute}' for {$team->name}: {$subOutPlayer->name} → {$subInPlayer->name}");
        }
    }

    private function addYellowCards(MatchSheet $matchSheet, Team $homeTeam, $homePlayers, Team $awayTeam, $awayPlayers)
    {
        $numYellowCards = rand(2, 6);
        $allPlayers = $homePlayers->merge($awayPlayers);
        $teams = [$homeTeam, $awayTeam];

        for ($i = 0; $i < $numYellowCards; $i++) {
            $player = $allPlayers->random();
            
            // Determine which team the player belongs to
            $team = $homePlayers->contains('id', $player->id) ? $homeTeam : $awayTeam;
            $minute = rand(5, 90);

            MatchEvent::create([
                'match_sheet_id' => $matchSheet->id,
                'type' => 'yellow_card',
                'minute' => $minute,
                'player_id' => $player->id,
                'team_id' => $team->id,
                'description' => "Yellow card: {$player->name}"
            ]);

            $this->line("  Added yellow card at {$minute}' for {$player->name} ({$team->name})");
        }
    }

    private function addRedCards(MatchSheet $matchSheet, Team $homeTeam, $homePlayers, Team $awayTeam, $awayPlayers)
    {
        $numRedCards = rand(1, 2);
        $allPlayers = $homePlayers->merge($awayPlayers);
        $teams = [$homeTeam, $awayTeam];

        for ($i = 0; $i < $numRedCards; $i++) {
            $player = $allPlayers->random();
            
            // Determine which team the player belongs to
            $team = $homePlayers->contains('id', $player->id) ? $homeTeam : $awayTeam;
            $minute = rand(20, 85);

            MatchEvent::create([
                'match_sheet_id' => $matchSheet->id,
                'type' => 'red_card',
                'minute' => $minute,
                'player_id' => $player->id,
                'team_id' => $team->id,
                'description' => "Red card: {$player->name}"
            ]);

            $this->line("  Added red card at {$minute}' for {$player->name} ({$team->name})");
        }
    }

    private function addPenalties(MatchSheet $matchSheet, Team $homeTeam, $homePlayers, Team $awayTeam, $awayPlayers)
    {
        $numPenalties = rand(1, 2);
        $teams = [$homeTeam, $awayTeam];

        for ($i = 0; $i < $numPenalties; $i++) {
            $team = $teams[array_rand($teams)];
            $players = $team->id === $homeTeam->id ? $homePlayers : $awayPlayers;
            $player = $players->random();
            $minute = rand(15, 80);

            // 75% chance of penalty being scored
            $isScored = rand(1, 100) <= 75;

            MatchEvent::create([
                'match_sheet_id' => $matchSheet->id,
                'type' => $isScored ? 'goal' : 'injury', // Using injury for missed penalty
                'minute' => $minute,
                'player_id' => $player->id,
                'team_id' => $team->id,
                'description' => $isScored ? "Penalty goal: {$player->name}" : "Missed penalty: {$player->name}"
            ]);

            $this->line("  Added " . ($isScored ? "scored" : "missed") . " penalty at {$minute}' by {$player->name} ({$team->name})");
        }
    }

    private function addSubstitutionTimeGoals(MatchSheet $matchSheet, Team $homeTeam, $homePlayers, Team $awayTeam, $awayPlayers)
    {
        // Goals in first half stoppage time (45+ minutes)
        if (rand(1, 100) <= 30) {
            $team = rand(0, 1) ? $homeTeam : $awayTeam;
            $players = $team->id === $homeTeam->id ? $homePlayers : $awayPlayers;
            $player = $players->random();
            $minute = 45 + rand(1, 5);

            MatchEvent::create([
                'match_sheet_id' => $matchSheet->id,
                'type' => 'goal',
                'minute' => $minute,
                'player_id' => $player->id,
                'team_id' => $team->id,
                'description' => "Goal in stoppage time: {$player->name}"
            ]);

            $this->line("  Added stoppage time goal at {$minute}' by {$player->name} ({$team->name})");
        }

        // Goals in second half stoppage time (90+ minutes)
        if (rand(1, 100) <= 40) {
            $team = rand(0, 1) ? $homeTeam : $awayTeam;
            $players = $team->id === $homeTeam->id ? $homePlayers : $awayPlayers;
            $player = $players->random();
            $minute = 90 + rand(1, 7);

            MatchEvent::create([
                'match_sheet_id' => $matchSheet->id,
                'type' => 'goal',
                'minute' => $minute,
                'player_id' => $player->id,
                'team_id' => $team->id,
                'description' => "Goal in stoppage time: {$player->name}"
            ]);

            $this->line("  Added stoppage time goal at {$minute}' by {$player->name} ({$team->name})");
        }
    }

    private function addOtherEvents(MatchSheet $matchSheet, Team $homeTeam, $homePlayers, Team $awayTeam, $awayPlayers)
    {
        // Add some injuries (10% chance per match)
        if (rand(1, 100) <= 10) {
            $team = rand(0, 1) ? $homeTeam : $awayTeam;
            $players = $team->id === $homeTeam->id ? $homePlayers : $awayPlayers;
            $player = $players->random();
            $minute = rand(10, 75);

            MatchEvent::create([
                'match_sheet_id' => $matchSheet->id,
                'type' => 'injury',
                'minute' => $minute,
                'player_id' => $player->id,
                'team_id' => $team->id,
                'description' => "Injury: {$player->name}"
            ]);

            $this->line("  Added injury at {$minute}' for {$player->name} ({$team->name})");
        }
    }

    private function generateSubstitutionMinutes($numSubstitutions)
    {
        $minutes = [];
        
        // First substitution usually between 45-60 minutes
        if ($numSubstitutions >= 1) {
            $minutes[] = rand(45, 60);
        }
        
        // Second substitution between 60-75 minutes
        if ($numSubstitutions >= 2) {
            $minutes[] = rand(60, 75);
        }
        
        // Third substitution between 70-85 minutes
        if ($numSubstitutions >= 3) {
            $minutes[] = rand(70, 85);
        }
        
        // Fourth substitution between 80-90 minutes
        if ($numSubstitutions >= 4) {
            $minutes[] = rand(80, 90);
        }
        
        // Fifth substitution in stoppage time
        if ($numSubstitutions >= 5) {
            $minutes[] = 90 + rand(1, 5);
        }

        sort($minutes);
        return $minutes;
    }
} 