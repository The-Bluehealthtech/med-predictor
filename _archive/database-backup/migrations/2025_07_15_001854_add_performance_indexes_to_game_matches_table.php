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
        Schema::table('game_matches', function (Blueprint $table) {
            // Index for scheduling queries
            $table->index(['competition_id', 'kickoff_time'], 'idx_competition_kickoff');
            $table->index(['competition_id', 'match_status'], 'idx_competition_status');
            $table->index(['kickoff_time', 'match_status'], 'idx_kickoff_status');
            
            // Index for team conflict validation
            $table->index(['home_team_id', 'kickoff_time'], 'idx_home_team_kickoff');
            $table->index(['away_team_id', 'kickoff_time'], 'idx_away_team_kickoff');
            
            // Index for venue conflict validation
            $table->index(['venue', 'kickoff_time'], 'idx_venue_kickoff');
            
            // Index for matchday queries
            $table->index(['competition_id', 'matchday'], 'idx_competition_matchday');
            
            // Composite index for common filtering
            $table->index(['competition_id', 'match_status', 'kickoff_time'], 'idx_competition_status_kickoff');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_matches', function (Blueprint $table) {
            $table->dropIndex('idx_competition_kickoff');
            $table->dropIndex('idx_competition_status');
            $table->dropIndex('idx_kickoff_status');
            $table->dropIndex('idx_home_team_kickoff');
            $table->dropIndex('idx_away_team_kickoff');
            $table->dropIndex('idx_venue_kickoff');
            $table->dropIndex('idx_competition_matchday');
            $table->dropIndex('idx_competition_status_kickoff');
        });
    }
};
