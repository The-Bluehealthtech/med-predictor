<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'fifa_connect_id',
        'doctor_id',
        'title',
        'description',
        'appointment_date',
        'status',
        'type',
        'location',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'metadata' => 'array'
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
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            default => 'Inconnu'
        };
    }

    /**
     * Obtenir le type formaté
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'consultation' => 'Consultation',
            'examination' => 'Examen',
            'follow_up' => 'Suivi',
            'emergency' => 'Urgence',
            default => 'Autre'
        };
    }
}
