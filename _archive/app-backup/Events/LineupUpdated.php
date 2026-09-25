<?php

namespace App\Events;

use App\Models\Lineup;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LineupUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lineup;

    /**
     * Create a new event instance.
     */
    public function __construct(Lineup $lineup)
    {
        $this->lineup = $lineup;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('lineups'),
            new Channel('team.' . $this->lineup->team_id),
            new Channel('match.' . $this->lineup->game_match_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'lineup' => $this->lineup->load(['team', 'gameMatch', 'players']),
            'message' => 'Lineup updated successfully',
            'timestamp' => now()->toISOString(),
        ];
    }
} 