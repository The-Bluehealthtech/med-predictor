<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'level',
        'type'
    ];

    /**
     * Relation avec les performances des joueurs
     */
    public function playerPerformances(): HasMany
    {
        return $this->hasMany(PlayerPerformance::class);
    }

    /**
     * Obtenir les compétitions nationales
     */
    public static function getNationalCompetitions()
    {
        return static::where('level', 'national')->get();
    }

    /**
     * Obtenir les compétitions de type ligue
     */
    public static function getLeagueCompetitions()
    {
        return static::where('type', 'league')->get();
    }
} 