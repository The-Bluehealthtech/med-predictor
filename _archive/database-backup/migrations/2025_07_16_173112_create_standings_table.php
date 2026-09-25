<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->onDelete('cascade');
            $table->foreignId('season_id')->nullable()->constrained('seasons')->onDelete('set null');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->integer('played')->default(0);
            $table->integer('won')->default(0);
            $table->integer('drawn')->default(0);
            $table->integer('lost')->default(0);
            $table->integer('goals_for')->default(0);
            $table->integer('goals_against')->default(0);
            $table->integer('goal_difference')->default(0);
            $table->integer('points')->default(0);
            $table->integer('position')->nullable();
            $table->json('form')->nullable();
            $table->integer('home_played')->default(0);
            $table->integer('home_won')->default(0);
            $table->integer('home_drawn')->default(0);
            $table->integer('home_lost')->default(0);
            $table->integer('home_goals_for')->default(0);
            $table->integer('home_goals_against')->default(0);
            $table->integer('away_played')->default(0);
            $table->integer('away_won')->default(0);
            $table->integer('away_drawn')->default(0);
            $table->integer('away_lost')->default(0);
            $table->integer('away_goals_for')->default(0);
            $table->integer('away_goals_against')->default(0);
            $table->integer('clean_sheets')->default(0);
            $table->integer('failed_to_score')->default(0);
            $table->integer('biggest_win')->nullable();
            $table->integer('biggest_loss')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standings');
    }
};
