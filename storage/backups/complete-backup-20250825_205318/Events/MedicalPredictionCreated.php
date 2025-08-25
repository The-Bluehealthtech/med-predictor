<?php

namespace App\Events;

use App\Models\MedicalPrediction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedicalPredictionCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $prediction;

    /**
     * Create a new event instance.
     */
    public function __construct(MedicalPrediction $prediction)
    {
        $this->prediction = $prediction;
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
            new Channel('medical-predictions'),
            new Channel('player.' . $this->prediction->player_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'prediction' => $this->prediction->load(['player']),
            'message' => 'Medical prediction created successfully',
            'timestamp' => now()->toISOString(),
        ];
    }
} 