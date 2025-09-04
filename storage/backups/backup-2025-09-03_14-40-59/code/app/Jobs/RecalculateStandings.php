<?php

namespace App\Jobs;

use App\Models\Competition;
use App\Services\StandingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecalculateStandings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 180; // 3 minutes
    public $tries = 3;

    protected $competitionId;

    public function __construct($competitionId)
    {
        $this->competitionId = $competitionId;
    }

    public function handle(StandingsService $standingsService)
    {
        try {
            Log::info("Recalculating standings for competition: {$this->competitionId}");

            $competition = Competition::findOrFail($this->competitionId);
            
            $standings = $standingsService->calculateStandings($competition);
            
            // Cache the standings for performance
            cache()->put("standings_{$competition->id}", $standings, now()->addHours(1));

            Log::info("Successfully recalculated standings for competition: {$this->competitionId}");

        } catch (\Exception $e) {
            Log::error("Failed to recalculate standings for competition {$this->competitionId}: " . $e->getMessage());
            throw $e;
        }
    }
}
