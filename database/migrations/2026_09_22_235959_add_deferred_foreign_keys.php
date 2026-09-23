<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_records', function (Blueprint $table) {
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
        });

        Schema::table('medical_predictions', function (Blueprint $table) {
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
        });

        Schema::table('game_matches', function (Blueprint $table) {
            $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('set null');
            $table->foreign('home_team_id')->references('id')->on('teams')->onDelete('set null');
            $table->foreign('away_team_id')->references('id')->on('teams')->onDelete('set null');
        });

        Schema::table('lineups', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('set null');
            $table->foreign('captain_id')->references('id')->on('players')->onDelete('set null');
            $table->foreign('vice_captain_id')->references('id')->on('players')->onDelete('set null');
            $table->foreign('penalty_taker_id')->references('id')->on('players')->onDelete('set null');
            $table->foreign('free_kick_taker_id')->references('id')->on('players')->onDelete('set null');
            $table->foreign('corner_taker_id')->references('id')->on('players')->onDelete('set null');
        });

        Schema::table('lineup_players', function (Blueprint $table) {
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
        });

        Schema::table('team_players', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
        });

        Schema::table('competition_club', function (Blueprint $table) {
            $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
        });

        Schema::table('competition_team', function (Blueprint $table) {
            $table->foreign('season_id')->references('id')->on('seasons')->onDelete('set null');
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
        });

        Schema::table('dental_records', function (Blueprint $table) {
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('dental_records', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->dropForeign(['appointment_id']);
        });

        Schema::table('competition_team', function (Blueprint $table) {
            $table->dropForeign(['season_id']);
        });

        Schema::table('competition_club', function (Blueprint $table) {
            $table->dropForeign(['competition_id']);
        });

        Schema::table('team_players', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->dropForeign(['team_id']);
        });

        Schema::table('lineup_players', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
        });

        Schema::table('lineups', function (Blueprint $table) {
            $table->dropForeign(['corner_taker_id']);
            $table->dropForeign(['free_kick_taker_id']);
            $table->dropForeign(['penalty_taker_id']);
            $table->dropForeign(['vice_captain_id']);
            $table->dropForeign(['captain_id']);
            $table->dropForeign(['competition_id']);
            $table->dropForeign(['team_id']);
        });

        Schema::table('game_matches', function (Blueprint $table) {
            $table->dropForeign(['away_team_id']);
            $table->dropForeign(['home_team_id']);
            $table->dropForeign(['competition_id']);
        });

        Schema::table('medical_predictions', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
        });

        Schema::table('health_records', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
        });
    }
};
