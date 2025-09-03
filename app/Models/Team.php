<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team extends Model
{
    use HasFactory;

    // Constantes FIFA Connect - Niveaux d'équipe
    const LEVEL_PROFESSIONAL = 'professional';
    const LEVEL_SEMI_PROFESSIONAL = 'semi_professional';
    const LEVEL_AMATEUR = 'amateur';
    const LEVEL_YOUTH = 'youth';
    const LEVEL_ACADEMY = 'academy';

    // Constantes FIFA Connect - Catégories d'âge d'équipe
    const AGE_CATEGORY_U12 = 'U12';
    const AGE_CATEGORY_U13 = 'U13';
    const AGE_CATEGORY_U14 = 'U14';
    const AGE_CATEGORY_U15 = 'U15';
    const AGE_CATEGORY_U16 = 'U16';
    const AGE_CATEGORY_U17 = 'U17';
    const AGE_CATEGORY_U18 = 'U18';
    const AGE_CATEGORY_U19 = 'U19';
    const AGE_CATEGORY_U20 = 'U20';
    const AGE_CATEGORY_U21 = 'U21';
    const AGE_CATEGORY_U23 = 'U23';
    const AGE_CATEGORY_SENIOR = 'SENIOR';

    // Constantes FIFA Connect - Disciplines
    const DISCIPLINE_FOOTBALL = 'football';
    const DISCIPLINE_FUTSAL = 'futsal';
    const DISCIPLINE_BEACH_SOCCER = 'beach_soccer';
    const DISCIPLINE_WOMEN_FOOTBALL = 'women_football';

    protected $fillable = [
        'name',
        'level',
        'federation_id',
        'club_id',
        'fifa_team_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the club that owns this team.
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the athletes for this team.
     */
    public function athletes(): HasMany
    {
        return $this->hasMany(Athlete::class);
    }

    /**
     * Get active athletes for this team.
     */
    public function activeAthletes(): HasMany
    {
        return $this->hasMany(Athlete::class)->where('active', true);
    }

    /**
     * Scope to filter by level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope to filter by federation.
     */
    public function scopeByFederation($query, $federationId)
    {
        return $query->where('federation_id', $federationId);
    }

    /**
     * Get the team's medical statistics.
     */
    public function getMedicalStats()
    {
        return [
            'total_athletes' => $this->athletes()->count(),
            'active_athletes' => $this->activeAthletes()->count(),
            'injured_athletes' => $this->athletes()->whereHas('injuries', function ($query) {
                $query->where('status', 'open');
            })->count(),
            'pending_pcma' => $this->athletes()->whereHas('pcmas', function ($query) {
                $query->where('status', 'pending');
            })->count(),
        ];
    }
} 