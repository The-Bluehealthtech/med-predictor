<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('player_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('season_id')->constrained()->onDelete('cascade');
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            
            // Statistiques de match
            $table->integer('matches_played')->default(0);
            $table->integer('minutes_played')->default(0);
            $table->integer('goals_scored')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('shots_on_target')->default(0);
            $table->integer('shots_total')->default(0);
            $table->integer('passes_completed')->default(0);
            $table->integer('passes_total')->default(0);
            $table->integer('key_passes')->default(0);
            $table->integer('crosses_completed')->default(0);
            $table->integer('crosses_total')->default(0);
            
            // Statistiques défensives
            $table->integer('tackles_won')->default(0);
            $table->integer('tackles_total')->default(0);
            $table->integer('interceptions')->default(0);
            $table->integer('clearances')->default(0);
            $table->integer('blocks')->default(0);
            $table->integer('duels_won')->default(0);
            $table->integer('duels_total')->default(0);
            $table->integer('fouls_committed')->default(0);
            $table->integer('fouls_drawn')->default(0);
            $table->integer('yellow_cards')->default(0);
            $table->integer('red_cards')->default(0);
            
            // Statistiques physiques
            $table->decimal('distance_covered', 5, 2)->default(0); // en km
            $table->decimal('sprint_distance', 5, 2)->default(0); // en km
            $table->decimal('max_speed', 4, 1)->default(0); // en km/h
            $table->integer('sprints_count')->default(0);
            $table->decimal('avg_speed', 4, 1)->default(0); // en km/h
            
            // Métriques avancées
            $table->decimal('expected_goals', 4, 2)->default(0);
            $table->decimal('expected_assists', 4, 2)->default(0);
            $table->decimal('pass_accuracy', 5, 2)->default(0); // en %
            $table->decimal('shot_accuracy', 5, 2)->default(0); // en %
            $table->decimal('tackle_success_rate', 5, 2)->default(0); // en %
            $table->decimal('duel_success_rate', 5, 2)->default(0); // en %
            
            // Ratings et scores
            $table->decimal('overall_rating', 3, 1)->default(0);
            $table->decimal('attacking_rating', 3, 1)->default(0);
            $table->decimal('defending_rating', 3, 1)->default(0);
            $table->decimal('physical_rating', 3, 1)->default(0);
            $table->decimal('technical_rating', 3, 1)->default(0);
            $table->decimal('mental_rating', 3, 1)->default(0);
            
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['player_id', 'season_id']);
            $table->index(['player_id', 'competition_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('player_performances');
    }
};

