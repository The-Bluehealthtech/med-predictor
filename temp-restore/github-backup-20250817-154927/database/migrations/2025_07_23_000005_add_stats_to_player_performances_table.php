<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('player_performances', function (Blueprint $table) {
            $table->integer('appearances')->nullable()->after('season');
            $table->integer('goals')->nullable()->after('appearances');
            $table->integer('assists')->nullable()->after('goals');
            $table->integer('minutes_played')->nullable()->after('assists');
            $table->integer('yellow_cards')->nullable()->after('minutes_played');
            $table->integer('red_cards')->nullable()->after('yellow_cards');
            $table->integer('clean_sheets')->nullable()->after('red_cards');
            $table->integer('season_rating')->nullable()->after('clean_sheets');
        });
    }

    public function down(): void
    {
        Schema::table('player_performances', function (Blueprint $table) {
            $table->dropColumn(['appearances', 'goals', 'assists', 'minutes_played', 'yellow_cards', 'red_cards', 'clean_sheets', 'season_rating']);
        });
    }
}; 