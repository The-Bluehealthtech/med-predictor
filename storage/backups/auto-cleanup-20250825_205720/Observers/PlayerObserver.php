<?php

namespace App\Observers;

use App\Models\Player;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class PlayerObserver
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Player "created" event.
     */
    public function created(Player $player): void
    {
        $this->clearRelatedCaches($player);
    }

    /**
     * Handle the Player "updated" event.
     */
    public function updated(Player $player): void
    {
        $this->clearRelatedCaches($player);
    }

    /**
     * Handle the Player "deleted" event.
     */
    public function deleted(Player $player): void
    {
        $this->clearRelatedCaches($player);
    }

    /**
     * Clear all caches related to the player
     */
    private function clearRelatedCaches(Player $player): void
    {
        // Clear player-specific caches
        $this->cacheService->clearPlayerCache($player);
        
        // Clear club caches if player belongs to a club
        if ($player->club_id) {
            $club = $player->club;
            if ($club) {
                $this->cacheService->clearClubCache($club);
            }
        }
        
        // Clear association caches if player belongs to an association
        if ($player->association_id) {
            Cache::forget("association_stats_{$player->association_id}");
        }
        
        // Clear dashboard caches
        Cache::forget('player_dashboard_stats_' . auth()->id());
        
        // Clear FIFA Connect caches
        if ($player->fifa_connect_id) {
            Cache::forget("fifa_player_{$player->fifa_connect_id}");
        }
    }
} 