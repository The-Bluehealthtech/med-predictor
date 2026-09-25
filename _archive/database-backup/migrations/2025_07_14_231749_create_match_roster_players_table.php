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
        Schema::create('match_roster_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_roster_id')->constrained('match_rosters')->onDelete('cascade');
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->string('position'); // goalkeeper, defender, midfielder, forward
            $table->boolean('is_starter')->default(false);
            $table->integer('jersey_number');
            $table->timestamps();

            // Ensure unique player per roster
            $table->unique(['match_roster_id', 'player_id']);
            
            // Ensure unique jersey number per roster
            $table->unique(['match_roster_id', 'jersey_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_roster_players');
    }
};
