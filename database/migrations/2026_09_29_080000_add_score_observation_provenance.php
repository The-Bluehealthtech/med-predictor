<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['performances', 'external_player_performance_metrics', 'player_season_stats'] as $table) {
            if (!Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $blueprint): void {
                // Les anciennes lignes demeurent non vérifiées : jamais présumées réelles.
                $blueprint->string('score_origin', 20)->default('unverified');
            });
        }
        if (Schema::hasTable('performances')) {
            Schema::table('performances', function (Blueprint $table): void {
                $table->string('position_played', 12)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('performances')) {
            Schema::table('performances', fn (Blueprint $table) => $table->dropColumn('position_played'));
        }
        foreach (['performances', 'external_player_performance_metrics', 'player_season_stats'] as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn('score_origin'));
            }
        }
    }
};
