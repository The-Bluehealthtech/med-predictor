<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;

class EnhancedTenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Skip if no authenticated user
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        
        // System admin can see everything
        if ($user->isSystemAdmin()) {
            return;
        }

        // If model has tenant_id column, apply tenant filtering
        if (Schema::hasColumn($model->getTable(), 'tenant_id')) {
            $this->applyTenantFilter($builder, $model, $user);
        } else {
            // Fallback to legacy association-based filtering
            $this->applyLegacyFilter($builder, $model, $user);
        }
    }

    /**
     * Apply tenant-based filtering
     */
    protected function applyTenantFilter(Builder $builder, Model $model, $user): void
    {
        $userTenantId = $user->tenant_id;
        
        if ($userTenantId) {
            // Get user's tenant
            $userTenant = Tenant::find($userTenantId);
            
            if ($userTenant) {
                // Get all accessible tenant IDs (including descendants and ancestors)
                $accessibleTenantIds = $this->getAccessibleTenantIds($userTenant);
                
                $builder->whereIn($model->getTable() . '.tenant_id', $accessibleTenantIds);
            } else {
                // If tenant not found, only show records without tenant_id (legacy data)
                $builder->whereNull($model->getTable() . '.tenant_id');
            }
        } else {
            // User without tenant can only see records without tenant_id (legacy data)
            $builder->whereNull($model->getTable() . '.tenant_id');
        }
    }

    /**
     * Apply legacy association-based filtering (backward compatibility)
     */
    protected function applyLegacyFilter(Builder $builder, Model $model, $user): void
    {
        // Association users can only see their association's data
        if ($user->association_id) {
            $builder->where($model->getTable() . '.association_id', $user->association_id);
        }
        
        // Club users can only see their club's data
        elseif ($user->club_id) {
            $builder->where($model->getTable() . '.club_id', $user->club_id);
        }
        
        // Federation users can only see their federation's data
        elseif ($user->federation_id) {
            $builder->where($model->getTable() . '.federation_id', $user->federation_id);
        }
    }

    /**
     * Get all accessible tenant IDs for a given tenant
     */
    protected function getAccessibleTenantIds(Tenant $tenant): array
    {
        $accessibleIds = [$tenant->id];
        
        // Add descendant tenant IDs
        $descendants = $tenant->getAllChildTenants();
        $accessibleIds = array_merge($accessibleIds, $descendants->pluck('id')->toArray());
        
        // Add ancestor tenant IDs
        $ancestors = $tenant->getAncestors();
        $accessibleIds = array_merge($accessibleIds, $ancestors->pluck('id')->toArray());
        
        return array_unique($accessibleIds);
    }

    /**
     * Check if the scope should be applied
     */
    public static function shouldApply(): bool
    {
        return Auth::check() && !Auth::user()->isSystemAdmin();
    }

    /**
     * Get the current user's accessible tenant IDs
     */
    public static function getCurrentUserAccessibleTenantIds(): array
    {
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();
        
        if ($user->isSystemAdmin()) {
            return Tenant::pluck('id')->toArray();
        }

        if ($user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);
            if ($tenant) {
                $scope = new self();
                return $scope->getAccessibleTenantIds($tenant);
            }
        }

        return [];
    }

    /**
     * Check if current user can access a specific tenant
     */
    public static function canAccessTenant(int $tenantId): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        if ($user->isSystemAdmin()) {
            return true;
        }

        if ($user->tenant_id) {
            $userTenant = Tenant::find($user->tenant_id);
            $targetTenant = Tenant::find($tenantId);
            
            if ($userTenant && $targetTenant) {
                return $userTenant->canAccessTenant($targetTenant);
            }
        }

        return false;
    }
}






