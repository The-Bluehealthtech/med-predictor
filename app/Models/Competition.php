<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\UsesEnhancedTenantScope;
use Illuminate\Support\Str;

class Competition extends Model
{
    use HasFactory, SoftDeletes, UsesEnhancedTenantScope;

    protected $fillable = [
        'name',
        'short_name',
        'type',
        'category',
        'discipline',
        'format',
        'number_of_teams',
        'eligibility_rules',
        'start_date',
        'end_date',
        'main_stadium',
        'responsible_person',
        'contact_email',
        'contact_phone',
        'status',
        'federation_approval_date',
        'published_at',
        'association_id',
        'season_id',
        'fifa_connect_id',
        'require_federation_license',
        'fifa_sync_enabled',
        'entry_fee',
        'prize_pool',
        'description',
        'rules',
        'min_teams',
        'max_teams',
        'registration_deadline'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_deadline' => 'date',
        'federation_approval_date' => 'datetime',
        'published_at' => 'datetime',
        'require_federation_license' => 'boolean',
        'fifa_sync_enabled' => 'boolean',
        'entry_fee' => 'decimal:2',
        'prize_pool' => 'decimal:2',
    ];

    // Statuts de workflow FIFA Connect
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_VALIDATED = 'validated';
    const STATUS_PUBLISHED = 'published';
    const STATUS_CANCELLED = 'cancelled';

    // Types de compétitions FIFA
    const TYPE_CHAMPIONSHIP = 'championship';
    const TYPE_CUP = 'cup';
    const TYPE_TOURNAMENT = 'tournament';
    const TYPE_FRIENDLY = 'friendly';
    const TYPE_INTERNATIONAL = 'international';

    // Catégories d'âge FIFA (basées sur l'âge au 1er janvier de l'année de compétition)
    const CATEGORY_U13 = 'u13';           // Moins de 13 ans
    const CATEGORY_U15 = 'u15';           // Moins de 15 ans
    const CATEGORY_U17 = 'u17';           // Moins de 17 ans (compétitions FIFA officielles)
    const CATEGORY_U20 = 'u20';           // Moins de 20 ans (Coupe du Monde U-20)
    const CATEGORY_U23 = 'u23';           // Moins de 23 ans (Jeux Olympiques)
    const CATEGORY_SENIOR = 'senior';     // Pas de limite d'âge (Coupe du Monde, compétitions nationales)
    
    // Catégories par genre FIFA
    const CATEGORY_MEN = 'men';           // Masculin
    const CATEGORY_WOMEN = 'women';       // Féminin
    
    // Disciplines FIFA
    const CATEGORY_FUTSAL = 'futsal';     // Futsal (généralement Seniors)
    const CATEGORY_BEACH = 'beach';       // Beach Soccer (généralement Seniors)
    
    // Catégories locales (associations nationales)
    const CATEGORY_U12 = 'u12';           // Moins de 12 ans (local)
    const CATEGORY_U14 = 'u14';           // Moins de 14 ans (local)
    const CATEGORY_U16 = 'u16';           // Moins de 16 ans (local)
    const CATEGORY_U18 = 'u18';           // Moins de 18 ans (local)
    const CATEGORY_U19 = 'u19';           // Moins de 19 ans (local)
    const CATEGORY_U21 = 'u21';           // Espoirs (local)

    // Disciplines FIFA
    const DISCIPLINE_FOOTBALL = 'football';
    const DISCIPLINE_FUTSAL = 'futsal';
    const DISCIPLINE_BEACH_SOCCER = 'beach_soccer';

    // Formats de compétition
    const FORMAT_ROUND_ROBIN = 'round_robin';
    const FORMAT_KNOCKOUT = 'knockout';
    const FORMAT_GROUP_FINAL = 'group_final';
    const FORMAT_MIXED = 'mixed';

    /**
     * Boot method pour générer l'UUID FIFA Connect
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($competition) {
            if (empty($competition->fifa_connect_id)) {
                $competition->fifa_connect_id = 'COMP_' . Str::random(8) . '_' . time();
            }
        });
    }

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
            ->withTimestamps()
            ->withPivot(['registration_date', 'status', 'points', 'goals_for', 'goals_against', 'goal_difference', 'matches_played', 'wins', 'draws', 'losses']);
    }

    /**
     * Relation avec les phases de compétition
     */
    public function phases(): HasMany
    {
        return $this->hasMany(CompetitionPhase::class);
    }

    /**
     * Relation avec les matchs
     */
    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }

    /**
     * Relation avec les classements
     */
    public function standings(): HasMany
    {
        return $this->hasMany(Standing::class);
    }

    /**
     * Relation avec les arbitres assignés
     */
    public function referees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'competition_referees')
            ->withTimestamps()
            ->withPivot(['role', 'assigned_date', 'status']);
    }

    /**
     * Relation avec les notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(CompetitionNotification::class);
    }

    /**
     * Relation avec les logs d'audit
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(CompetitionAuditLog::class);
    }

    /**
     * Vérifier si la compétition peut être publiée
     */
    public function canBePublished(): bool
    {
        return $this->status === self::STATUS_VALIDATED 
            && $this->federation_approval_date !== null
            && $this->clubs()->count() >= $this->min_teams;
    }

    /**
     * Vérifier si la compétition est active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_PUBLISHED 
            && $this->start_date <= now() 
            && $this->end_date >= now();
    }

    /**
     * Vérifier si la compétition peut recevoir des inscriptions
     */
    public function canAcceptRegistrations(): bool
    {
        return $this->status === self::STATUS_PUBLISHED 
            && $this->registration_deadline > now()
            && $this->clubs()->count() < $this->max_teams;
    }

    /**
     * Obtenir le statut formaté pour l'affichage
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_DRAFT => 'Brouillon',
            self::STATUS_SUBMITTED => 'Soumis',
            self::STATUS_VALIDATED => 'Validé par la Fédération',
            self::STATUS_PUBLISHED => 'Publié',
            self::STATUS_CANCELLED => 'Annulé'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Obtenir le type formaté pour l'affichage
     */
    public function getTypeLabelAttribute(): string
    {
        $labels = [
            self::TYPE_CHAMPIONSHIP => 'Championnat',
            self::TYPE_CUP => 'Coupe',
            self::TYPE_TOURNAMENT => 'Tournoi',
            self::TYPE_FRIENDLY => 'Match amical',
            self::TYPE_INTERNATIONAL => 'International'
        ];

        return $labels[$this->type] ?? $this->type;
    }

    /**
     * Obtenir la catégorie formatée pour l'affichage (selon les standards FIFA)
     */
    public function getCategoryLabelAttribute(): string
    {
        $labels = [
            // Catégories d'âge FIFA officielles
            self::CATEGORY_U13 => 'U-13',
            self::CATEGORY_U15 => 'U-15',
            self::CATEGORY_U17 => 'U-17',
            self::CATEGORY_U20 => 'U-20',
            self::CATEGORY_U23 => 'U-23',
            self::CATEGORY_SENIOR => 'Senior',
            
            // Catégories par genre FIFA
            self::CATEGORY_MEN => 'Masculin',
            self::CATEGORY_WOMEN => 'Féminin',
            
            // Disciplines FIFA
            self::CATEGORY_FUTSAL => 'Futsal',
            self::CATEGORY_BEACH => 'Beach Soccer',
            
            // Catégories locales (associations nationales)
            self::CATEGORY_U12 => 'U-12',
            self::CATEGORY_U14 => 'U-14',
            self::CATEGORY_U16 => 'U-16',
            self::CATEGORY_U18 => 'U-18',
            self::CATEGORY_U19 => 'U-19',
            self::CATEGORY_U21 => 'U-21 (Espoirs)'
        ];

        return $labels[$this->category] ?? $this->category;
    }

    /**
     * Obtenir le format formaté pour l'affichage
     */
    public function getFormatLabelAttribute(): string
    {
        $labels = [
            self::FORMAT_ROUND_ROBIN => 'Aller-retour',
            self::FORMAT_KNOCKOUT => 'Élimination directe',
            self::FORMAT_GROUP_FINAL => 'Groupes + Finale',
            self::FORMAT_MIXED => 'Mixte'
        ];

        return $labels[$this->format] ?? $this->format;
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
        return static::where('type', self::TYPE_CHAMPIONSHIP)->get();
    }

    /**
     * Obtenir les compétitions par statut
     */
    public static function getByStatus(string $status)
    {
        return static::where('status', $status)->get();
    }

    /**
     * Obtenir les compétitions actives
     */
    public static function getActiveCompetitions()
    {
        return static::where('status', self::STATUS_PUBLISHED)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();
    }

    /**
     * Obtenir les compétitions par association
     */
    public static function getByAssociation(int $associationId)
    {
        return static::where('association_id', $associationId)->get();
    }

    /**
     * Obtenir les compétitions par saison
     */
    public static function getBySeason(int $seasonId)
    {
        return static::where('season_id', $seasonId)->get();
    }

    /**
     * Scope pour les compétitions de la fédération
     */
    public function scopeFederation($query)
    {
        return $query->where('level', 'federation');
    }

    /**
     * Scope pour les compétitions de l'association
     */
    public function scopeAssociation($query, $associationId)
    {
        return $query->where('association_id', $associationId);
    }

    /**
     * Scope pour les compétitions par statut
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour les compétitions actives
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }
} 