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
        Schema::create('lineups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->foreignId('competition_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('match_id')->nullable()->constrained('game_matches')->onDelete('set null');
            $table->string('name');
            $table->string('formation', 10);
            $table->string('tactical_style')->nullable();
            $table->text('playing_philosophy')->nullable();
            $table->foreignId('captain_id')->nullable()->constrained('players')->onDelete('set null');
            $table->foreignId('vice_captain_id')->nullable()->constrained('players')->onDelete('set null');
            $table->foreignId('penalty_taker_id')->nullable()->constrained('players')->onDelete('set null');
            $table->foreignId('free_kick_taker_id')->nullable()->constrained('players')->onDelete('set null');
            $table->foreignId('corner_taker_id')->nullable()->constrained('players')->onDelete('set null');
            $table->enum('match_type', ['league', 'cup', 'friendly', 'international']);
            $table->string('opponent')->nullable();
            $table->enum('venue', ['home', 'away', 'neutral']);
            $table->string('weather_conditions')->nullable();
            $table->string('pitch_condition')->nullable();
            $table->text('tactical_notes')->nullable();
            $table->text('substitutions_plan')->nullable();
            $table->text('set_pieces_strategy')->nullable();
            $table->string('pressing_intensity')->nullable();
            $table->string('possession_style')->nullable();
            $table->string('counter_attack_style')->nullable();
            $table->string('defensive_line_height')->nullable();
            $table->string('marking_system')->nullable();
            $table->enum('status', ['planned', 'confirmed', 'used', 'archived'])->default('planned');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['club_id', 'status']);
            $table->index(['team_id', 'status']);
            $table->index(['match_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lineups');
    }
};
