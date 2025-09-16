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
        Schema::create('game_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('home_team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->foreignId('away_team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->foreignId('home_club_id')->nullable()->constrained('clubs')->onDelete('set null');
            $table->foreignId('away_club_id')->nullable()->constrained('clubs')->onDelete('set null');
            $table->date('match_date')->nullable();
            $table->dateTime('kickoff_time')->nullable();
            $table->string('venue')->nullable();
            $table->string('stadium')->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('attendance')->nullable();
            $table->string('weather_conditions')->nullable();
            $table->string('pitch_condition')->nullable();
            $table->string('referee')->nullable();
            $table->string('assistant_referee_1')->nullable();
            $table->string('assistant_referee_2')->nullable();
            $table->string('fourth_official')->nullable();
            $table->string('var_referee')->nullable();
            $table->enum('match_status', ['scheduled', 'live', 'completed', 'postponed', 'cancelled'])->default('scheduled');
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->integer('home_penalties')->nullable();
            $table->integer('away_penalties')->nullable();
            $table->integer('home_yellow_cards')->nullable();
            $table->integer('away_yellow_cards')->nullable();
            $table->integer('home_red_cards')->nullable();
            $table->integer('away_red_cards')->nullable();
            $table->integer('home_possession')->nullable();
            $table->integer('away_possession')->nullable();
            $table->integer('home_shots')->nullable();
            $table->integer('away_shots')->nullable();
            $table->integer('home_shots_on_target')->nullable();
            $table->integer('away_shots_on_target')->nullable();
            $table->integer('home_corners')->nullable();
            $table->integer('away_corners')->nullable();
            $table->integer('home_fouls')->nullable();
            $table->integer('away_fouls')->nullable();
            $table->integer('home_offsides')->nullable();
            $table->integer('away_offsides')->nullable();
            $table->text('match_highlights')->nullable();
            $table->text('match_report')->nullable();
            $table->text('broadcast_info')->nullable();
            $table->text('ticket_info')->nullable();
            $table->integer('matchday')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('referee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['competition_id', 'match_status']);
            $table->index(['home_team_id', 'away_team_id']);
            $table->index(['home_club_id', 'away_club_id']);
            $table->index(['match_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_matches');
    }
};
