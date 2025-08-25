<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\CountryCodeHelper;

class Association extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'country',
        'confederation',
        'fifa_ranking',
        'association_logo_url',
        'nation_flag_url',
        'fifa_version',
        'last_updated',
        'fifa_sync_status',
        'fifa_sync_date',
        'fifa_last_error'
    ];

    protected $casts = [
        'last_updated' => 'datetime',
        'fifa_ranking' => 'integer',
        'fifa_sync_date' => 'datetime'
    ];

    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->name . ' (' . $this->country . ')';
    }

    // Logo and Image Methods
    public function getLogoUrl(): string
    {
        if ($this->association_logo_url) {
            return $this->association_logo_url;
        }
        
        // Essayer de récupérer le logo national depuis l'API-Football
        $nationalLogoUrl = $this->getNationalLogoUrl();
        if ($nationalLogoUrl) {
            return $nationalLogoUrl;
        }
        
        // Return a default association logo
        return asset('images/defaults/association-logo.png');
    }

    /**
     * Récupère l'URL du logo national depuis les logos téléchargés
     */
    public function getNationalLogoUrl(): ?string
    {
        if (!$this->country) {
            return null;
        }
        
        // Utiliser le helper pour convertir le nom du pays en code ISO
        $countryCode = CountryCodeHelper::getCountryCode($this->country);
        
        if (!$countryCode) {
            return null;
        }
        
        // Vérifier si le logo national existe
        $logoPath = public_path("associations/{$countryCode}.png");
        
        if (file_exists($logoPath)) {
            return asset("associations/{$countryCode}.png");
        }
        
        return null;
    }

    /**
     * Vérifie si le logo national est disponible
     */
    public function hasNationalLogo(): bool
    {
        return $this->getNationalLogoUrl() !== null;
    }

    /**
     * Obtient le code ISO du pays de l'association
     */
    public function getCountryCode(): ?string
    {
        if (!$this->country) {
            return null;
        }
        
        return CountryCodeHelper::getCountryCode($this->country);
    }

    public function getFlagUrl(): string
    {
        if ($this->nation_flag_url) {
            return $this->nation_flag_url;
        }
        
        // Essayer de récupérer le drapeau depuis les logos nationaux
        $countryCode = $this->getCountryCode();
        if ($countryCode) {
            $flagPath = public_path("associations/{$countryCode}.png");
            if (file_exists($flagPath)) {
                return asset("associations/{$countryCode}.png");
            }
        }
        
        // Return a default flag based on country
        return asset('images/defaults/flag.png');
    }

    public function getLogoAlt(): string
    {
        return $this->name . ' Association Logo';
    }

    public function getFlagAlt(): string
    {
        return $this->country . ' Flag';
    }

    public function hasLogo(): bool
    {
        return !empty($this->association_logo_url) || $this->hasNationalLogo();
    }

    public function hasFlag(): bool
    {
        return !empty($this->nation_flag_url) || $this->hasNationalLogo();
    }

    public function getDisplayName(): string
    {
        return $this->short_name ?: $this->name;
    }

    public function getFullDisplayName(): string
    {
        $display = $this->name;
        if ($this->country) {
            $display .= ' (' . $this->country . ')';
        }
        return $display;
    }
}
