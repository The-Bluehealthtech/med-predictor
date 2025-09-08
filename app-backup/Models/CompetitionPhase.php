<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionPhase extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'name',
        'short_name',
        'phase_type',
        'start_date',
        'end_date',
        'status',
        'description',
        'order',
        'is_elimination',
        'number_of_teams',
        'number_of_groups',
        'teams_per_group',
        'advancing_teams_per_group'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_elimination' => 'boolean',
        'order' => 'integer',
        'number_of_teams' => 'integer',
        'number_of_groups' => 'integer',
        'teams_per_group' => 'integer',
        'advancing_teams_per_group' => 'integer'
    ];

    // Types de phases
    const PHASE_TYPE_GROUP = 'group';
    const PHASE_TYPE_KNOCKOUT = 'knockout';
    const PHASE_TYPE_FINAL = 'final';
    const PHASE_TYPE_PLAYOFF = 'playoff';

    // Statuts des phases
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Relation avec la compétition
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Relation avec les matchs de cette phase
     */
    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'phase_id');
    }

    /**
     * Relation avec les groupes (si phase de groupes)
     */
    public function groups(): HasMany
    {
        return $this->hasMany(CompetitionGroup::class);
    }

    /**
     * Relation avec les classements de cette phase
     */
    public function standings(): HasMany
    {
        return $this->hasMany(Standing::class, 'phase_id');
    }

    /**
     * Vérifier si la phase est active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Vérifier si la phase est terminée
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Vérifier si c'est une phase d'élimination
     */
    public function isEliminationPhase(): bool
    {
        return $this->is_elimination;
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_PENDING => 'En attente',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Terminée',
            self::STATUS_CANCELLED => 'Annulée'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Obtenir le type de phase formaté
     */
    public function getPhaseTypeLabelAttribute(): string
    {
        $labels = [
            self::PHASE_TYPE_GROUP => 'Phase de groupes',
            self::PHASE_TYPE_KNOCKOUT => 'Phase éliminatoire',
            self::PHASE_TYPE_FINAL => 'Finale',
            self::PHASE_TYPE_PLAYOFF => 'Playoff'
        ];

        return $labels[$this->phase_type] ?? $this->phase_type;
    }

    /**
     * Obtenir les phases par ordre
     */
    public static function getOrderedPhases(int $competitionId)
    {
        return static::where('competition_id', $competitionId)
            ->orderBy('order')
            ->get();
    }

    /**
     * Obtenir la phase active d'une compétition
     */
    public static function getActivePhase(int $competitionId)
    {
        return static::where('competition_id', $competitionId)
            ->where('status', self::STATUS_ACTIVE)
            ->first();
    }

    /**
     * Obtenir la prochaine phase d'une compétition
     */
    public static function getNextPhase(int $competitionId, int $currentOrder)
    {
        return static::where('competition_id', $competitionId)
            ->where('order', '>', $currentOrder)
            ->orderBy('order')
            ->first();
    }
}





