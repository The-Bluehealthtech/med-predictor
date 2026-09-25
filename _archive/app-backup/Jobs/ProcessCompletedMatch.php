<?php

namespace App\Jobs;

use App\Models\GameMatch;
use App\Models\MatchEvent;
use App\Models\PlayerSeasonStat;
use App\Services\StandingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MatchCompletedNotification;

class ProcessCompletedMatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    protected $gameMatch;

    public function __construct(GameMatch $gameMatch)
    {
        $this->gameMatch = $gameMatch;
    }

    public function handle(StandingsService $standingsService)
    {
        try {
            Log::info("Processing completed match: {$this->gameMatch->id}");

            // Update player statistics
            $this->updatePlayerStatistics();

            // Recalculate standings
            $standingsService->recalculateStandings($this->gameMatch->competition);

            // Send notifications
            $this->sendNotifications();

            // Update match events summary
            $this->updateMatchSummary();

            Log::info("Successfully processed match: {$this->gameMatch->id}");

        } catch (\Exception $e) {
            Log::error("Failed to process match {$this->gameMatch->id}: " . $e->getMessage());
            throw $e;
        }
    }

    protected function updatePlayerStatistics()
    {
        $events = $this->gameMatch->events()
            ->with(['player', 'team'])
            ->get();

        foreach ($events as $event) {
            if (!$event->player) continue;

            $stat = PlayerSeasonStat::firstOrCreate([
                'player_id' => $event->player_id,
                'team_id' => $event->team_id,
                'competition_id' => $this->gameMatch->competition_id,
                'season' => $this->gameMatch->competition->season,
            ], [
                'matches_played' => 0,
                'goals' => 0,
                'assists' => 0,
                'yellow_cards' => 0,
                'red_cards' => 0,
                'minutes_played' => 0,
                'clean_sheets' => 0,
            ]);

            // Update based on event type
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
                    $stat->increment('minutes_played', 45); // Approximate
                    break;
            }

            $stat->increment('matches_played');
        }
    }

    protected function sendNotifications()
    {
        // Notify clubs
        $homeClub = $this->gameMatch->homeTeam->club;
        $awayClub = $this->gameMatch->awayTeam->club;

        if ($homeClub) {
            $homeClub->users()->each(function ($user) {
                Notification::send($user, new MatchCompletedNotification($this->gameMatch, 'home'));
            });
        }

        if ($awayClub) {
            $awayClub->users()->each(function ($user) {
                Notification::send($user, new MatchCompletedNotification($this->gameMatch, 'away'));
            });
        }

        // Notify association
        $association = $this->gameMatch->competition->association;
        if ($association) {
            $association->users()->each(function ($user) {
                Notification::send($user, new MatchCompletedNotification($this->gameMatch, 'association'));
            });
        }
    }

    protected function updateMatchSummary()
    {
        $events = $this->gameMatch->events()->get();
        
        $summary = [
            'total_goals' => $events->where('event_type', 'goal')->count(),
            'total_yellow_cards' => $events->where('event_type', 'yellow_card')->count(),
            'total_red_cards' => $events->where('event_type', 'red_card')->count(),
            'total_substitutions' => $events->where('event_type', 'substitution_in')->count(),
            'match_duration' => $this->calculateMatchDuration(),
        ];

        $this->gameMatch->update(['match_summary' => $summary]);
    }

    protected function calculateMatchDuration()
    {
        $startTime = $this->gameMatch->kickoff_time;
        $endTime = $this->gameMatch->completed_at;
        
        if (!$startTime || !$endTime) {
            return 90; // Default 90 minutes
        }

        return $startTime->diffInMinutes($endTime);
    }
}
