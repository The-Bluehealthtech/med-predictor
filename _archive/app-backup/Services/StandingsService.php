<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\GameMatch;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StandingsService
{
    /**
     * Calculate standings for a competition
     */
    public function calculateStandings(Competition $competition): array
    {
        $teams = $competition->teams;
        $standings = [];

        foreach ($teams as $team) {
            $stats = $this->calculateTeamStats($team, $competition);
            $standings[] = [
                'team' => $team,
                'stats' => $stats,
            ];
        }

        // Sort by points (desc), goal difference (desc), goals scored (desc)
        usort($standings, function ($a, $b) {
            if ($a['stats']['points'] !== $b['stats']['points']) {
                return $b['stats']['points'] - $a['stats']['points'];
            }
            
            if ($a['stats']['goal_difference'] !== $b['stats']['goal_difference']) {
                return $b['stats']['goal_difference'] - $a['stats']['goal_difference'];
            }
            
            return $b['stats']['goals_scored'] - $a['stats']['goals_scored'];
        });

        // Add position
        foreach ($standings as $index => $standing) {
            $standing['position'] = $index + 1;
        }

        return $standings;
    }

    /**
     * Calculate team statistics
     */
    private function calculateTeamStats(Team $team, Competition $competition): array
    {
        $matches = $this->getCompletedMatches($team, $competition);
        
        $played = $matches->count();
        $won = 0;
        $drawn = 0;
        $lost = 0;
        $goalsScored = 0;
        $goalsConceded = 0;

        foreach ($matches as $match) {
            $isHome = $match->home_team_id === $team->id;
            $teamScore = $isHome ? $match->home_score : $match->away_score;
            $opponentScore = $isHome ? $match->away_score : $match->home_score;

            $goalsScored += $teamScore;
            $goalsConceded += $opponentScore;

            if ($teamScore > $opponentScore) {
                $won++;
            } elseif ($teamScore === $opponentScore) {
                $drawn++;
            } else {
                $lost++;
            }
        }

        $points = ($won * 3) + $drawn;
        $goalDifference = $goalsScored - $goalsConceded;

        return [
            'played' => $played,
            'won' => $won,
            'drawn' => $drawn,
            'lost' => $lost,
            'goals_scored' => $goalsScored,
            'goals_conceded' => $goalsConceded,
            'goal_difference' => $goalDifference,
            'points' => $points,
        ];
    }

    /**
     * Get completed matches for a team in a competition
     */
    private function getCompletedMatches(Team $team, Competition $competition): Collection
    {
        return GameMatch::where('competition_id', $competition->id)
            ->where('status', 'completed')
            ->where(function ($query) use ($team) {
                $query->where('home_team_id', $team->id)
                      ->orWhere('away_team_id', $team->id);
            })
            ->get();
    }

    /**
     * Recalculate standings after a match completion
     */
    public function recalculateStandings(Competition $competition): void
    {
        // This method can be called after a match is completed
        // For now, we'll just log that standings need recalculation
        // In a real implementation, you might want to cache standings
        // or trigger a background job for recalculation
        
        Log::info("Standings recalculation triggered for competition: {$competition->id}");
    }

    /**
     * Get team form (last 5 matches)
     */
    public function getTeamForm(Team $team, Competition $competition, int $limit = 5): array
    {
        $matches = GameMatch::where('competition_id', $competition->id)
            ->where('status', 'completed')
            ->where(function ($query) use ($team) {
                $query->where('home_team_id', $team->id)
                      ->orWhere('away_team_id', $team->id);
            })
            ->orderBy('completed_at', 'desc')
            ->limit($limit)
            ->get();

        $form = [];
        foreach ($matches as $match) {
            $isHome = $match->home_team_id === $team->id;
            $teamScore = $isHome ? $match->home_score : $match->away_score;
            $opponentScore = $isHome ? $match->away_score : $match->home_score;

            if ($teamScore > $opponentScore) {
                $form[] = 'W';
            } elseif ($teamScore === $opponentScore) {
                $form[] = 'D';
            } else {
                $form[] = 'L';
            }
        }

        return array_reverse($form); // Show oldest to newest
    }

    /**
     * Get head-to-head statistics between two teams
     */
    public function getHeadToHead(Team $team1, Team $team2, Competition $competition): array
    {
        $matches = GameMatch::where('competition_id', $competition->id)
            ->where('status', 'completed')
            ->where(function ($query) use ($team1, $team2) {
                $query->where(function ($q) use ($team1, $team2) {
                    $q->where('home_team_id', $team1->id)
                      ->where('away_team_id', $team2->id);
                })->orWhere(function ($q) use ($team1, $team2) {
                    $q->where('home_team_id', $team2->id)
                      ->where('away_team_id', $team1->id);
                });
            })
            ->get();

        $team1Wins = 0;
        $team2Wins = 0;
        $draws = 0;
        $team1Goals = 0;
        $team2Goals = 0;

        foreach ($matches as $match) {
            $team1IsHome = $match->home_team_id === $team1->id;
            $team1Score = $team1IsHome ? $match->home_score : $match->away_score;
            $team2Score = $team1IsHome ? $match->away_score : $match->home_score;

            $team1Goals += $team1Score;
            $team2Goals += $team2Score;

            if ($team1Score > $team2Score) {
                $team1Wins++;
            } elseif ($team1Score < $team2Score) {
                $team2Wins++;
            } else {
                $draws++;
            }
        }

        return [
            'total_matches' => $matches->count(),
            'team1_wins' => $team1Wins,
            'team2_wins' => $team2Wins,
            'draws' => $draws,
            'team1_goals' => $team1Goals,
            'team2_goals' => $team2Goals,
        ];
    }
} 