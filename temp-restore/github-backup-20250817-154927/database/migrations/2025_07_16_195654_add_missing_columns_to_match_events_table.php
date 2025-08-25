<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('match_events', function (Blueprint $table) {
            // Add missing columns that are expected by the model
            if (!Schema::hasColumn('match_events', 'match_id')) {
                $table->foreignId('match_id')->nullable()->constrained('game_matches')->onDelete('cascade')->after('id');
            }
            if (!Schema::hasColumn('match_events', 'assisted_by_player_id')) {
                $table->foreignId('assisted_by_player_id')->nullable()->constrained('players')->onDelete('set null')->after('team_id');
            }
            if (!Schema::hasColumn('match_events', 'substituted_player_id')) {
                $table->foreignId('substituted_player_id')->nullable()->constrained('players')->onDelete('set null')->after('assisted_by_player_id');
            }
            if (!Schema::hasColumn('match_events', 'recorded_by_user_id')) {
                $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->onDelete('set null')->after('substituted_player_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_events', function (Blueprint $table) {
            $table->dropForeign(['match_id']);
            $table->dropForeign(['assisted_by_player_id']);
            $table->dropForeign(['substituted_player_id']);
            $table->dropForeign(['recorded_by_user_id']);
            
            $table->dropColumn([
                'match_id',
                'assisted_by_player_id',
                'substituted_player_id',
                'recorded_by_user_id'
            ]);
        });
    }
};
