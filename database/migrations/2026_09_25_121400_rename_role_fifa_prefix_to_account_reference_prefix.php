<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasColumn('roles', 'fifa_connect_id_prefix')
            && !Schema::hasColumn('roles', 'account_reference_prefix')
        ) {
            Schema::table('roles', function (Blueprint $table) {
                $table->renameColumn(
                    'fifa_connect_id_prefix',
                    'account_reference_prefix'
                );
            });
        }

        if (Schema::hasColumn('roles', 'account_reference_prefix')) {
            DB::table('roles')
                ->select(['id', 'account_reference_prefix'])
                ->orderBy('id')
                ->get()
                ->each(function ($role) {
                    $value = $role->account_reference_prefix;

                    if (!is_string($value) || $value === '') {
                        return;
                    }

                    $normalized = preg_replace(
                        '/^FIFA_/',
                        '',
                        $value
                    );

                    if ($normalized !== $value) {
                        DB::table('roles')
                            ->where('id', $role->id)
                            ->update([
                                'account_reference_prefix' =>
                                    $normalized,
                            ]);
                    }
                });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasColumn('roles', 'account_reference_prefix')
            && !Schema::hasColumn('roles', 'fifa_connect_id_prefix')
        ) {
            Schema::table('roles', function (Blueprint $table) {
                $table->renameColumn(
                    'account_reference_prefix',
                    'fifa_connect_id_prefix'
                );
            });
        }
    }
};
