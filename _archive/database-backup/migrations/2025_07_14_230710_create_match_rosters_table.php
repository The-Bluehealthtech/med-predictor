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
        Schema::create('match_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('game_matches')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['match_id', 'team_id']);
            // Contrainte logique : max 18 joueurs par équipe et par match à gérer côté application
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_rosters');
    }
};
