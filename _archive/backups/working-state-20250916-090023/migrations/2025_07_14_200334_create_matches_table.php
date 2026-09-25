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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->dateTime('match_date')->nullable();
            $table->foreignId('competition_id')->constrained('competitions')->onDelete('cascade');
            $table->foreignId('home_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('away_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('home_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('away_club_id')->nullable()->constrained('clubs')->nullOnDelete();
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
            $table->string('match_status')->nullable();
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
            $table->string('broadcast_info')->nullable();
            $table->string('ticket_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
