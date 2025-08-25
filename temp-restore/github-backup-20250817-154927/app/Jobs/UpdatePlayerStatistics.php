<?php

namespace App\Jobs;

use App\Models\PlayerSeasonStat;
use App\Models\GameMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdatePlayerStatistics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes
    public $tries = 3;

    protected $matchId;

    public function __construct($matchId)
    {
        $this->matchId = $matchId;
    }

    public function handle()
    {
        try {
            Log::info("Updating player statistics for match: {$this->matchId}");

            $match = GameMatch::with(['events.player', 'events.team', 'competition'])
                ->findOrFail($this->matchId);

            $this->processMatchEvents($match);

            Log::info("Successfully updated player statistics for match: {$this->matchId}");

        } catch (\Exception $e) {
            Log::error("Failed to update player statistics for match {$this->matchId}: " . $e->getMessage());
            throw $e;
        }
    }

    protected function processMatchEvents(GameMatch $match)
    {
        $events = $match->events()->with(['player', 'team'])->get();

        foreach ($events as $event) {
            if (!$event->player || !$event->team) continue;

            $this->updatePlayerStat($event, $match);
        }

        // Update clean sheets for goalkeepers
        $this->updateCleanSheets($match);
    }

    protected function updatePlayerStat($event, GameMatch $match)
    {
        $stat = PlayerSeasonStat::firstOrCreate([
            'player_id' => $event->player_id,
            'team_id' => $event->team_id,
            'competition_id' => $match->competition_id,
            'season' => $match->competition->season,
        ], [
            'matches_played' => 0,
            'goals' => 0,
            'assists' => 0,
            'yellow_cards' => 0,
            'red_cards' => 0,
            'minutes_played' => 0,
            'clean_sheets' => 0,
            'saves' => 0,
            'passes_completed' => 0,
            'passes_attempted' => 0,
            'tackles_won' => 0,
            'tackles_attempted' => 0,
        ]);

        switch ($event->event_type) {
            case 'goal':
                $stat->increment('goals');
                break;
            case 'assist':
                $stat->increment('assists');
                break;
            case 'yellow_card':
                $stat->increment('yellow_cards');
                break;
            case 'red_card':
                $stat->increment('red_cards');
                break;
            case 'substitution_in':
                $minutes = $this->calculateMinutesPlayed($event, $match);
                $stat->increment('minutes_played', $minutes);
                break;
            case 'save':
                $stat->increment('saves');
                break;
            case 'pass_completed':
                $stat->increment('passes_completed');
                $stat->increment('passes_attempted');
                break;
            case 'pass_attempted':
                $stat->increment('passes_attempted');
                break;
            case 'tackle_won':
                $stat->increment('tackles_won');
                $stat->increment('tackles_attempted');
                break;
            case 'tackle_attempted':
                $stat->increment('tackles_attempted');
                break;
        }

        // Update match participation
        if (!in_array($event->event_type, ['substitution_out'])) {
            $stat->increment('matches_played');
        }
    }

    protected function updateCleanSheets(GameMatch $match)
    {
        // Check if home team kept clean sheet
        if ($match->away_score === 0) {
            $this->awardCleanSheet($match->home_team_id, $match);
        }

        // Check if away team kept clean sheet
        if ($match->home_score === 0) {
            $this->awardCleanSheet($match->away_team_id, $match);
        }
    }

    protected function awardCleanSheet($teamId, GameMatch $match)
    {
        $goalkeepers = $match->events()
            ->where('team_id', $teamId)
            ->where('event_type', 'substitution_in')
            ->whereHas('player', function ($query) {
                $query->where('position', 'GK');
            })
            ->get();

        foreach ($goalkeepers as $event) {
            $stat = PlayerSeasonStat::where([
                'player_id' => $event->player_id,
                'team_id' => $teamId,
                'competition_id' => $match->competition_id,
                'season' => $match->competition->season,
            ])->first();

            if ($stat) {
                $stat->increment('clean_sheets');
            }
        }
    }

    protected function calculateMinutesPlayed($event, GameMatch $match)
    {
        $substitutionTime = $event->minute ?? 45; // Default to half-time
        return $substitutionTime;
    }
}
