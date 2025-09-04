<?php

namespace Database\Seeders;

use App\Models\GameMatch;
use App\Models\Competition;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateMatchDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Generating match data with dates, scores, and rankings...');

        // Get the EPL competition
        $epl = Competition::where('name', 'Premier League')->first();
        if (!$epl) {
            $this->command->error('Premier League competition not found!');
            return;
        }

        // Get only teams registered to the EPL competition
        $teams = $epl->teams()->get();
        $this->command->info('Found ' . $teams->count() . ' teams registered to EPL');
        
        if ($teams->count() === 0) {
            $this->command->error('No teams registered to Premier League!');
            return;
        }

        // Generate realistic match dates (weekends and some midweek games)
        $this->generateMatchDates($epl);

        // Generate realistic scores and update match status
        $this->generateMatchScores($epl);

        // Generate rankings after each round
        $this->generateRankings($epl);

        $this->command->info('Match data generation completed successfully!');
    }

    private function generateMatchDates(Competition $competition): void
    {
        $this->command->info('Generating match dates...');

        $matches = $competition->matches()->orderBy('id')->get();
        $currentDate = Carbon::now()->next(Carbon::SATURDAY)->setTime(15, 0); // Start next Saturday at 3 PM

        foreach ($matches as $index => $match) {
            // Every 10 matches (1 round), add a week
            if ($index > 0 && $index % 10 === 0) {
                $currentDate->addWeek();
            }

            // Alternate between Saturday and Sunday for weekend games
            if ($index % 10 < 8) {
                // Weekend games (8 out of 10)
                if ($index % 2 === 0) {
                    $matchDate = $currentDate->copy()->next(Carbon::SATURDAY);
                } else {
                    $matchDate = $currentDate->copy()->next(Carbon::SUNDAY);
                }
            } else {
                // Midweek games (2 out of 10)
                $matchDate = $currentDate->copy()->next(Carbon::TUESDAY);
            }

            // Random kickoff times: 12:30, 15:00, 17:30, 20:00
            $kickoffTimes = ['12:30', '15:00', '17:30', '20:00'];
            $kickoffTime = $kickoffTimes[array_rand($kickoffTimes)];
            $matchDate->setTimeFromTimeString($kickoffTime);

            $match->update([
                'match_date' => $matchDate->format('Y-m-d'),
                'kickoff_time' => $matchDate,
                'match_status' => 'scheduled'
            ]);
        }

        $this->command->info('Match dates generated for ' . $matches->count() . ' matches');
    }

    private function generateMatchScores(Competition $competition): void
    {
        $this->command->info('Generating match scores...');

        $matches = $competition->matches()->orderBy('match_date')->get();
        $teamStats = [];

        // Initialize team statistics
        foreach ($competition->teams as $team) {
            $teamStats[$team->id] = [
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'points' => 0
            ];
        }

        foreach ($matches as $match) {
            // Generate realistic scores based on team performance
            $homeTeamId = $match->home_team_id;
            $awayTeamId = $match->away_team_id;

            // Calculate team strength based on current form
            $homeStrength = $this->calculateTeamStrength($teamStats[$homeTeamId]);
            $awayStrength = $this->calculateTeamStrength($teamStats[$awayTeamId]);

            // Add home advantage
            $homeStrength += 0.1;

            // Generate scores
            $homeScore = $this->generateScore($homeStrength);
            $awayScore = $this->generateScore($awayStrength);

            // Update match
            $match->update([
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'match_status' => 'completed',
                'home_shots' => rand(8, 20),
                'away_shots' => rand(6, 18),
                'home_shots_on_target' => rand(3, min($homeScore + 3, 12)),
                'away_shots_on_target' => rand(2, min($awayScore + 3, 10)),
                'home_possession' => rand(40, 65),
                'away_possession' => 100 - rand(40, 65),
                'home_corners' => rand(3, 12),
                'away_corners' => rand(2, 10),
                'home_fouls' => rand(8, 18),
                'away_fouls' => rand(8, 18),
                'home_yellow_cards' => rand(0, 4),
                'away_yellow_cards' => rand(0, 4),
                'home_red_cards' => rand(0, 1),
                'away_red_cards' => rand(0, 1),
            ]);

            // Update team statistics
            $this->updateTeamStats($teamStats, $homeTeamId, $awayTeamId, $homeScore, $awayScore);
        }

        $this->command->info('Match scores generated for ' . $matches->count() . ' matches');
    }

    private function calculateTeamStrength(array $stats): float
    {
        // Base strength from current form
        $form = ($stats['wins'] * 3 + $stats['draws']) / max(1, $stats['wins'] + $stats['draws'] + $stats['losses']);
        
        // Goal difference factor
        $goalDiff = $stats['goals_for'] - $stats['goals_against'];
        $goalFactor = 1 + ($goalDiff * 0.01);

        return max(0.5, min(2.0, $form * $goalFactor));
    }

    private function generateScore(float $strength): int
    {
        // Higher strength = higher chance of scoring
        $maxGoals = round($strength * 3);
        $chance = $strength * 0.6;

        if (rand(1, 100) <= $chance * 100) {
            return rand(0, $maxGoals);
        }
        return 0;
    }

    private function updateTeamStats(array &$teamStats, int $homeTeamId, int $awayTeamId, int $homeScore, int $awayScore): void
    {
        if ($homeScore > $awayScore) {
            // Home win
            $teamStats[$homeTeamId]['wins']++;
            $teamStats[$awayTeamId]['losses']++;
            $teamStats[$homeTeamId]['points'] += 3;
        } elseif ($awayScore > $homeScore) {
            // Away win
            $teamStats[$awayTeamId]['wins']++;
            $teamStats[$homeTeamId]['losses']++;
            $teamStats[$awayTeamId]['points'] += 3;
        } else {
            // Draw
            $teamStats[$homeTeamId]['draws']++;
            $teamStats[$awayTeamId]['draws']++;
            $teamStats[$homeTeamId]['points'] += 1;
            $teamStats[$awayTeamId]['points'] += 1;
        }

        $teamStats[$homeTeamId]['goals_for'] += $homeScore;
        $teamStats[$homeTeamId]['goals_against'] += $awayScore;
        $teamStats[$awayTeamId]['goals_for'] += $awayScore;
        $teamStats[$awayTeamId]['goals_against'] += $homeScore;
    }

    private function generateRankings(Competition $competition): void
    {
        $this->command->info('Generating rankings after each round...');

        $matches = $competition->matches()->orderBy('match_date')->get();
        $rounds = ceil($matches->count() / 10);

        for ($round = 1; $round <= $rounds; $round++) {
            $this->generateRoundRanking($competition, $round);
        }
    }

    private function generateRoundRanking(Competition $competition, int $round): void
    {
        $this->command->info("Generating ranking for Round {$round}...");

        // Get matches up to this round
        $matchesUpToRound = $competition->matches()
            ->orderBy('match_date')
            ->limit($round * 10)
            ->get();

        // Calculate standings
        $standings = $this->calculateStandings($competition, $matchesUpToRound);

        // Store ranking in database (you might want to create a rankings table)
        $this->storeRanking($competition, $round, $standings);

        $this->command->info("Round {$round} ranking generated with " . count($standings) . " teams");
    }

    private function calculateStandings(Competition $competition, $matches): array
    {
        $standings = [];

        // Initialize standings for all teams
        foreach ($competition->teams as $team) {
            $standings[$team->id] = [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'goal_difference' => 0,
                'points' => 0
            ];
        }

        // Calculate from completed matches
        foreach ($matches as $match) {
            if ($match->match_status === 'completed' && $match->home_score !== null && $match->away_score !== null) {
                $homeTeamId = $match->home_team_id;
                $awayTeamId = $match->away_team_id;

                $standings[$homeTeamId]['played']++;
                $standings[$awayTeamId]['played']++;

                $standings[$homeTeamId]['goals_for'] += $match->home_score;
                $standings[$homeTeamId]['goals_against'] += $match->away_score;
                $standings[$awayTeamId]['goals_for'] += $match->away_score;
                $standings[$awayTeamId]['goals_against'] += $match->home_score;

                if ($match->home_score > $match->away_score) {
                    $standings[$homeTeamId]['won']++;
                    $standings[$awayTeamId]['lost']++;
                    $standings[$homeTeamId]['points'] += 3;
                } elseif ($match->away_score > $match->home_score) {
                    $standings[$awayTeamId]['won']++;
                    $standings[$homeTeamId]['lost']++;
                    $standings[$awayTeamId]['points'] += 3;
                } else {
                    $standings[$homeTeamId]['drawn']++;
                    $standings[$awayTeamId]['drawn']++;
                    $standings[$homeTeamId]['points'] += 1;
                    $standings[$awayTeamId]['points'] += 1;
                }
            }
        }

        // Calculate goal difference
        foreach ($standings as &$standing) {
            $standing['goal_difference'] = $standing['goals_for'] - $standing['goals_against'];
        }

        // Sort by points, goal difference, goals scored
        uasort($standings, function ($a, $b) {
            if ($a['points'] !== $b['points']) {
                return $b['points'] - $a['points'];
            }
            if ($a['goal_difference'] !== $b['goal_difference']) {
                return $b['goal_difference'] - $a['goal_difference'];
            }
            return $b['goals_for'] - $a['goals_for'];
        });

        return $standings;
    }

    private function storeRanking(Competition $competition, int $round, array $standings): void
    {
        // For now, we'll store this in a simple way
        // You might want to create a proper rankings table
        $rankingData = [
            'competition_id' => $competition->id,
            'round' => $round,
            'standings' => json_encode($standings),
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Use updateOrInsert instead of insertOrUpdate
        DB::table('competition_rankings')->updateOrInsert(
            ['competition_id' => $competition->id, 'round' => $round],
            $rankingData
        );
    }
} 