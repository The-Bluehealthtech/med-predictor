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
        Schema::create('external_player_performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->decimal('index_ksa', 12, 4)->nullable();
            $table->decimal('mistakes_leading_to_goals', 12, 4)->nullable();
            $table->decimal('mistakes_leading_to_chances', 12, 4)->nullable();
            $table->decimal('chances', 12, 4)->nullable();
            $table->decimal('chances_successful', 12, 4)->nullable();
            $table->decimal('chances_successful_percentage', 8, 4)->nullable();
            $table->decimal('chances_created', 12, 4)->nullable();
            $table->decimal('involvement_in_scoring_attacks', 12, 4)->nullable();
            $table->decimal('goals_by_head', 12, 4)->nullable();
            $table->decimal('free_kick_shots', 12, 4)->nullable();
            $table->decimal('free_kick_goals', 12, 4)->nullable();
            $table->decimal('progressive_passes', 12, 4)->nullable();
            $table->decimal('progressive_passes_accurate', 12, 4)->nullable();
            $table->decimal('short_passes', 12, 4)->nullable();
            $table->decimal('short_passes_accurate', 12, 4)->nullable();
            $table->decimal('long_passes', 12, 4)->nullable();
            $table->decimal('long_passes_accurate', 12, 4)->nullable();
            $table->decimal('passes_for_a_shot', 12, 4)->nullable();
            $table->decimal('super_long_passes', 12, 4)->nullable();
            $table->decimal('challenges', 12, 4)->nullable();
            $table->decimal('challenges_won', 12, 4)->nullable();
            $table->decimal('defensive_challenges', 12, 4)->nullable();
            $table->decimal('defensive_challenges_won', 12, 4)->nullable();
            $table->decimal('attacking_challenges', 12, 4)->nullable();
            $table->decimal('attacking_challenges_won', 12, 4)->nullable();
            $table->decimal('aerial_challenges', 12, 4)->nullable();
            $table->decimal('aerial_challenges_won', 12, 4)->nullable();
            $table->decimal('dribbles', 12, 4)->nullable();
            $table->decimal('dribbles_successful', 12, 4)->nullable();
            $table->decimal('dribbling_final_third', 12, 4)->nullable();
            $table->decimal('dribbling_final_third_successful', 12, 4)->nullable();
            $table->decimal('loose_ball_recoveries', 12, 4)->nullable();
            $table->string('metric_name');
            $table->decimal('metric_value', 12, 4)->nullable();
            $table->string('metric_unit')->nullable();
            $table->string('source')->default('KSA');
            $table->string('season')->nullable();
            $table->string('competition')->nullable();
            $table->date('measured_at')->nullable();
            $table->json('raw_data')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['player_id', 'metric_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_player_performance_metrics');
    }
};
