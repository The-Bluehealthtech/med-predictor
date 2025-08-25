<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_match_detailed_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('joueurs')->onDelete('cascade');
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('competition_id')->nullable()->constrained('competitions')->onDelete('set null');
            $table->foreignId('season_id')->nullable()->constrained('seasons')->onDelete('set null');
            
            // === INFORMATIONS DE BASE ===
            $table->enum('position_played', ['GK', 'CB', 'LB', 'RB', 'DM', 'CM', 'AM', 'LW', 'RW', 'ST'])->comment('Position jouée');
            $table->integer('minutes_played')->default(0)->comment('Minutes jouées');
            $table->boolean('started_match')->default(false)->comment('A commencé le match');
            $table->boolean('substituted_in')->default(false)->comment('A été remplacé');
            $table->boolean('substituted_out')->default(false)->comment('A remplacé quelqu\'un');
            $table->integer('substitution_minute')->nullable()->comment('Minute de remplacement');
            $table->enum('formation_position', ['GK', 'CB', 'LB', 'RB', 'DM', 'CM', 'AM', 'LW', 'RW', 'ST'])->nullable()->comment('Position dans la formation');
            
            // === STATISTIQUES OFFENSIVES DÉTAILLÉES ===
            $table->integer('goals_scored')->default(0)->comment('Buts marqués');
            $table->integer('assists_provided')->default(0)->comment('Passes décisives');
            $table->integer('shots_total')->default(0)->comment('Tirs totaux');
            $table->integer('shots_on_target')->default(0)->comment('Tirs cadrés');
            $table->integer('shots_off_target')->default(0)->comment('Tirs manqués');
            $table->integer('shots_blocked')->default(0)->comment('Tirs bloqués');
            $table->integer('shots_inside_box')->default(0)->comment('Tirs dans la surface');
            $table->integer('shots_outside_box')->default(0)->comment('Tirs hors de la surface');
            $table->decimal('shooting_accuracy', 5, 2)->default(0)->comment('Précision des tirs en %');
            
            // === PASSES DÉTAILLÉES ===
            $table->integer('passes_total')->default(0)->comment('Passes totales');
            $table->integer('passes_completed')->default(0)->comment('Passes réussies');
            $table->integer('passes_failed')->default(0)->comment('Passes ratées');
            $table->decimal('pass_accuracy', 5, 2)->default(0)->comment('Précision des passes en %');
            $table->integer('key_passes')->default(0)->comment('Passes clés');
            $table->integer('long_passes')->default(0)->comment('Passes longues');
            $table->integer('long_passes_completed')->default(0)->comment('Passes longues réussies');
            $table->integer('crosses_total')->default(0)->comment('Centres totaux');
            $table->integer('crosses_completed')->default(0)->comment('Centres réussis');
            $table->decimal('cross_accuracy', 5, 2)->default(0)->comment('Précision des centres en %');
            
            // === DRIBBLES ET CONTROLE ===
            $table->integer('dribbles_attempted')->default(0)->comment('Dribbles tentés');
            $table->integer('dribbles_completed')->default(0)->comment('Dribbles réussis');
            $table->decimal('dribble_success_rate', 5, 2)->default(0)->comment('Taux de réussite des dribbles en %');
            $table->integer('times_dispossessed')->default(0)->comment('Fois dépossédé du ballon');
            $table->integer('bad_controls')->default(0)->comment('Mauvais contrôles');
            $table->integer('successful_take_ons')->default(0)->comment('Débordements réussis');
            $table->integer('failed_take_ons')->default(0)->comment('Débordements ratés');
            
            // === STATISTIQUES DÉFENSIVES ===
            $table->integer('tackles_total')->default(0)->comment('Tacles totaux');
            $table->integer('tackles_won')->default(0)->comment('Tacles gagnés');
            $table->integer('tackles_lost')->default(0)->comment('Tacles perdus');
            $table->decimal('tackle_success_rate', 5, 2)->default(0)->comment('Taux de réussite des tacles en %');
            $table->integer('interceptions')->default(0)->comment('Interceptions');
            $table->integer('clearances')->default(0)->comment('Dégagements');
            $table->integer('blocks')->default(0)->comment('Blocages');
            $table->integer('clearances_off_line')->default(0)->comment('Dégagements sur la ligne');
            $table->integer('recoveries')->default(0)->comment('Récupérations de ballon');
            
            // === DUELS ET CONTACTS ===
            $table->integer('aerial_duels_total')->default(0)->comment('Duels aériens totaux');
            $table->integer('aerial_duels_won')->default(0)->comment('Duels aériens gagnés');
            $table->integer('aerial_duels_lost')->default(0)->comment('Duels aériens perdus');
            $table->decimal('aerial_duel_success_rate', 5, 2)->default(0)->comment('Taux de réussite des duels aériens en %');
            $table->integer('ground_duels_total')->default(0)->comment('Duels au sol totaux');
            $table->integer('ground_duels_won')->default(0)->comment('Duels au sol gagnés');
            $table->integer('ground_duels_lost')->default(0)->comment('Duels au sol perdus');
            $table->decimal('ground_duel_success_rate', 5, 2)->default(0)->comment('Taux de réussite des duels au sol en %');
            
            // === FAUTES ET DISCIPLINE ===
            $table->integer('fouls_committed')->default(0)->comment('Fautes commises');
            $table->integer('fouls_drawn')->default(0)->comment('Fautes subies');
            $table->integer('yellow_cards')->default(0)->comment('Cartons jaunes');
            $table->integer('red_cards')->default(0)->comment('Cartons rouges');
            $table->integer('second_yellow_cards')->default(0)->comment('Deuxièmes cartons jaunes');
            $table->integer('offsides')->default(0)->comment('Hors-jeu');
            $table->integer('handballs')->default(0)->comment('Mains');
            
            // === STATISTIQUES PHYSIQUES ===
            $table->decimal('distance_covered_km', 6, 2)->default(0)->comment('Distance parcourue en km');
            $table->decimal('distance_sprinting_km', 6, 2)->default(0)->comment('Distance en sprint en km');
            $table->decimal('distance_jogging_km', 6, 2)->default(0)->comment('Distance en jogging en km');
            $table->decimal('distance_walking_km', 6, 2)->default(0)->comment('Distance en marchant en km');
            $table->decimal('max_speed_kmh', 5, 2)->default(0)->comment('Vitesse maximale en km/h');
            $table->decimal('avg_speed_kmh', 5, 2)->default(0)->comment('Vitesse moyenne en km/h');
            $table->integer('sprint_count')->default(0)->comment('Nombre de sprints');
            $table->integer('acceleration_count')->default(0)->comment('Nombre d\'accélérations');
            $table->integer('deceleration_count')->default(0)->comment('Nombre de décélérations');
            $table->integer('direction_changes')->default(0)->comment('Changements de direction');
            
            // === PERFORMANCE ET ÉVALUATION ===
            $table->decimal('match_rating', 3, 1)->default(0)->comment('Note du match (0-10)');
            $table->enum('performance_level', ['excellent', 'very_good', 'good', 'average', 'below_average', 'poor', 'very_poor'])->nullable();
            $table->boolean('man_of_match')->default(false)->comment('Homme du match');
            $table->boolean('team_of_week')->default(false)->comment('Dans l\'équipe de la semaine');
            $table->decimal('fifa_rating', 3, 1)->nullable()->comment('Note FIFA du match');
            $table->decimal('fifa_rating_change', 4, 2)->nullable()->comment('Changement de note FIFA');
            
            // === CONTEXTE DU MATCH ===
            $table->enum('match_importance', ['friendly', 'league', 'cup', 'champions_league', 'international', 'playoff'])->nullable();
            $table->boolean('home_match')->default(false)->comment('Match à domicile');
            $table->boolean('away_match')->default(false)->comment('Match à l\'extérieur');
            $table->string('opponent_team')->nullable()->comment('Équipe adverse');
            $table->integer('team_goals_scored')->default(0)->comment('Buts marqués par l\'équipe');
            $table->integer('team_goals_conceded')->default(0)->comment('Buts encaissés par l\'équipe');
            $table->enum('match_result', ['win', 'draw', 'loss'])->nullable()->comment('Résultat du match');
            $table->integer('goal_difference')->default(0)->comment('Différence de buts');
            
            // === MÉTADONNÉES ===
            $table->enum('data_source', ['official_stats', 'gps_tracking', 'video_analysis', 'coach_evaluation', 'fifa_connect', 'manual_entry'])->default('official_stats');
            $table->decimal('data_confidence', 5, 2)->default(100)->comment('Niveau de confiance des données en %');
            $table->enum('data_quality', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->json('additional_metrics')->nullable()->comment('Métriques supplémentaires');
            $table->text('coach_notes')->nullable()->comment('Notes du coach');
            $table->text('player_notes')->nullable()->comment('Notes du joueur');
            $table->json('metadata')->nullable()->comment('Métadonnées supplémentaires');
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['player_id', 'match_id']);
            $table->index(['match_id', 'team_id']);
            $table->index(['season_id', 'player_id']);
            $table->index(['competition_id', 'match_id']);
            $table->index(['match_rating', 'match_date']);
            $table->index(['goals_scored', 'assists_provided']);
            $table->index(['distance_covered_km', 'match_date']);
            $table->unique(['player_id', 'match_id']); // Un joueur ne peut avoir qu'une seule entrée par match
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_match_detailed_stats');
    }
};








