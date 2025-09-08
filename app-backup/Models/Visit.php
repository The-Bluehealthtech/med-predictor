<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'doctor_id',
        'appointment_id',
        'visit_date',
        'visit_type',
        'status',
        'notes',
        'administrative_data',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'administrative_data' => 'array',
    ];

    // Relations
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    // Relations vers les modèles cliniques
    public function pcmaRecords(): HasMany
    {
        return $this->hasMany(PCMA::class);
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function injuries(): HasMany
    {
        return $this->hasMany(Injury::class);
    }

    public function scatAssessments(): HasMany
    {
        return $this->hasMany(SCATAssessment::class);
    }

    public function medicalNotes(): HasMany
    {
        return $this->hasMany(MedicalNote::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // Scopes pour faciliter les requêtes
    public function scopeToday($query)
    {
        return $query->whereDate('visit_date', today());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByAthlete($query, $athleteId)
    {
        return $query->where('athlete_id', $athleteId);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    // Méthodes utilitaires
    public function isCheckInReady(): bool
    {
        return $this->status === 'Planifié' && $this->appointment_id !== null;
    }

    public function canStart(): bool
    {
        return $this->status === 'Enregistré';
    }

    public function canComplete(): bool
    {
        return $this->status === 'En cours';
    }

    public function checkIn(): bool
    {
        if ($this->isCheckInReady()) {
            $this->update(['status' => 'Enregistré']);
            return true;
        }
        return false;
    }

    public function start(): bool
    {
        \Log::info('Visit::start() called', ['visit_id' => $this->id, 'current_status' => $this->status, 'canStart' => $this->canStart()]);
        
        if ($this->canStart()) {
            $this->update(['status' => 'En cours']);
            \Log::info('Visit status updated to En cours', ['visit_id' => $this->id, 'new_status' => $this->status]);
            return true;
        }
        
        \Log::warning('Cannot start visit', ['visit_id' => $this->id, 'current_status' => $this->status]);
        return false;
    }

    public function complete(): bool
    {
        if ($this->canComplete()) {
            $this->update(['status' => 'Terminé']);
            return true;
        }
        return false;
    }

    public function cancel(): bool
    {
        if (in_array($this->status, ['Planifié', 'Enregistré'])) {
            $this->update(['status' => 'Annulé']);
            return true;
        }
        return false;
    }
}
