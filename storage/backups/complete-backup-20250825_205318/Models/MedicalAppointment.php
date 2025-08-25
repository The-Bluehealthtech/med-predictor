<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_type',
        'status',
        'appointment_date',
        'duration_minutes',
        'reason',
        'notes',
        'video_meeting_url',
        'video_meeting_id',
        'video_meeting_password',
        'started_at',
        'ended_at',
        'medical_data'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'medical_data' => 'array'
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->appointment_type) {
            'onsite' => 'Sur site',
            'telemedicine' => 'Télé médecine',
            default => 'Inconnu'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmé',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            default => 'Inconnu'
        };
    }

    public function isVideoAppointment(): bool
    {
        return $this->appointment_type === 'telemedicine';
    }

    public function canStartVideo(): bool
    {
        return $this->isVideoAppointment() && 
               $this->status === 'confirmed' && 
               $this->appointment_date->subMinutes(5)->isPast() &&
               $this->appointment_date->addMinutes($this->duration_minutes + 15)->isFuture();
    }

    public function generateVideoMeeting(): void
    {
        if ($this->isVideoAppointment()) {
            $this->update([
                'video_meeting_id' => 'meeting_' . uniqid(),
                'video_meeting_password' => strtoupper(substr(md5(uniqid()), 0, 8)),
                'video_meeting_url' => config('app.url') . '/portal/video/' . $this->video_meeting_id
            ]);
        }
    }
}
