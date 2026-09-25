<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Confederation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'country',
        'fifa_ranking',
        'fifa_version',
        'fifa_sync_status',
        'fifa_sync_date',
        'fifa_last_error',
        'confederation_logo_url',
        'founded_year',
        'status',
    ];

    protected $casts = [
        'fifa_sync_date' => 'datetime',
        'founded_year' => 'integer',
    ];

    /**
     * Relations avec les associations affiliées
     */
    public function associations(): HasMany
    {
        return $this->hasMany(Association::class);
    }

    /**
     * Accesseur pour l'URL du logo de confédération
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->confederation_logo_url) {
            return asset('storage/' . $this->confederation_logo_url);
        }
        
        // Logo par défaut basé sur le short_name
        $defaultLogoPath = public_path("confederations/{$this->short_name}.png");
        if (file_exists($defaultLogoPath)) {
            return asset("confederations/{$this->short_name}.png");
        }
        
        return null;
    }

    /**
     * Accesseur pour le statut FIFA formaté
     */
    public function getFifaStatusBadgeAttribute(): string
    {
        $statusColors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'syncing' => 'bg-blue-100 text-blue-800',
            'synced' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
        ];
        
        $color = $statusColors[$this->fifa_sync_status] ?? 'bg-gray-100 text-gray-800';
        
        return "<span class=\"px-2 py-1 text-xs font-medium rounded-full {$color}\">{$this->fifa_sync_status}</span>";
    }

    /**
     * Accesseur pour corriger l'encodage UTF-8
     */
    public function getNameAttribute($value)
    {
        // Corriger l'encodage double UTF-8
        if (mb_check_encoding($value, 'UTF-8') && preg_match('/Ã[©|¨]/', $value)) {
            return utf8_decode($value);
        }
        return $value;
    }

    /**
     * Scope pour les confédérations actives
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour les confédérations synchronisées avec FIFA
     */
    public function scopeSynced($query)
    {
        return $query->where('fifa_sync_status', 'synced');
    }
}
