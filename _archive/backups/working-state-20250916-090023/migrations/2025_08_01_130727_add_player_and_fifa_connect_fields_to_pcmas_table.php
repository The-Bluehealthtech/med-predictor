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
        Schema::table('pcmas', function (Blueprint $table) {
            // Add player and FIFA Connect ID relationships
            $table->foreignId('player_id')->nullable()->constrained('players')->onDelete('cascade');
            $table->foreignId('fifa_connect_id')->nullable()->constrained('fifa_connect_ids')->onDelete('cascade');
            
            // Add indexes for better performance
            $table->index(['player_id', 'assessment_date']);
            $table->index(['fifa_connect_id', 'assessment_date']);
            $table->index(['player_id', 'status']);
            $table->index(['fifa_connect_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcmas', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->dropForeign(['fifa_connect_id']);
            $table->dropIndex(['player_id', 'assessment_date']);
            $table->dropIndex(['fifa_connect_id', 'assessment_date']);
            $table->dropIndex(['player_id', 'status']);
            $table->dropIndex(['fifa_connect_id', 'status']);
            $table->dropColumn(['player_id', 'fifa_connect_id']);
        });
    }
};
