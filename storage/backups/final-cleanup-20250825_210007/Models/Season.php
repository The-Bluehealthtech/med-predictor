<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    /**
     * Relation avec les performances des joueurs
     */
    public function playerPerformances(): HasMany
    {
        return $this->hasMany(PlayerPerformance::class);
    }

    /**
     * Obtenir la saison active
     */
    public static function getActiveSeason()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Obtenir la saison en cours
     */
    public static function getCurrentSeason()
    {
        $now = now();
        return static::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->first();
    }
} 