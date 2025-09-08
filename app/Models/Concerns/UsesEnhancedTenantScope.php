<?php

namespace App\Models\Concerns;

use App\Scopes\EnhancedTenantScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait UsesEnhancedTenantScope
{
    /**
     * Automatically apply tenant filtering and set tenant_id on create.
     */
    protected static function bootUsesEnhancedTenantScope(): void
    {
        // Apply global tenant scope
        static::addGlobalScope(new EnhancedTenantScope());

        // Set tenant_id automatically on create when available
        static::creating(function ($model) {
            if (Schema::hasColumn($model->getTable(), 'tenant_id') && empty($model->tenant_id)) {
                $user = Auth::user();
                if ($user && $user->tenant_id) {
                    $model->tenant_id = $user->tenant_id;
                }
            }
        });
    }
}




