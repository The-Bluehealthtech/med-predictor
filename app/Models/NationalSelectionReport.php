<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * État partagé d'une sélection : départ (club → DTN) ou retour (DTN → club).
 *
 * La colonne « medical » est masquée de toute sérialisation : elle n'est lue
 * qu'explicitement, après contrôle du rôle (DtnAccess::canSeeMedical).
 */
class NationalSelectionReport extends Model
{
    public const DEPARTURE = 'departure';
    public const RETURN = 'return';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_ACKNOWLEDGED = 'acknowledged';

    public const FITNESS = [
        'fit' => 'Apte',
        'fit_with_restrictions' => 'Apte avec restrictions',
        'unfit' => 'Inapte',
    ];

    public const LEVELS = ['low' => 'Faible', 'medium' => 'Modéré', 'high' => 'Élevé'];

    protected $fillable = [
        'national_selection_id', 'direction', 'status', 'fitness_status', 'snapshot', 'content', 'medical',
        'author_id', 'medical_author_id', 'sent_at', 'acknowledged_by', 'acknowledged_at',
    ];

    protected $hidden = ['medical'];

    protected $casts = [
        'snapshot' => 'array',
        'content' => 'array',
        'medical' => 'array',
        'sent_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function selection(): BelongsTo
    {
        return $this->belongsTo(NationalSelection::class, 'national_selection_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function isSent(): bool
    {
        return in_array($this->status, [self::STATUS_SENT, self::STATUS_ACKNOWLEDGED], true);
    }

    public function fitnessLabel(): string
    {
        return self::FITNESS[$this->fitness_status] ?? 'Non renseigné';
    }
}
