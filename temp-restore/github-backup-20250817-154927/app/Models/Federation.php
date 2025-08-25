<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Federation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'fifa_code',
        'fifa_connect_id',
        'website',
        'email',
        'phone',
        'address',
        'logo_url',
        'status',
        'fifa_settings',
    ];

    protected $casts = [
        'fifa_settings' => 'array',
    ];

    /**
     * Relations
     */
    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    public function transfersAsOrigin(): HasMany
    {
        return $this->hasMany(Transfer::class, 'federation_origin_id');
    }

    public function transfersAsDestination(): HasMany
    {
        return $this->hasMany(Transfer::class, 'federation_destination_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Accessors & Mutators
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->country})";
    }

    public function getFifaCodeAttribute($value): string
    {
        return strtoupper($value);
    }

    /**
     * Méthodes FIFA
     */
    public function isFifaConnected(): bool
    {
        return !empty($this->fifa_connect_id);
    }

    public function getFifaSetting(string $key, $default = null)
    {
        return data_get($this->fifa_settings, $key, $default);
    }

    public function setFifaSetting(string $key, $value): void
    {
        $settings = $this->fifa_settings ?? [];
        $settings[$key] = $value;
        $this->fifa_settings = $settings;
    }
}
