<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\UsesEnhancedTenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use UsesEnhancedTenantScope;
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'doctor_id',
        'created_by',
        'appointment_date',
        'duration_minutes',
        'appointment_type',
        'status',
        'reason',
        'notes',
        'reminder_settings',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'reminder_settings' => 'array'
    ];

    /**
     * Relation avec l'athlète
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Relation avec le médecin
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function visit(): HasOne
    {
        return $this->hasOne(Visit::class);
    }

    /**
     * Les rendez-vous sont reliés à l'Athlete canonique, lui-même relié au Player.
     */
    public static function getForAthlete(int $athleteId)
    {
        return static::where('athlete_id', $athleteId)
            ->with(['athlete.player', 'doctor'])
            ->orderBy('appointment_date', 'desc');
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'Programmé',
            'confirmed' => 'Confirmé',
            'Planifié' => 'Planifié',
            'Confirmé' => 'Confirmé',
            'Enregistré' => 'En salle d’attente',
            'En cours' => 'En consultation',
            'Terminé' => 'Terminé',
            'Annulé' => 'Annulé',
            'No-show' => 'Absent',
            default => 'Inconnu'
        };
    }

    /**
     * Obtenir le type formaté
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->appointment_type) {
            'consultation' => 'Consultation',
            'examination' => 'Examen',
            'follow_up' => 'Suivi',
            'emergency' => 'Urgence',
            default => 'Autre'
        };
    }
}
