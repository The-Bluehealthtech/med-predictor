<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Sélection d'un joueur en équipe nationale, partagée entre son club et la
 * Direction technique nationale (DTN).
 */
class NationalSelection extends Model
{
    public const STATUS_CONVOKED = 'convoked';
    public const STATUS_DEPARTURE_SENT = 'departure_sent';
    public const STATUS_IN_SELECTION = 'in_selection';
    public const STATUS_RETURN_SENT = 'return_sent';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_LABELS = [
        self::STATUS_CONVOKED => 'Convoqué — état de départ à préparer',
        self::STATUS_DEPARTURE_SENT => 'État de départ envoyé',
        self::STATUS_IN_SELECTION => 'En sélection',
        self::STATUS_RETURN_SENT => 'État de retour reçu — à lire',
        self::STATUS_CLOSED => 'Clôturé',
        self::STATUS_CANCELLED => 'Annulé',
    ];

    public const EVENT_TYPES = [
        'friendly' => 'Match amical',
        'qualifier' => 'Match de qualification',
        'tournament' => 'Tournoi',
        'training_camp' => 'Stage',
    ];

    protected $fillable = [
        'player_id', 'club_id', 'association_id', 'team_label', 'event_type', 'event_name', 'opponent',
        'start_date', 'end_date', 'status', 'convocation_note', 'created_by', 'is_demo',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_demo' => 'boolean',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class)->withoutGlobalScopes();
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class)->withoutGlobalScopes();
    }

    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class)->withoutGlobalScopes();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(NationalSelectionReport::class);
    }

    public function departure(): HasOne
    {
        return $this->hasOne(NationalSelectionReport::class)->where('direction', NationalSelectionReport::DEPARTURE);
    }

    public function returnReport(): HasOne
    {
        return $this->hasOne(NationalSelectionReport::class)->where('direction', NationalSelectionReport::RETURN);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function eventTypeLabel(): string
    {
        return self::EVENT_TYPES[$this->event_type] ?? $this->event_type;
    }

    /** Statut affiché : « en sélection » se déduit des dates une fois l'état de départ envoyé. */
    public function effectiveStatus(): string
    {
        if ($this->status === self::STATUS_DEPARTURE_SENT && $this->start_date && now()->startOfDay()->gte($this->start_date)) {
            return self::STATUS_IN_SELECTION;
        }

        return $this->status;
    }
}
