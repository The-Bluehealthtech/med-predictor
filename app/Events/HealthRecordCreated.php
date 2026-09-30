<?php

namespace App\Events;

use App\Models\HealthRecord;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Événement interne : le dossier ne doit pas être diffusé sur un canal public.
class HealthRecordCreated
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

}
