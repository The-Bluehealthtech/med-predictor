<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Athlete extends Model
{
    use HasFactory;

    protected $fillable = [
        'fifa_id',
        'name',
        'dob',
        'nationality',
        'team_id',
        'position',
        'jersey_number',
        'gender',
        'blood_type',
        'emergency_contact',
        'medical_history',
        'allergies',
        'medications',
        'active',
    ];

    protected $casts = [
        'dob' => 'date',
        'emergency_contact' => 'array',
        'medical_history' => 'array',
        'allergies' => 'array',
        'medications' => 'array',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the team that the athlete belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the PCMA assessments for this athlete.
     */
    public function pcmas(): HasMany
    {
        return $this->hasMany(PCMA::class);
    }

    /**
     * Get the injuries for this athlete.
     */
    public function injuries(): HasMany
    {
        return $this->hasMany(Injury::class);
    }

    /**
     * Get the SCAT assessments for this athlete.
     */
    public function scatAssessments(): HasMany
    {
        return $this->hasMany(SCATAssessment::class);
    }

    /**
     * Get the TUE requests for this athlete.
     */
    public function tueRequests(): HasMany
    {
        return $this->hasMany(TUERequest::class);
    }

    /**
     * Get the medical notes for this athlete.
     */
    public function medicalNotes(): HasMany
    {
        return $this->hasMany(MedicalNote::class);
    }

    /**
     * Get the risk alerts for this athlete.
     */
    public function riskAlerts(): HasMany
    {
        return $this->hasMany(RiskAlert::class);
    }

    public function healthScores(): HasMany
    {
        return $this->hasMany(HealthScore::class);
    }

    /**
     * Get the immunisations for this athlete.
     */
    public function immunisations(): HasMany
    {
        return $this->hasMany(Immunisation::class);
    }

    /**
     * Get active injuries for this athlete.
     */
    public function activeInjuries(): HasMany
    {
        return $this->hasMany(Injury::class)->where('status', 'open');
    }

    /**
     * Get pending PCMA assessments for this athlete.
     */
    public function pendingPCMAs(): HasMany
    {
        return $this->hasMany(PCMA::class)->where('status', 'pending');
    }

    /**
     * Get unresolved risk alerts for this athlete.
     */
    public function unresolvedRiskAlerts(): HasMany
    {
        return $this->hasMany(RiskAlert::class)->where('resolved', false);
    }

    /**
     * Calculate athlete's age.
     */
    public function getAgeAttribute(): int
    {
        return $this->dob->age;
    }

    /**
     * Get athlete's current medical status.
     */
    public function getMedicalStatus(): array
    {
        return [
            'has_active_injuries' => $this->activeInjuries()->exists(),
            'has_pending_pcma' => $this->pendingPCMAs()->exists(),
            'has_unresolved_alerts' => $this->unresolvedRiskAlerts()->exists(),
            'last_assessment_date' => $this->pcmas()->latest()->first()?->completed_at,
            'last_injury_date' => $this->injuries()->latest()->first()?->date,
        ];
    }

    /**
     * Get athlete's health score (0-100).
     */
    public function getHealthScore(): int
    {
        $score = 100;

        // Deduct points for active injuries
        $activeInjuries = $this->activeInjuries()->count();
        $score -= ($activeInjuries * 15);

        // Deduct points for unresolved alerts
        $unresolvedAlerts = $this->unresolvedRiskAlerts()->count();
        $score -= ($unresolvedAlerts * 10);

        // Deduct points for pending PCMA
        $pendingPCMA = $this->pendingPCMAs()->count();
        $score -= ($pendingPCMA * 5);

        return max(0, $score);
    }

    /**
     * Scope to filter by team.
     */
    public function scopeByTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Scope to filter by nationality.
     */
    public function scopeByNationality($query, $nationality)
    {
        return $query->where('nationality', $nationality);
    }

    /**
     * Scope to filter active athletes.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope to filter injured athletes.
     */
    public function scopeInjured($query)
    {
        return $query->whereHas('injuries', function ($query) {
            $query->where('status', 'open');
        });
    }

    /**
     * Get athlete's FIFA compliance status.
     */
    public function getFifaComplianceStatus(): array
    {
        $pcmaStatus = $this->pcmas()
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subYear())
            ->exists();

        $tueStatus = $this->tueRequests()
            ->where('status', 'approved')
            ->where('expiry_date', '>=', now())
            ->exists();

        return [
            'pcma_compliant' => $pcmaStatus,
            'tue_compliant' => $tueStatus,
            'fully_compliant' => $pcmaStatus && $tueStatus,
        ];
    }
} 