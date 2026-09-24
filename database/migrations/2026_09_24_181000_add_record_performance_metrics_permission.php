<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PERMISSION = 'record-performance-metrics';

    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            return;
        }

        $role = DB::table('roles')
            ->where('name', 'sports_scientist')
            ->first();

        if (!$role) {
            return;
        }

        $permissions = json_decode($role->permissions ?? '[]', true);

        if (!is_array($permissions)) {
            $permissions = [];
        }

        if (!in_array(self::PERMISSION, $permissions, true)) {
            $permissions[] = self::PERMISSION;

            DB::table('roles')
                ->where('id', $role->id)
                ->update([
                    'permissions' => json_encode(array_values($permissions)),
                    'updated_at' => now(),
                ]);
        }

        $this->clearCaches();
    }

    public function down(): void
    {
        if (!Schema::hasTable('roles')) {
            return;
        }

        $role = DB::table('roles')
            ->where('name', 'sports_scientist')
            ->first();

        if (!$role) {
            return;
        }

        $permissions = json_decode($role->permissions ?? '[]', true);

        if (!is_array($permissions)) {
            $permissions = [];
        }

        $permissions = array_values(array_filter(
            $permissions,
            fn ($permission) => $permission !== self::PERMISSION
        ));

        DB::table('roles')
            ->where('id', $role->id)
            ->update([
                'permissions' => json_encode($permissions),
                'updated_at' => now(),
            ]);

        $this->clearCaches();
    }

    private function clearCaches(): void
    {
        $this->forgetCacheSafely('rbac_permissions');
        $this->forgetCacheSafely(
            'rbac_role_permissions_sports_scientist'
        );

        if (Schema::hasTable('users')) {
            DB::table('users')
                ->where('role', 'sports_scientist')
                ->pluck('id')
                ->each(
                    fn ($id) => $this->forgetCacheSafely(
                        'rbac_user_permissions_' . $id
                    )
                );
        }
    }

    private function forgetCacheSafely(string $key): void
    {
        try {
            Cache::forget($key);
        } catch (\Throwable $e) {
            // Cache invalidation must never block a schema/data migration.
        }
    }
};
