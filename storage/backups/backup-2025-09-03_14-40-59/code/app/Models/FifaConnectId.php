<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FifaConnectId extends Model
{
    use HasFactory;

    protected $fillable = [
        'fifa_id',
        'entity_type',
        'entity_id',
        'entity_status',
        'last_sync',
        'sync_status',
        'metadata'
    ];

    protected $casts = [
        'last_sync' => 'datetime',
        'metadata' => 'json'
    ];

    /**
     * Relation polymorphique avec l'entité
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relation avec la compétition
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class, 'entity_id')->where('entity_type', 'competition');
    }

    /**
     * Obtenir le statut de synchronisation
     */
    public function getSyncStatusTextAttribute(): string
    {
        return match($this->sync_status) {
            'pending' => 'En attente',
            'syncing' => 'Synchronisation en cours',
            'completed' => 'Terminé',
            'failed' => 'Échec',
            default => 'Inconnu'
        };
    }

    /**
     * Vérifier si la synchronisation est en cours
     */
    public function isSyncing(): bool
    {
        return $this->sync_status === 'syncing';
    }

    /**
     * Vérifier si la synchronisation a échoué
     */
    public function hasFailed(): bool
    {
        return $this->sync_status === 'failed';
    }
}
