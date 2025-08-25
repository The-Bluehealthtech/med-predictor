<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\Club;
use App\Models\Competition;
use App\Models\PlayerLicense;
use App\Models\MatchEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Get dashboard analytics
     */
    public function dashboard(): JsonResponse
    {
        $this->authorize('viewAny', MatchModel::class);

        $user = auth()->user();
        $associationId = $user->association_id;

        // Base query scope
        $baseScope = function ($query) use ($user, $associationId) {
            if ($user->role === 'system_admin') {
                return $query;
            }
            return $query->where('association_id', $associationId);
        };

        // Get current month stats
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $stats = [
            'matches' => [
                'total' => MatchModel::when($user->role !== 'system_admin', $baseScope)->count(),
                'this_month' => MatchModel::when($user->role !== 'system_admin', $baseScope)
                    ->where('match_date', '>=', $currentMonth)->count(),
                'last_month' => MatchModel::when($user->role !== 'system_admin', $baseScope)
                    ->whereBetween('match_date', [$lastMonth, $currentMonth])->count(),
            ],
            'players' => [
                'total' => Player::when($user->role !== 'system_admin', $baseScope)->count(),
                'active' => Player::when($user->role !== 'system_admin', $baseScope)
                    ->where('status', 'active')->count(),
                'new_this_month' => Player::when($user->role !== 'system_admin', $baseScope)
                    ->where('created_at', '>=', $currentMonth)->count(),
            ],
            'clubs' => [
                'total' => Club::when($user->role !== 'system_admin', $baseScope)->count(),
                'active' => Club::when($user->role !== 'system_admin', $baseScope)
                    ->where('status', 'active')->count(),
            ],
            'licenses' => [
                'total' => PlayerLicense::when($user->role !== 'system_admin', $baseScope)->count(),
                'pending' => PlayerLicense::when($user->role !== 'system_admin', $baseScope)
                    ->where('status', 'pending')->count(),
                'approved_this_month' => PlayerLicense::when($user->role !== 'system_admin', $baseScope)
                    ->where('status', 'approved')
                    ->where('updated_at', '>=', $currentMonth)->count(),
            ],
            'events' => [
                'total' => MatchEvent::when($user->role !== 'system_admin', $baseScope)->count(),
                'this_month' => MatchEvent::when($user->role !== 'system_admin', $baseScope)
                    ->where('created_at', '>=', $currentMonth)->count(),
            ]
        ];

        return response()->json([
            'message' => 'Analytics retrieved successfully',
            'data' => $stats
        ]);
    }

    /**
     * Get match analytics
     */
    public function matchAnalytics(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MatchModel::class);

        $user = auth()->user();
        $associationId = $user->association_id;

        $query = MatchModel::query();

        if ($user->role !== 'system_admin') {
            $query->where('association_id', $associationId);
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->where('match_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('match_date', '<=', $request->end_date);
        }

        // Competition filter
        if ($request->filled('competition_id')) {
            $query->where('competition_id', $request->competition_id);
        }

        $analytics = [
            'total_matches' => $query->count(),
            'completed_matches' => (clone $query)->where('status', 'completed')->count(),
            'cancelled_matches' => (clone $query)->where('status', 'cancelled')->count(),
            'average_goals_per_match' => $this->calculateAverageGoals($query),
            'most_common_events' => $this->getMostCommonEvents($user, $associationId),
            'matches_by_month' => $this->getMatchesByMonth($query),
            'top_scoring_teams' => $this->getTopScoringTeams($query),
        ];

        return response()->json([
            'message' => 'Match analytics retrieved successfully',
            'data' => $analytics
        ]);
    }

    /**
     * Get player analytics
     */
    public function playerAnalytics(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Player::class);

        $user = auth()->user();
        $associationId = $user->association_id;

        $query = Player::query();

        if ($user->role !== 'system_admin') {
            $query->where('association_id', $associationId);
        }

        // Club filter
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        $analytics = [
            'total_players' => $query->count(),
            'active_players' => (clone $query)->where('status', 'active')->count(),
            'players_by_position' => $this->getPlayersByPosition($query),
            'players_by_age_group' => $this->getPlayersByAgeGroup($query),
            'players_by_nationality' => $this->getPlayersByNationality($query),
            'new_players_trend' => $this->getNewPlayersTrend($query),
            'top_performing_players' => $this->getTopPerformingPlayers($user, $associationId),
        ];

        return response()->json([
            'message' => 'Player analytics retrieved successfully',
            'data' => $analytics
        ]);
    }

    /**
     * Get license analytics
     */
    public function licenseAnalytics(Request $request): JsonResponse
    {
        $this->authorize('viewAny', PlayerLicense::class);

        $user = auth()->user();
        $associationId = $user->association_id;

        $query = PlayerLicense::query();

        if ($user->role !== 'system_admin') {
            $query->where('association_id', $associationId);
        }

        $analytics = [
            'total_licenses' => $query->count(),
            'pending_licenses' => (clone $query)->where('status', 'pending')->count(),
            'approved_licenses' => (clone $query)->where('status', 'approved')->count(),
            'rejected_licenses' => (clone $query)->where('status', 'rejected')->count(),
            'licenses_by_type' => $this->getLicensesByType($query),
            'approval_rate' => $this->calculateApprovalRate($query),
            'processing_time' => $this->calculateProcessingTime($query),
            'licenses_by_month' => $this->getLicensesByMonth($query),
        ];

        return response()->json([
            'message' => 'License analytics retrieved successfully',
            'data' => $analytics
        ]);
    }

    /**
     * Get club analytics
     */
    public function clubAnalytics(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Club::class);

        $user = auth()->user();
        $associationId = $user->association_id;

        $query = Club::query();

        if ($user->role !== 'system_admin') {
            $query->where('association_id', $associationId);
        }

        $analytics = [
            'total_clubs' => $query->count(),
            'active_clubs' => (clone $query)->where('status', 'active')->count(),
            'clubs_by_city' => $this->getClubsByCity($query),
            'clubs_by_founded_year' => $this->getClubsByFoundedYear($query),
            'top_clubs_by_players' => $this->getTopClubsByPlayers($query),
            'clubs_performance' => $this->getClubsPerformance($user, $associationId),
        ];

        return response()->json([
            'message' => 'Club analytics retrieved successfully',
            'data' => $analytics
        ]);
    }

    /**
     * Export analytics data
     */
    public function export(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MatchModel::class);

        $type = $request->get('type', 'matches');
        $format = $request->get('format', 'json');

        $data = match ($type) {
            'matches' => $this->getMatchExportData(),
            'players' => $this->getPlayerExportData(),
            'licenses' => $this->getLicenseExportData(),
            'clubs' => $this->getClubExportData(),
            default => throw new \InvalidArgumentException('Invalid export type'),
        };

        if ($format === 'csv') {
            return $this->exportToCsv($data, $type);
        }

        return response()->json([
            'message' => 'Export data retrieved successfully',
            'data' => $data
        ]);
    }

    // Private helper methods

    private function calculateAverageGoals($query): float
    {
        $matches = (clone $query)->whereNotNull('home_score')->whereNotNull('away_score')->get();
        
        if ($matches->isEmpty()) {
            return 0;
        }

        $totalGoals = $matches->sum(function ($match) {
            return ($match->home_score ?? 0) + ($match->away_score ?? 0);
        });

        return round($totalGoals / $matches->count(), 2);
    }

    private function getMostCommonEvents($user, $associationId): array
    {
        $query = MatchEvent::query();

        if ($user->role !== 'system_admin') {
            $query->where('association_id', $associationId);
        }

        return $query->select('event_type', DB::raw('count(*) as count'))
            ->groupBy('event_type')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getMatchesByMonth($query): array
    {
        return (clone $query)
            ->select(DB::raw('DATE_FORMAT(match_date, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->toArray();
    }

    private function getTopScoringTeams($query): array
    {
        $matches = (clone $query)->with(['homeTeam', 'awayTeam'])->get();
        
        $teamScores = [];
        
        foreach ($matches as $match) {
            if ($match->homeTeam) {
                $teamScores[$match->homeTeam->name] = ($teamScores[$match->homeTeam->name] ?? 0) + ($match->home_score ?? 0);
            }
            if ($match->awayTeam) {
                $teamScores[$match->awayTeam->name] = ($teamScores[$match->awayTeam->name] ?? 0) + ($match->away_score ?? 0);
            }
        }

        arsort($teamScores);
        return array_slice($teamScores, 0, 10, true);
    }

    private function getPlayersByPosition($query): array
    {
        return (clone $query)
            ->select('position', DB::raw('count(*) as count'))
            ->whereNotNull('position')
            ->groupBy('position')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private function getPlayersByAgeGroup($query): array
    {
        return (clone $query)
            ->select(
                DB::raw('CASE 
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18 THEN "Under 18"
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 25 THEN "18-24"
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 35 THEN "25-34"
                    ELSE "35+"
                END as age_group'),
                DB::raw('count(*) as count')
            )
            ->groupBy('age_group')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private function getPlayersByNationality($query): array
    {
        return (clone $query)
            ->select('nationality', DB::raw('count(*) as count'))
            ->whereNotNull('nationality')
            ->groupBy('nationality')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getNewPlayersTrend($query): array
    {
        return (clone $query)
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->toArray();
    }

    private function getTopPerformingPlayers($user, $associationId): array
    {
        $query = MatchEvent::query();

        if ($user->role !== 'system_admin') {
            $query->where('association_id', $associationId);
        }

        return $query->with('player')
            ->select('player_id', DB::raw('count(*) as event_count'))
            ->groupBy('player_id')
            ->orderBy('event_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($event) {
                return [
                    'player' => $event->player,
                    'event_count' => $event->event_count
                ];
            })
            ->toArray();
    }

    private function getLicensesByType($query): array
    {
        return (clone $query)
            ->select('license_type', DB::raw('count(*) as count'))
            ->groupBy('license_type')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private function calculateApprovalRate($query): float
    {
        $total = (clone $query)->whereIn('status', ['approved', 'rejected'])->count();
        
        if ($total === 0) {
            return 0;
        }

        $approved = (clone $query)->where('status', 'approved')->count();
        return round(($approved / $total) * 100, 2);
    }

    private function calculateProcessingTime($query): float
    {
        $licenses = (clone $query)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereNotNull('updated_at')
            ->get();

        if ($licenses->isEmpty()) {
            return 0;
        }

        $totalDays = $licenses->sum(function ($license) {
            return Carbon::parse($license->created_at)->diffInDays($license->updated_at);
        });

        return round($totalDays / $licenses->count(), 1);
    }

    private function getLicensesByMonth($query): array
    {
        return (clone $query)
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->toArray();
    }

    private function getClubsByCity($query): array
    {
        return (clone $query)
            ->select('city', DB::raw('count(*) as count'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getClubsByFoundedYear($query): array
    {
        return (clone $query)
            ->select('founded_year', DB::raw('count(*) as count'))
            ->whereNotNull('founded_year')
            ->groupBy('founded_year')
            ->orderBy('founded_year', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getTopClubsByPlayers($query): array
    {
        return (clone $query)
            ->withCount('players')
            ->orderBy('players_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($club) {
                return [
                    'club' => $club->name,
                    'player_count' => $club->players_count
                ];
            })
            ->toArray();
    }

    private function getClubsPerformance($user, $associationId): array
    {
        $query = MatchModel::query();

        if ($user->role !== 'system_admin') {
            $query->where('association_id', $associationId);
        }

        $matches = $query->with(['homeTeam', 'awayTeam'])->get();
        
        $clubPerformance = [];
        
        foreach ($matches as $match) {
            if ($match->homeTeam) {
                $clubName = $match->homeTeam->name;
                $clubPerformance[$clubName] = $clubPerformance[$clubName] ?? ['wins' => 0, 'losses' => 0, 'draws' => 0];
                
                if ($match->home_score > $match->away_score) {
                    $clubPerformance[$clubName]['wins']++;
                } elseif ($match->home_score < $match->away_score) {
                    $clubPerformance[$clubName]['losses']++;
                } else {
                    $clubPerformance[$clubName]['draws']++;
                }
            }
            
            if ($match->awayTeam) {
                $clubName = $match->awayTeam->name;
                $clubPerformance[$clubName] = $clubPerformance[$clubName] ?? ['wins' => 0, 'losses' => 0, 'draws' => 0];
                
                if ($match->away_score > $match->home_score) {
                    $clubPerformance[$clubName]['wins']++;
                } elseif ($match->away_score < $match->home_score) {
                    $clubPerformance[$clubName]['losses']++;
                } else {
                    $clubPerformance[$clubName]['draws']++;
                }
            }
        }

        return array_slice($clubPerformance, 0, 10, true);
    }

    private function getMatchExportData(): array
    {
        $user = auth()->user();
        $query = MatchModel::query();

        if ($user->role !== 'system_admin') {
            $query->where('association_id', $user->association_id);
        }

        return $query->with(['homeTeam', 'awayTeam', 'competition'])
            ->get()
            ->map(function ($match) {
                return [
                    'id' => $match->id,
                    'date' => $match->match_date,
                    'home_team' => $match->homeTeam?->name,
                    'away_team' => $match->awayTeam?->name,
                    'home_score' => $match->home_score,
                    'away_score' => $match->away_score,
                    'status' => $match->status,
                    'competition' => $match->competition?->name,
                ];
            })
            ->toArray();
    }

    private function getPlayerExportData(): array
    {
        $user = auth()->user();
        $query = Player::query();

        if ($user->role !== 'system_admin') {
            $query->where('association_id', $user->association_id);
        }

        return $query->with(['club', 'team'])
            ->get()
            ->map(function ($player) {
                return [
                    'id' => $player->id,
                    'name' => $player->full_name,
                    'email' => $player->email,
                    'position' => $player->position,
                    'nationality' => $player->nationality,
                    'club' => $player->club?->name,
                    'team' => $player->team?->name,
                    'status' => $player->status,
                ];
            })
            ->toArray();
    }

    private function getLicenseExportData(): array
    {
        $user = auth()->user();
        $query = PlayerLicense::query();

        if ($user->role !== 'system_admin') {
            $query->where('association_id', $user->association_id);
        }

        return $query->with(['player', 'club'])
            ->get()
            ->map(function ($license) {
                return [
                    'id' => $license->id,
                    'player' => $license->player?->full_name,
                    'club' => $license->club?->name,
                    'type' => $license->license_type,
                    'status' => $license->status,
                    'created_at' => $license->created_at,
                    'updated_at' => $license->updated_at,
                ];
            })
            ->toArray();
    }

    private function getClubExportData(): array
    {
        $user = auth()->user();
        $query = Club::query();

        if ($user->role !== 'system_admin') {
            $query->where('association_id', $user->association_id);
        }

        return $query->get()
            ->map(function ($club) {
                return [
                    'id' => $club->id,
                    'name' => $club->name,
                    'city' => $club->city,
                    'founded_year' => $club->founded_year,
                    'status' => $club->status,
                ];
            })
            ->toArray();
    }

    private function exportToCsv(array $data, string $type): JsonResponse
    {
        if (empty($data)) {
            return response()->json(['message' => 'No data to export'], 404);
        }

        $filename = "{$type}_export_" . date('Y-m-d_H-i-s') . ".csv";
        
        // In a real implementation, you would generate and return the CSV file
        // For now, we'll return the data as JSON with a CSV flag
        return response()->json([
            'message' => 'CSV export data prepared',
            'data' => $data,
            'filename' => $filename,
            'format' => 'csv'
        ]);
    }
} 