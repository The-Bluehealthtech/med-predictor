<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('player_performances', function (Blueprint $table) {
            if (!Schema::hasColumn('player_performances', 'appearances')) {
                $table->integer('appearances')->nullable()->after('season');
            }
            if (!Schema::hasColumn('player_performances', 'goals')) {
                $table->integer('goals')->nullable()->after('appearances');
            }
            if (!Schema::hasColumn('player_performances', 'assists')) {
                $table->integer('assists')->nullable()->after('goals');
            }
            if (!Schema::hasColumn('player_performances', 'minutes_played')) {
                $table->integer('minutes_played')->nullable()->after('assists');
            }
            if (!Schema::hasColumn('player_performances', 'yellow_cards')) {
                $table->integer('yellow_cards')->nullable()->after('minutes_played');
            }
            if (!Schema::hasColumn('player_performances', 'red_cards')) {
                $table->integer('red_cards')->nullable()->after('yellow_cards');
            }
            if (!Schema::hasColumn('player_performances', 'clean_sheets')) {
                $table->integer('clean_sheets')->nullable()->after('red_cards');
            }
            if (!Schema::hasColumn('player_performances', 'season_rating')) {
                $table->integer('season_rating')->nullable()->after('clean_sheets');
            }
        });
    }

    public function down(): void
    {
        Schema::table('player_performances', function (Blueprint $table) {
            $table->dropColumn(['appearances', 'goals', 'assists', 'minutes_played', 'yellow_cards', 'red_cards', 'clean_sheets', 'season_rating']);
        });
    }
}; 