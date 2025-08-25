<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'type',
        'season',
        'start_date',
        'end_date',
        'registration_deadline',
        'min_teams',
        'max_teams',
        'format',
        'status',
        'description',
        'rules',
        'entry_fee',
        'prize_pool',
        'association_id',
        'fifa_connect_id',
        'require_federation_license',
        'fifa_sync_enabled'
    ];

    /**
     * Relation avec l'association
     */
    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }

    /**
     * Relation avec la saison
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Relation avec les performances des joueurs
     */
    public function playerPerformances(): HasMany
    {
        return $this->hasMany(PlayerPerformance::class);
    }

    /**
     * Relation avec FIFA Connect ID
     */
    public function fifaConnectId(): HasOne
    {
        return $this->hasOne(FifaConnectId::class, 'entity_id')->where('entity_type', 'competition');
    }

    /**
     * Relation many-to-many avec les clubs
     */
    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'competition_club')
            ->withTimestamps();
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