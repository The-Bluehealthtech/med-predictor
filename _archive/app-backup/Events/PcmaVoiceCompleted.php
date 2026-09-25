<?php

namespace App\Events;

use App\Models\VoiceSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PcmaVoiceCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $voiceSession;
    public $pcmaData;

    /**
     * Create a new event instance.
     */
    public function __construct(VoiceSession $voiceSession, array $pcmaData)
    {
        $this->voiceSession = $voiceSession;
        $this->pcmaData = $pcmaData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->voiceSession->user_id),
            new Channel('pcma-completed')
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->voiceSession->id,
            'player_name' => $this->voiceSession->player_name,
            'user_id' => $this->voiceSession->user_id,
            'completion_date' => now()->toISOString(),
            'pcma_summary' => $this->voiceSession->getPcmaSummary(),
            'message' => "PCMA vocal terminé pour {$this->voiceSession->player_name}"
        ];
    }

    /**
     * Get the event name.
     */
    public function broadcastAs(): string
    {
        return 'pcma.voice.completed';
    }
}
