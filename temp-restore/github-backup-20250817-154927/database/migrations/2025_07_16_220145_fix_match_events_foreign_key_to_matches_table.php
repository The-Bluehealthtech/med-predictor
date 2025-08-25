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
            // Drop the existing foreign key constraint
            $table->dropForeign(['match_id']);
            
            // Add the correct foreign key constraint
            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_events', function (Blueprint $table) {
            // Drop the correct foreign key constraint
            $table->dropForeign(['match_id']);
            
            // Restore the original foreign key constraint
            $table->foreign('match_id')->references('id')->on('game_matches')->onDelete('cascade');
        });
    }
};
