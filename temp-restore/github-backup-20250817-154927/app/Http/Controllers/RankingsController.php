<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CompetitionRanking;

class RankingsController extends Controller
{
    public function index()
    {
        // Get the competition (temporarily bypassing cache for debugging)
        $competition = Competition::where('name', 'Premier League')->first();
        
        if (!$competition) {
            return view('rankings.index', [
                'rankingsArray' => [],
                'competition' => null,
                'latestRanking' => null,
                'error' => 'Premier League competition not found.'
            ]);
        }

        // Use Eloquent model to get the latest ranking
        $latestRanking = CompetitionRanking::where('competition_id', $competition->id)
            ->orderBy('round', 'desc')
            ->first();
        
        if ($latestRanking) {
            $latestRanking->created_at = \Carbon\Carbon::parse($latestRanking->created_at);
        }

        if (!$latestRanking) {
            return view('rankings.index', [
                'rankingsArray' => [],
                'competition' => $competition,
                'latestRanking' => null,
                'error' => 'No rankings available.'
            ]);
        }

        $standings = $latestRanking->standings;
        // Fallback: decode if still a string
        if (is_string($standings)) {
            $standings = json_decode($standings, true);
        }
        if (!is_array($standings) && !is_object($standings)) {
            return view('rankings.index', [
                'rankingsArray' => [],
                'competition' => $competition,
                'latestRanking' => $latestRanking,
                'error' => 'Invalid rankings data.'
            ]);
        }
        // Convert to array and sort by points
        $rankingsArray = [];
        foreach ($standings as $teamId => $stats) {
            if (isset($stats['club_name']) && isset($stats['points'])) {
                $rankingsArray[] = [
                    'team_id' => $teamId,
                    'team_name' => $stats['club_name'], // Use club_name instead of team_name
                    'played' => $stats['played'] ?? 0,
                    'won' => $stats['won'] ?? 0,
                    'drawn' => $stats['drawn'] ?? 0,
                    'lost' => $stats['lost'] ?? 0,
                    'goals_for' => $stats['goals_for'] ?? 0,
                    'goals_against' => $stats['goals_against'] ?? 0,
                    'goal_difference' => $stats['goal_difference'] ?? 0,
                    'points' => $stats['points'] ?? 0
                ];
            }
        }
        // Sort by points (desc), goal difference (desc), goals scored (desc)
        usort($rankingsArray, function ($a, $b) {
            if ($a['points'] !== $b['points']) {
                return $b['points'] - $a['points'];
            }
            if ($a['goal_difference'] !== $b['goal_difference']) {
                return $b['goal_difference'] - $a['goal_difference'];
            }
            return $b['goals_for'] - $a['goals_for'];
        });
        // Add position
        foreach ($rankingsArray as $index => $ranking) {
            $rankingsArray[$index]['position'] = $index + 1;
        }
        // Debug: Log the rankings array
        \Log::info('Rankings array count: ' . count($rankingsArray));
        if (count($rankingsArray) > 0) {
            \Log::info('First team: ' . $rankingsArray[0]['team_name'] . ' - ' . $rankingsArray[0]['points'] . ' pts');
        }
        return view('rankings.index', compact('rankingsArray', 'competition', 'latestRanking'));
    }

    public function test()
    {
        // Get the competition (temporarily bypassing cache for debugging)
        $competition = Competition::where('name', 'Premier League')->first();
        
        if (!$competition) {
            return 'Premier League competition not found.';
        }

        // Get the latest rankings (temporarily bypassing cache for debugging)
        $ranking = DB::table('competition_rankings')
            ->where('competition_id', $competition->id)
            ->orderBy('round', 'desc')
            ->first();
        
        if ($ranking) {
            // Convert created_at to Carbon object
            $ranking->created_at = \Carbon\Carbon::parse($ranking->created_at);
        }
        
        $latestRanking = $ranking;

        if (!$latestRanking) {
            return 'No rankings available.';
        }

        $standings = json_decode($latestRanking->standings, true);
        
        if (!$standings) {
            return 'Invalid rankings data.';
        }
        
        // Convert to array and sort by points
        $rankingsArray = [];
        foreach ($standings as $teamId => $stats) {
            if (isset($stats['club_name']) && isset($stats['points'])) {
                $rankingsArray[] = [
                    'team_id' => $teamId,
                    'team_name' => $stats['club_name'], // Use club_name instead of team_name
                    'played' => $stats['played'] ?? 0,
                    'won' => $stats['won'] ?? 0,
                    'drawn' => $stats['drawn'] ?? 0,
                    'lost' => $stats['lost'] ?? 0,
                    'goals_for' => $stats['goals_for'] ?? 0,
                    'goals_against' => $stats['goals_against'] ?? 0,
                    'goal_difference' => $stats['goal_difference'] ?? 0,
                    'points' => $stats['points'] ?? 0
                ];
            }
        }

        // Sort by points (desc), goal difference (desc), goals scored (desc)
        usort($rankingsArray, function ($a, $b) {
            if ($a['points'] !== $b['points']) {
                return $b['points'] - $a['points'];
            }
            
            if ($a['goal_difference'] !== $b['goal_difference']) {
                return $b['goal_difference'] - $a['goal_difference'];
            }
            
            return $b['goals_for'] - $a['goals_for'];
        });

        // Add position
        foreach ($rankingsArray as $index => $ranking) {
            $rankingsArray[$index]['position'] = $index + 1;
        }

        return view('rankings.test', compact('rankingsArray', 'competition', 'latestRanking'));
    }

    public function debug()
    {
        // Get the competition
        $competition = Competition::where('name', 'Premier League')->first();
        
        if (!$competition) {
            return 'Premier League competition not found.';
        }

        // Get the latest rankings
        $ranking = DB::table('competition_rankings')
            ->where('competition_id', $competition->id)
            ->orderBy('round', 'desc')
            ->first();
        
        $latestRanking = $ranking;

        if (!$latestRanking) {
            return 'No rankings available.';
        }

        $standings = json_decode($latestRanking->standings, true);
        
        if (!$standings) {
            return 'Invalid rankings data.';
        }
        
        // Convert to array and sort by points
        $rankingsArray = [];
        foreach ($standings as $teamId => $stats) {
            if (isset($stats['club_name']) && isset($stats['points'])) {
                $rankingsArray[] = [
                    'team_id' => $teamId,
                    'team_name' => $stats['club_name'], // Use club_name instead of team_name
                    'played' => $stats['played'] ?? 0,
                    'won' => $stats['won'] ?? 0,
                    'drawn' => $stats['drawn'] ?? 0,
                    'lost' => $stats['lost'] ?? 0,
                    'goals_for' => $stats['goals_for'] ?? 0,
                    'goals_against' => $stats['goals_against'] ?? 0,
                    'goal_difference' => $stats['goal_difference'] ?? 0,
                    'points' => $stats['points'] ?? 0
                ];
            }
        }

        // Sort by points (desc), goal difference (desc), goals scored (desc)
        usort($rankingsArray, function ($a, $b) {
            if ($a['points'] !== $b['points']) {
                return $b['points'] - $a['points'];
            }
            
            if ($a['goal_difference'] !== $b['goal_difference']) {
                return $b['goal_difference'] - $a['goal_difference'];
            }
            
            return $b['goals_for'] - $a['goals_for'];
        });

        // Add position
        foreach ($rankingsArray as $index => $ranking) {
            $rankingsArray[$index]['position'] = $index + 1;
        }

        $output = "DEBUG OUTPUT:\n";
        $output .= "Competition: " . $competition->name . "\n";
        $output .= "Latest Round: " . $latestRanking->round . "\n";
        $output .= "Rankings Array Count: " . count($rankingsArray) . "\n\n";
        
        if (count($rankingsArray) > 0) {
            $output .= "First 5 teams:\n";
            for ($i = 0; $i < min(5, count($rankingsArray)); $i++) {
                $team = $rankingsArray[$i];
                $output .= $team['position'] . ". " . $team['team_name'] . " - " . $team['points'] . " pts\n";
            }
        } else {
            $output .= "No teams found!\n";
        }

        return response($output)->header('Content-Type', 'text/plain');
    }
} 