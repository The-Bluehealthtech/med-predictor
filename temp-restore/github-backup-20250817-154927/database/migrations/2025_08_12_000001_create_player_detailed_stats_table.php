<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_detailed_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('joueurs')->onDelete('cascade');
            $table->foreignId('match_id')->nullable()->constrained('matches')->onDelete('set null');
            $table->foreignId('season_id')->nullable()->constrained('seasons')->onDelete('set null');
            $table->date('stats_date');
            
            // === STATISTIQUES OFFENSIVES ===
            $table->integer('shots_on_target')->default(0)->comment('Tirs cadrés');
            $table->integer('total_shots')->default(0)->comment('Tirs totaux');
            $table->decimal('shooting_accuracy', 5, 2)->default(0)->comment('Précision tirs en %');
            $table->integer('key_passes')->default(0)->comment('Passes clés');
            $table->integer('successful_crosses')->default(0)->comment('Centres réussis');
            $table->integer('successful_dribbles')->default(0)->comment('Dribbles réussis');
            $table->integer('big_chances_created')->default(0)->comment('Grosses occasions créées');
            $table->integer('big_chances_missed')->default(0)->comment('Grosses occasions manquées');
            $table->integer('offsides')->default(0)->comment('Hors-jeu');
            $table->integer('fouls_drawn')->default(0)->comment('Fautes subies');
            
            // === STATISTIQUES PHYSIQUES ===
            $table->decimal('distance_covered_km', 6, 2)->default(0)->comment('Distance parcourue en km');
            $table->decimal('max_speed_kmh', 5, 2)->default(0)->comment('Vitesse maximale en km/h');
            $table->decimal('avg_speed_kmh', 5, 2)->default(0)->comment('Vitesse moyenne en km/h');
            $table->integer('sprint_count')->default(0)->comment('Nombre de sprints');
            $table->integer('acceleration_count')->default(0)->comment('Nombre d\'accélérations');
            $table->integer('deceleration_count')->default(0)->comment('Nombre de décélérations');
            $table->integer('direction_changes')->default(0)->comment('Changements de direction');
            $table->integer('jump_count')->default(0)->comment('Nombre de sauts');
            $table->decimal('high_intensity_distance', 6, 2)->default(0)->comment('Distance haute intensité');
            $table->decimal('sprint_distance', 6, 2)->default(0)->comment('Distance en sprint');
            
            // === STATISTIQUES TECHNIQUES ===
            $table->integer('long_passes')->default(0)->comment('Passes longues');
            $table->integer('successful_tackles')->default(0)->comment('Tacles réussis');
            $table->integer('interceptions')->default(0)->comment('Interceptions');
            $table->integer('clearances')->default(0)->comment('Dégagements');
            $table->integer('fouls_committed')->default(0)->comment('Fautes commises');
            $table->integer('yellow_cards')->default(0)->comment('Cartons jaunes');
            $table->integer('red_cards')->default(0)->comment('Cartons rouges');
            $table->integer('blocks')->default(0)->comment('Blocages');
            $table->integer('aerial_duels_won')->default(0)->comment('Duels aériens gagnés');
            $table->integer('ground_duels_won')->default(0)->comment('Duels au sol gagnés');
            
            // === STATISTIQUES DE MATCH ===
            $table->integer('minutes_played')->default(0)->comment('Minutes jouées');
            $table->decimal('match_rating', 3, 1)->default(0)->comment('Note du match');
            $table->enum('position_played', ['GK', 'CB', 'LB', 'RB', 'DM', 'CM', 'AM', 'LW', 'RW', 'ST'])->nullable();
            $table->boolean('man_of_match')->default(false)->comment('Homme du match');
            $table->enum('performance_level', ['excellent', 'good', 'average', 'poor', 'very_poor'])->nullable();
            
            // === MÉTADONNÉES ===
            $table->enum('data_source', ['match_analysis', 'gps_tracking', 'video_analysis', 'coach_evaluation', 'fifa_connect'])->default('match_analysis');
            $table->decimal('data_confidence', 5, 2)->default(100)->comment('Niveau de confiance des données');
            $table->json('additional_metrics')->nullable()->comment('Métriques supplémentaires');
            $table->text('notes')->nullable()->comment('Notes du coach/analyste');
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['player_id', 'stats_date']);
            $table->index(['match_id', 'player_id']);
            $table->index(['season_id', 'player_id']);
            $table->index(['data_source', 'stats_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_detailed_stats');
    }
};








