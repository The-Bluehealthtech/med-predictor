<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\GameMatch;
use App\Models\PlayerSeasonStat;
use App\Models\MatchEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Generate competition standings report
     */
    public function standingsReport(Competition $competition): JsonResponse
    {
        try {
            $standings = $this->calculateStandings($competition);
            
            $report = [
                'competition' => [
                    'id' => $competition->id,
                    'name' => $competition->name,
                    'season' => $competition->season,
                    'format' => $competition->format,
                ],
                'standings' => $standings,
                'generated_at' => now()->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate standings report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export standings to Excel
     */
    public function exportStandings(Competition $competition): JsonResponse
    {
        try {
            $standings = $this->calculateStandings($competition);
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Headers
            $sheet->setCellValue('A1', 'Position');
            $sheet->setCellValue('B1', 'Team');
            $sheet->setCellValue('C1', 'Played');
            $sheet->setCellValue('D1', 'Won');
            $sheet->setCellValue('E1', 'Drawn');
            $sheet->setCellValue('F1', 'Lost');
            $sheet->setCellValue('G1', 'Goals For');
            $sheet->setCellValue('H1', 'Goals Against');
            $sheet->setCellValue('I1', 'Goal Difference');
            $sheet->setCellValue('J1', 'Points');
            
            $row = 2;
            foreach ($standings as $standing) {
                $sheet->setCellValue('A' . $row, $standing['position']);
                $sheet->setCellValue('B' . $row, $standing['team_name']);
                $sheet->setCellValue('C' . $row, $standing['played']);
                $sheet->setCellValue('D' . $row, $standing['won']);
                $sheet->setCellValue('E' . $row, $standing['drawn']);
                $sheet->setCellValue('F' . $row, $standing['lost']);
                $sheet->setCellValue('G' . $row, $standing['goals_for']);
                $sheet->setCellValue('H' . $row, $standing['goals_against']);
                $sheet->setCellValue('I' . $row, $standing['goal_difference']);
                $sheet->setCellValue('J' . $row, $standing['points']);
                $row++;
            }
            
            $filename = "standings_{$competition->name}_{$competition->season}_" . now()->format('Y-m-d_H-i-s') . '.xlsx';
            $path = "reports/{$filename}";
            
            $writer = new Xlsx($spreadsheet);
            $writer->save(storage_path("app/public/{$path}"));
            
            return response()->json([
                'success' => true,
                'message' => 'Standings exported successfully',
                'data' => [
                    'filename' => $filename,
                    'download_url' => Storage::url($path)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export standings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate player statistics report
     */
    public function playerStatsReport(Competition $competition): JsonResponse
    {
        try {
            $stats = PlayerSeasonStat::where('competition_id', $competition->id)
                ->with(['player', 'team'])
                ->orderBy('goals', 'desc')
                ->orderBy('assists', 'desc')
                ->get();

            $report = [
                'competition' => [
                    'id' => $competition->id,
                    'name' => $competition->name,
                    'season' => $competition->season,
                ],
                'stats' => $stats->map(function ($stat) {
                    return [
                        'player_name' => $stat->player->name,
                        'team_name' => $stat->team->name,
                        'position' => $stat->player->position,
                        'matches_played' => $stat->matches_played,
                        'goals' => $stat->goals,
                        'assists' => $stat->assists,
                        'yellow_cards' => $stat->yellow_cards,
                        'red_cards' => $stat->red_cards,
                        'minutes_played' => $stat->minutes_played,
                        'clean_sheets' => $stat->clean_sheets,
                        'saves' => $stat->saves,
                        'pass_accuracy' => $stat->passes_attempted > 0 
                            ? round(($stat->passes_completed / $stat->passes_attempted) * 100, 1)
                            : 0,
                        'tackle_accuracy' => $stat->tackles_attempted > 0
                            ? round(($stat->tackles_won / $stat->tackles_attempted) * 100, 1)
                            : 0,
                    ];
                }),
                'generated_at' => now()->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate player stats report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate match report
     */
    public function matchReport(GameMatch $gameMatch): JsonResponse
    {
        try {
            $events = $gameMatch->events()
                ->with(['player', 'team', 'assistedByPlayer', 'substitutedPlayer', 'recordedByUser'])
                ->orderBy('minute')
                ->orderBy('extra_time_minute')
                ->get();

            $report = [
                'match' => [
                    'id' => $gameMatch->id,
                    'home_team' => $gameMatch->homeTeam->name,
                    'away_team' => $gameMatch->awayTeam->name,
                    'home_score' => $gameMatch->home_score,
                    'away_score' => $gameMatch->away_score,
                    'match_date' => $gameMatch->match_date,
                    'kickoff_time' => $gameMatch->kickoff_time,
                    'venue' => $gameMatch->venue,
                    'status' => $gameMatch->match_status,
                ],
                'events' => $events->map(function ($event) {
                    return [
                        'minute' => $event->display_time,
                        'event_type' => $event->event_type_label,
                        'player' => $event->player ? $event->player->name : null,
                        'team' => $event->team ? $event->team->name : null,
                        'description' => $event->getEventDescription(),
                        'recorded_by' => $event->recordedByUser ? $event->recordedByUser->name : null,
                        'is_confirmed' => $event->is_confirmed,
                        'is_contested' => $event->is_contested,
                    ];
                }),
                'summary' => [
                    'total_goals' => $events->where('event_type', 'goal')->count(),
                    'total_yellow_cards' => $events->where('event_type', 'yellow_card')->count(),
                    'total_red_cards' => $events->where('event_type', 'red_card')->count(),
                    'total_substitutions' => $events->where('event_type', 'substitution_in')->count(),
                ],
                'generated_at' => now()->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate match report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate competition summary report
     */
    public function competitionSummary(Competition $competition): JsonResponse
    {
        try {
            $matches = $competition->matches()->get();
            $stats = PlayerSeasonStat::where('competition_id', $competition->id)->get();

            $report = [
                'competition' => [
                    'id' => $competition->id,
                    'name' => $competition->name,
                    'season' => $competition->season,
                    'start_date' => $competition->start_date,
                    'end_date' => $competition->end_date,
                    'status' => $competition->status,
                ],
                'summary' => [
                    'total_matches' => $matches->count(),
                    'completed_matches' => $matches->where('match_status', 'completed')->count(),
                    'total_goals' => $matches->sum(function ($match) {
                        return ($match->home_score ?? 0) + ($match->away_score ?? 0);
                    }),
                    'average_goals_per_match' => $matches->where('match_status', 'completed')->count() > 0
                        ? round($matches->sum(function ($match) {
                            return ($match->home_score ?? 0) + ($match->away_score ?? 0);
                        }) / $matches->where('match_status', 'completed')->count(), 2)
                        : 0,
                    'total_players' => $stats->unique('player_id')->count(),
                    'top_scorer' => $stats->sortByDesc('goals')->first(),
                    'most_assists' => $stats->sortByDesc('assists')->first(),
                ],
                'generated_at' => now()->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate competition summary: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function calculateStandings(Competition $competition): array
    {
        $matches = $competition->matches()
            ->where('match_status', 'completed')
            ->with(['homeTeam', 'awayTeam'])
            ->get();

        $standings = [];

        foreach ($matches as $match) {
            $this->updateTeamStats($standings, $match->homeTeam, $match, 'home');
            $this->updateTeamStats($standings, $match->awayTeam, $match, 'away');
        }

        // Calculate goal differences and sort by points
        foreach ($standings as &$standing) {
            $standing['goal_difference'] = $standing['goals_for'] - $standing['goals_against'];
        }

        usort($standings, function ($a, $b) {
            if ($a['points'] !== $b['points']) {
                return $b['points'] - $a['points'];
            }
            if ($a['goal_difference'] !== $b['goal_difference']) {
                return $b['goal_difference'] - $a['goal_difference'];
            }
            return $b['goals_for'] - $a['goals_for'];
        });

        // Add position
        foreach ($standings as $index => &$standing) {
            $standing['position'] = $index + 1;
        }

        return $standings;
    }

    protected function updateTeamStats(&$standings, $team, $match, $side): void
    {
        if (!$team) return;

        $teamId = $team->id;
        if (!isset($standings[$teamId])) {
            $standings[$teamId] = [
                'team_id' => $teamId,
                'team_name' => $team->name,
                'played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'points' => 0,
            ];
        }

        $teamScore = $side === 'home' ? $match->home_score : $match->away_score;
        $opponentScore = $side === 'home' ? $match->away_score : $match->home_score;

        $standings[$teamId]['played']++;
        $standings[$teamId]['goals_for'] += $teamScore;
        $standings[$teamId]['goals_against'] += $opponentScore;

        if ($teamScore > $opponentScore) {
            $standings[$teamId]['won']++;
            $standings[$teamId]['points'] += 3;
        } elseif ($teamScore === $opponentScore) {
            $standings[$teamId]['drawn']++;
            $standings[$teamId]['points'] += 1;
        } else {
            $standings[$teamId]['lost']++;
        }
    }
}
