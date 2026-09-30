<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Livrable 1, §2 : colonnes brutes (aucune moyenne stockée) alignées sur
     * le cockpit ($excludedKsaFields) et le catalogue KSA
     * (external_player_performance_metrics), + colonnes gardien option A
     * (mêmes tables, nullable, non pertinentes pour les non-gardiens).
     * Aucune des 96 colonnes existantes de player_match_detailed_stats n'est
     * modifiée ou supprimée.
     */
    public function up(): void
    {
        Schema::table('player_match_detailed_stats', function (Blueprint $table) {
            // Alignement cockpit / catalogue KSA
            $table->decimal('expected_goals', 6, 3)->nullable();
            $table->decimal('expected_goals_on_target', 6, 3)->nullable();
            $table->unsignedSmallInteger('progressive_passes')->nullable();
            $table->unsignedSmallInteger('progressive_passes_completed')->nullable();
            $table->unsignedSmallInteger('challenges_defensive')->nullable();
            $table->unsignedSmallInteger('challenges_defensive_won')->nullable();
            $table->unsignedSmallInteger('challenges_attacking')->nullable();
            $table->unsignedSmallInteger('challenges_attacking_won')->nullable();
            $table->unsignedSmallInteger('dribbles_final_third')->nullable();
            $table->unsignedSmallInteger('dribbles_final_third_completed')->nullable();
            $table->unsignedSmallInteger('chances_created')->nullable();
            $table->unsignedSmallInteger('mistakes_leading_to_shot')->nullable();
            $table->unsignedSmallInteger('mistakes_leading_to_goal')->nullable();

            // Champs gardien (option A recommandée au Livrable 1, §2)
            $table->unsignedSmallInteger('gk_shots_faced')->nullable();
            $table->unsignedSmallInteger('gk_shots_faced_on_target')->nullable();
            $table->unsignedSmallInteger('gk_saves')->nullable();
            $table->unsignedSmallInteger('gk_goals_conceded')->nullable();
            $table->decimal('gk_expected_goals_faced', 6, 3)->nullable();
            $table->unsignedSmallInteger('gk_claims_exits')->nullable();
            $table->unsignedSmallInteger('gk_long_passes')->nullable();
            $table->unsignedSmallInteger('gk_long_passes_completed')->nullable();
            $table->unsignedSmallInteger('gk_errors')->nullable();

            // Traçabilité (§7)
            $table->boolean('is_demo')->default(false);
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('player_match_detailed_stats', function (Blueprint $table) {
            $table->dropConstrainedForeignId('import_batch_id');
            $table->dropColumn([
                'is_demo',
                'expected_goals', 'expected_goals_on_target',
                'progressive_passes', 'progressive_passes_completed',
                'challenges_defensive', 'challenges_defensive_won',
                'challenges_attacking', 'challenges_attacking_won',
                'dribbles_final_third', 'dribbles_final_third_completed',
                'chances_created', 'mistakes_leading_to_shot', 'mistakes_leading_to_goal',
                'gk_shots_faced', 'gk_shots_faced_on_target', 'gk_saves', 'gk_goals_conceded',
                'gk_expected_goals_faced', 'gk_claims_exits', 'gk_long_passes',
                'gk_long_passes_completed', 'gk_errors',
            ]);
        });
    }
};
