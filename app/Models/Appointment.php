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
     * Rechercher par FIFA Connect ID
     */
    public static function findByFifaConnectId(string $fifaConnectId)
    {
        return static::where('fifa_connect_id', $fifaConnectId);
    }

    /**
     * Rechercher un athlète par FIFA Connect ID et créer un rendez-vous
     */
    public static function createForAthlete(string $fifaConnectId, array $data)
    {
        $athlete = Athlete::where('fifa_connect_id', $fifaConnectId)->first();
        
        if (!$athlete) {
            throw new \Exception("Athlète avec FIFA Connect ID {$fifaConnectId} non trouvé");
        }

        $data['athlete_id'] = $athlete->id;
        $data['fifa_connect_id'] = $fifaConnectId;

        return static::create($data);
    }

    /**
     * Scope pour les rendez-vous à venir
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now())
                    ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope pour les rendez-vous par statut
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour les rendez-vous par type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Obtenir les rendez-vous pour un athlète
     */
    public static function getForAthlete(string $fifaConnectId)
    {
        return static::where('fifa_connect_id', $fifaConnectId)
                    ->with(['athlete', 'doctor'])
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
