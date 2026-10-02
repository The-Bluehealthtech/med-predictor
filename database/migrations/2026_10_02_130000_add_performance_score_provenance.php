<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['performances', 'external_player_performance_metrics', 'player_season_stats'] as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'score_origin')) {
                Schema::table($table, function (Blueprint $blueprint): void {
                    $blueprint->string('score_origin', 20)->default('unverified');
                });
            }
        }

        if (Schema::hasTable('performances') && ! Schema::hasColumn('performances', 'position_played')) {
            Schema::table('performances', function (Blueprint $table): void {
                $table->string('position_played', 12)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('performances') && Schema::hasColumn('performances', 'position_played')) {
            Schema::table('performances', fn (Blueprint $table) => $table->dropColumn('position_played'));
        }

        foreach (['performances', 'external_player_performance_metrics', 'player_season_stats'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'score_origin')) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn('score_origin'));
            }
        }
    }
};
