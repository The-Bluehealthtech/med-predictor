<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'federation_id',
        'fifa_team_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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