<?php

namespace App\Events;

use App\Models\PCMA;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardioPCMASubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PCMA $pcma;

    /**
     * Create a new event instance.
     */
    public function __construct(PCMA $pcma)
    {
        $this->pcma = $pcma;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('medical.cardiovascular'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'pcma_id' => $this->pcma->id,
            'athlete_id' => $this->pcma->athlete_id,
            'assessment_date' => $this->pcma->completed_at?->toISOString(),
            'result_data' => $this->pcma->result_json,
            'fifa_compliance_data' => $this->pcma->fifa_compliance_data,
        ];
    }

    /**
     * Get the event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'cardio.pcma.submitted';
    }
} 