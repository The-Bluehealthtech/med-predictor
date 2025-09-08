<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'display_name',
        'description',
        'type', // 'association', 'club', 'federation', 'organization'
        'status', // 'active', 'inactive', 'suspended', 'pending'
        'fifa_connect_id',
        'fifa_code',
        'country',
        'timezone',
        'language',
        'logo_url',
        'website',
        'email',
        'phone',
        'address',
        'settings',
        'metadata',
        'parent_tenant_id', // For hierarchical tenants
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'settings' => 'array',
        'metadata' => 'array',
    ];

    // Relationships
    public function parentTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'parent_tenant_id');
    }

    public function childTenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'parent_tenant_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function associations(): HasMany
    {
        return $this->hasMany(Association::class);
    }

    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    public function federations(): HasMany
    {
        return $this->hasMany(Federation::class);
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    public function scopeRootTenants($query)
    {
        return $query->whereNull('parent_tenant_id');
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_tenant_id);
    }

    public function hasParent(): bool
    {
        return !is_null($this->parent_tenant_id);
    }

    public function getFullNameAttribute(): string
    {
        return $this->display_name ?: $this->name;
    }

    public function getSlugAttribute($value): string
    {
        return $value ?: \Str::slug($this->name);
    }

    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
    }

    public function getMetadata(string $key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }

    public function setMetadata(string $key, $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
    }

    public function isFifaConnected(): bool
    {
        return !empty($this->fifa_connect_id);
    }

    public function getUsersCount(): int
    {
        return $this->users()->count();
    }

    public function getAssociationsCount(): int
    {
        return $this->associations()->count();
    }

    public function getClubsCount(): int
    {
        return $this->clubs()->count();
    }

    public function getCompetitionsCount(): int
    {
        return $this->competitions()->count();
    }

    public function getHierarchyLevel(): int
    {
        if ($this->isRoot()) {
            return 0;
        }

        return $this->parentTenant->getHierarchyLevel() + 1;
    }

    public function getAllChildTenants(): \Illuminate\Database\Eloquent\Collection
    {
        $childTenants = $this->childTenants;
        
        foreach ($this->childTenants as $childTenant) {
            $childTenants = $childTenants->merge($childTenant->getAllChildTenants());
        }
        
        return $childTenants;
    }

    public function getAncestors(): \Illuminate\Database\Eloquent\Collection
    {
        $ancestors = new \Illuminate\Database\Eloquent\Collection();
        $current = $this->parentTenant;
        
        while ($current) {
            $ancestors->push($current);
            $current = $current->parentTenant;
        }
        
        return $ancestors;
    }

    public function getAncestorsIds(): array
    {
        return $this->getAncestors()->pluck('id')->toArray();
    }

    public function isDescendantOf(Tenant $tenant): bool
    {
        return in_array($tenant->id, $this->getAncestorsIds());
    }

    public function isAncestorOf(Tenant $tenant): bool
    {
        return $tenant->isDescendantOf($this);
    }

    public function canAccessTenant(Tenant $targetTenant): bool
    {
        // Root tenants can access everything
        if ($this->isRoot()) {
            return true;
        }

        // Tenants can access themselves
        if ($this->id === $targetTenant->id) {
            return true;
        }

        // Tenants can access their descendants
        if ($this->isAncestorOf($targetTenant)) {
            return true;
        }

        // Tenants can access their ancestors
        if ($this->isDescendantOf($targetTenant)) {
            return true;
        }

        return false;
    }

    // Static methods
    public static function getDefaultTenant(): ?Tenant
    {
        return static::where('type', 'system')->first();
    }

    public static function getSystemTenant(): ?Tenant
    {
        return static::where('type', 'system')->first();
    }

    public static function getAssociationTenants(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('type', 'association')->active()->get();
    }

    public static function getClubTenants(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('type', 'club')->active()->get();
    }

    public static function getFederationTenants(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('type', 'federation')->active()->get();
    }

    // Boot method for automatic slug generation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->slug)) {
                $tenant->slug = \Str::slug($tenant->name);
            }
        });

        static::updating(function ($tenant) {
            if ($tenant->isDirty('name') && empty($tenant->slug)) {
                $tenant->slug = \Str::slug($tenant->name);
            }
        });
    }
}






