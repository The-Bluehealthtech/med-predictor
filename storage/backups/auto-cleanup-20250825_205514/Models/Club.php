<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'country',
        'city',
        'stadium',
        'founded_year',
        'logo_url',
        'website',
        'email',
        'phone',
        'address',
        'fifa_connect_id',
        'association_id',
        'league',
        'division',
        'status',
        'budget_eur',
        'wage_budget_eur',
        'transfer_budget_eur',
        'reputation',
        'facilities_level',
        'youth_development',
        'scouting_network',
        'medical_team',
        'coaching_staff',
        'last_updated',
        'fifa_sync_status',
        'fifa_sync_date',
        'fifa_last_error',
    ];

    protected $casts = [
        'founded_year' => 'integer',
        'budget_eur' => 'decimal:2',
        'wage_budget_eur' => 'decimal:2',
        'transfer_budget_eur' => 'decimal:2',
        'reputation' => 'integer',
        'facilities_level' => 'integer',
        'youth_development' => 'integer',
        'scouting_network' => 'integer',
        'medical_team' => 'integer',
        'coaching_staff' => 'integer',
        'last_updated' => 'datetime',
        'fifa_sync_date' => 'datetime',
    ];

    // Relationships
    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function lineups(): HasMany
    {
        return $this->hasMany(Lineup::class);
    }

    public function competitions(): BelongsToMany
    {
        return $this->belongsToMany(Competition::class, 'competition_club');
    }

    public function playerLicenses(): HasMany
    {
        return $this->hasMany(PlayerLicense::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(\App\Models\User::class, 'entity_id')->where('entity_type', 'club');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByLeague($query, $league)
    {
        return $query->where('league', $league);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    // Methods
    public function getTotalSquadValue(): float
    {
        $cacheKey = "club_{$this->id}_total_squad_value";
        return \Cache::remember($cacheKey, 300, function () {
            $sum = $this->players()->sum('value_eur');
            return $sum ?? 0.0;
        });
    }

    public function getAverageSquadRating(): float
    {
        $cacheKey = "club_{$this->id}_avg_squad_rating";
        return \Cache::remember($cacheKey, 300, function () {
            $average = $this->players()->avg('overall_rating');
            return $average ?? 0.0;
        });
    }

    public function getSquadSize(): int
    {
        $cacheKey = "club_{$this->id}_squad_size";
        return \Cache::remember($cacheKey, 300, function () {
            return $this->players()->count();
        });
    }

    public function getAvailableBudget(): float
    {
        return $this->budget_eur - $this->getTotalSquadValue();
    }

    public function getWageSpending(): float
    {
        $cacheKey = "club_{$this->id}_wage_spending";
        return \Cache::remember($cacheKey, 300, function () {
            $sum = $this->players()->sum('wage_eur');
            return $sum ?? 0.0;
        });
    }

    public function getRemainingWageBudget(): float
    {
        return $this->wage_budget_eur - $this->getWageSpending();
    }

    public function canAffordPlayer(Player $player): bool
    {
        return $this->getAvailableBudget() >= $player->value_eur &&
               $this->getRemainingWageBudget() >= $player->wage_eur;
    }

    public function getSquadByPosition(string $position): \Illuminate\Database\Eloquent\Collection
    {
        return $this->players()->where('position', $position)->orderBy('overall_rating', 'desc')->get();
    }

    public function getBestPlayers(int $limit = 11): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "club_{$this->id}_best_players_{$limit}";
        return \Cache::remember($cacheKey, 300, function () use ($limit) {
            return $this->players()->orderBy('overall_rating', 'desc')->limit($limit)->get();
        });
    }

    public function clearCache(): void
    {
        $cacheKeys = [
            "club_{$this->id}_total_squad_value",
            "club_{$this->id}_avg_squad_rating",
            "club_{$this->id}_squad_size",
            "club_{$this->id}_wage_spending",
        ];
        
        foreach ($cacheKeys as $key) {
            \Cache::forget($key);
        }
        
        // Clear best players cache for different limits
        for ($i = 5; $i <= 25; $i += 5) {
            \Cache::forget("club_{$this->id}_best_players_{$i}");
        }
    }

    public function getYouthPlayers(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->players()->whereRaw('YEAR(CURDATE()) - YEAR(date_of_birth) <= 21')->get();
    }

    public function getExperiencedPlayers(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->players()->whereRaw('YEAR(CURDATE()) - YEAR(date_of_birth) >= 28')->get();
    }

    public function getInjuredPlayers(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->players()->whereHas('healthRecords', function ($query) {
            $query->where('status', 'injured');
        })->get();
    }

    public function getAvailablePlayers(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->players()->whereDoesntHave('healthRecords', function ($query) {
            $query->where('status', 'injured');
        })->get();
    }

    /**
     * Retourne l'utilisateur principal du club (club_admin ou club_manager)
     */
    public function userPrincipal()
    {
        return $this->users()->whereIn('role', ['club_admin', 'club_manager'])->orderBy('id')->first();
    }

    // Logo and Image Methods
    public function getLogoUrl(): string
    {
        if ($this->logo_url) {
            return $this->logo_url;
        }
        
        // Return a default club logo based on the club name or country
        return asset('images/defaults/club-logo.png');
    }

    public function getLogoAlt(): string
    {
        return $this->name . ' Logo';
    }

    public function hasLogo(): bool
    {
        return !empty($this->logo_url);
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
