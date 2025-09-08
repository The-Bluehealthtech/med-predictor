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
        Schema::table('match_rosters', function (Blueprint $table) {
            // Check if player_id column exists before trying to drop it
            if (Schema::hasColumn('match_rosters', 'player_id')) {
                // Drop the unique constraint that includes player_id
                $table->dropUnique(['match_id', 'team_id', 'player_id']);
                
                // Drop the player_id column
                $table->dropForeign(['player_id']);
                $table->dropColumn('player_id');
                
                // Add new unique constraint without player_id
                $table->unique(['match_id', 'team_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_rosters', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique(['match_id', 'team_id']);
            
            // Add back player_id column
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            
            // Add back the original unique constraint
            $table->unique(['match_id', 'team_id', 'player_id']);
        });
    }
};
