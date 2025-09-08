<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerLicenseHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'club_id',
        'association_id',
        'date_debut',
        'date_fin',
        'type_licence',
        'source_donnee',
        'license_number',
        'status',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'metadata' => 'array'
    ];

    /**
     * Relation avec le joueur
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Relation avec le club
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Relation avec l'association
     */
    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }

    /**
     * Scope pour la licence actuelle
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('date_fin')
              ->orWhere('date_fin', '>', now());
        });
    }

    /**
     * Scope pour les licences passées
     */
    public function scopePast($query)
    {
        return $query->where('date_fin', '<=', now());
    }

    /**
     * Vérifie si la licence est actuelle
     */
    public function isActive(): bool
    {
        return is_null($this->date_fin) || $this->date_fin->isFuture();
    }

    /**
     * Obtient le nom du type de licence formaté
     */
    public function getFormattedTypeAttribute(): string
    {
        return match($this->type_licence) {
            'Pro' => 'Professionnel',
            'Amateur' => 'Amateur',
            'Jeunes' => 'Jeunes',
            default => $this->type_licence
        };
    }
}
