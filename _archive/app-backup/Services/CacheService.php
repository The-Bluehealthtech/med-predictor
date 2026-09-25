<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Player;
use App\Models\Club;
use App\Models\Competition;
use App\Models\MatchModel;
use App\Models\HealthRecord;
use App\Models\MedicalPrediction;

class CacheService
{
    private const DEFAULT_TTL = 300; // 5 minutes
    private const LONG_TTL = 3600; // 1 hour
    private const SHORT_TTL = 60; // 1 minute

    /**
     * Get competition schedule with caching
     */
    public function getCompetitionSchedule(Competition $competition): array
    {
        $cacheKey = "competition_schedule_{$competition->id}";
        
        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($competition) {
            return MatchModel::where('competition_id', $competition->id)
                ->with(['homeTeam', 'awayTeam', 'competition'])
                ->orderBy('match_date')
                ->orderBy('kickoff_time')
                ->get()
                ->toArray();
        });
    }

    /**
     * Clear competition cache
     */
    public function clearCompetitionCache(Competition $competition): void
    {
        Cache::forget("competition_schedule_{$competition->id}");
        Cache::forget("competition_stats_{$competition->id}");
        Cache::forget("competition_standings_{$competition->id}");
    }

    /**
     * Get player statistics with caching
     */
    public function getPlayerStats(Player $player): array
    {
        $cacheKey = "player_stats_{$player->id}";
        
        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($player) {
            return [
                'health_records_count' => $player->healthRecords()->count(),
                'predictions_count' => $player->medicalPredictions()->count(),
                'active_predictions' => $player->medicalPredictions()->where('status', 'active')->count(),
                'high_risk_predictions' => $player->medicalPredictions()->where('risk_probability', '>', 0.7)->count(),
                'recent_health_records' => $player->healthRecords()->latest()->take(5)->count(),
            ];
        });
    }

    /**
     * Clear player cache
     */
    public function clearPlayerCache(Player $player): void
    {
        Cache::forget("player_stats_{$player->id}");
        Cache::forget("player_health_records_{$player->id}");
        Cache::forget("player_predictions_{$player->id}");
    }

    /**
     * Get club statistics with caching
     */
    public function getClubStats(Club $club): array
    {
        $cacheKey = "club_stats_{$club->id}";
        
        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($club) {
            return [
                'total_players' => $club->players()->count(),
                'total_squad_value' => $club->getTotalSquadValue(),
                'average_squad_rating' => $club->getAverageSquadRating(),
                'wage_spending' => $club->getWageSpending(),
                'available_budget' => $club->getAvailableBudget(),
                'players_with_health_records' => $club->players()->has('healthRecords')->count(),
                'players_with_predictions' => $club->players()->has('medicalPredictions')->count(),
            ];
        });
    }

    /**
     * Clear club cache
     */
    public function clearClubCache(Club $club): void
    {
        Cache::forget("club_stats_{$club->id}");
        $club->clearCache(); // Use the method we added to Club model
    }

    /**
     * Get dashboard statistics with caching
     */
    public function getDashboardStats(string $userType, int $userId): array
    {
        $cacheKey = "dashboard_stats_{$userType}_{$userId}";
        
        return Cache::remember($cacheKey, self::SHORT_TTL, function () use ($userType) {
            switch ($userType) {
                case 'system_admin':
                    return $this->getSystemAdminStats();
                case 'association':
                    return $this->getAssociationStats();
                case 'club':
                    return $this->getGeneralClubStats();
                default:
                    return [];
            }
        });
    }

    /**
     * Get system-wide statistics
     */
    private function getSystemAdminStats(): array
    {
        return [
            'total_players' => Player::count(),
            'total_clubs' => Club::count(),
            'total_competitions' => Competition::count(),
            'total_health_records' => HealthRecord::count(),
            'total_predictions' => MedicalPrediction::count(),
            'active_predictions' => MedicalPrediction::where('status', 'active')->count(),
        ];
    }

    /**
     * Get association statistics
     */
    private function getAssociationStats(): array
    {
        return [
            'total_players' => Player::count(),
            'total_clubs' => Club::count(),
            'total_health_records' => HealthRecord::count(),
            'total_predictions' => MedicalPrediction::count(),
        ];
    }

    /**
     * Get general club statistics for dashboard
     */
    private function getGeneralClubStats(): array
    {
        return [
            'total_players' => Player::count(),
            'total_health_records' => HealthRecord::count(),
            'total_predictions' => MedicalPrediction::count(),
        ];
    }

    /**
     * Get FIFA Connect data with caching
     */
    public function getFifaData(string $type, array $params = []): array
    {
        $cacheKey = "fifa_{$type}_" . md5(serialize($params));
        
        return Cache::remember($cacheKey, self::LONG_TTL, function () use ($type, $params) {
            // This would integrate with the actual FIFA API
            return [];
        });
    }

    /**
     * Clear all caches
     */
    public function clearAllCaches(): void
    {
        Cache::flush();
    }

    /**
     * Clear specific cache types
     */
    public function clearCacheByType(string $type): void
    {
        switch ($type) {
            case 'players':
                $this->clearPlayerCaches();
                break;
            case 'clubs':
                $this->clearClubCaches();
                break;
            case 'competitions':
                $this->clearCompetitionCaches();
                break;
            case 'health_records':
                $this->clearHealthRecordCaches();
                break;
            default:
                Cache::flush();
        }
    }

    /**
     * Clear all player-related caches
     */
    private function clearPlayerCaches(): void
    {
        $players = Player::all();
        foreach ($players as $player) {
            $this->clearPlayerCache($player);
        }
    }

    /**
     * Clear all club-related caches
     */
    private function clearClubCaches(): void
    {
        $clubs = Club::all();
        foreach ($clubs as $club) {
            $this->clearClubCache($club);
        }
    }

    /**
     * Clear all competition-related caches
     */
    private function clearCompetitionCaches(): void
    {
        $competitions = Competition::all();
        foreach ($competitions as $competition) {
            $this->clearCompetitionCache($competition);
        }
    }

    /**
     * Clear all health record-related caches
     */
    private function clearHealthRecordCaches(): void
    {
        // Clear healthcare-related caches
        Cache::flush(); // This is a simple approach - in production you'd be more specific
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        return [
            'driver' => config('cache.default'),
            'prefix' => config('cache.prefix'),
            'ttl_default' => self::DEFAULT_TTL,
            'ttl_long' => self::LONG_TTL,
            'ttl_short' => self::SHORT_TTL,
        ];
    }

    /**
     * Warm up frequently accessed caches
     */
    public function warmUpCaches(): void
    {
        // Warm up club statistics
        Club::chunk(100, function ($clubs) {
            foreach ($clubs as $club) {
                $this->getClubStats($club);
            }
        });

        // Warm up competition schedules
        Competition::chunk(50, function ($competitions) {
            foreach ($competitions as $competition) {
                $this->getCompetitionSchedule($competition);
            }
        });
    }
} 