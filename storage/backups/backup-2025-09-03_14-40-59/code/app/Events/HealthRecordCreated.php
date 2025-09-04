<?php

namespace App\Events;

use App\Models\HealthRecord;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HealthRecordCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $healthRecord;

    /**
     * Create a new event instance.
     */
    public function __construct(HealthRecord $healthRecord)
    {
        $this->healthRecord = $healthRecord;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('healthcare'),
            new Channel('player.' . $this->healthRecord->player_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'healthRecord' => $this->healthRecord->load(['player']),
            'message' => 'Health record created successfully',
            'timestamp' => now()->toISOString(),
        ];
    }
} 