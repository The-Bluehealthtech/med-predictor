<?php

namespace App\Observers;

use App\Models\Club;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class ClubObserver
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Club "created" event.
     */
    public function created(Club $club): void
    {
        $this->clearRelatedCaches($club);
    }

    /**
     * Handle the Club "updated" event.
     */
    public function updated(Club $club): void
    {
        $this->clearRelatedCaches($club);
    }

    /**
     * Handle the Club "deleted" event.
     */
    public function deleted(Club $club): void
    {
        $this->clearRelatedCaches($club);
    }

    /**
     * Clear all caches related to the club
     */
    private function clearRelatedCaches(Club $club): void
    {
        // Clear club-specific caches
        $this->cacheService->clearClubCache($club);
        
        // Clear association caches if club belongs to an association
        if ($club->association_id) {
            Cache::forget("association_stats_{$club->association_id}");
        }
        
        // Clear player caches for all players in this club
        $club->players()->chunk(100, function ($players) {
            foreach ($players as $player) {
                $this->cacheService->clearPlayerCache($player);
            }
        });
        
        // Clear team caches
        $club->teams()->chunk(50, function ($teams) {
            foreach ($teams as $team) {
                Cache::forget("team_stats_{$team->id}");
            }
        });
        
        // Clear competition caches
        $club->competitions()->chunk(25, function ($competitions) {
            foreach ($competitions as $competition) {
                $this->cacheService->clearCompetitionCache($competition);
            }
        });
        
        // Clear FIFA Connect caches
        if ($club->fifa_connect_id) {
            Cache::forget("fifa_club_{$club->fifa_connect_id}");
        }
    }
} 