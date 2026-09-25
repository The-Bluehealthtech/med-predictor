<?php

namespace App\Events;

use App\Models\MatchEvent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchEventRecorded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $event;

    public function __construct(MatchEvent $event)
    {
        $this->event = $event;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('match.' . $this->event->match_id),
            new Channel('match-events'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'match.event.recorded';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->event->id,
            'match_id' => $this->event->match_id,
            'event_type' => $this->event->event_type,
            'event_type_label' => $this->event->event_type_label,
            'minute' => $this->event->minute,
            'extra_time_minute' => $this->event->extra_time_minute,
            'display_time' => $this->event->display_time,
            'period' => $this->event->period,
            'description' => $this->event->description,
            'location' => $this->event->location,
            'severity' => $this->event->severity,
            'is_confirmed' => $this->event->is_confirmed,
            'is_contested' => $this->event->is_contested,
            'recorded_at' => $this->event->recorded_at,
            'player' => $this->event->player ? [
                'id' => $this->event->player->id,
                'name' => $this->event->player->name,
                'position' => $this->event->player->position,
            ] : null,
            'team' => $this->event->team ? [
                'id' => $this->event->team->id,
                'name' => $this->event->team->name,
            ] : null,
            'assisted_by_player' => $this->event->assistedByPlayer ? [
                'id' => $this->event->assistedByPlayer->id,
                'name' => $this->event->assistedByPlayer->name,
            ] : null,
            'substituted_player' => $this->event->substitutedPlayer ? [
                'id' => $this->event->substitutedPlayer->id,
                'name' => $this->event->substitutedPlayer->name,
            ] : null,
            'recorded_by_user' => $this->event->recordedByUser ? [
                'id' => $this->event->recordedByUser->id,
                'name' => $this->event->recordedByUser->name,
            ] : null,
        ];
    }
}
