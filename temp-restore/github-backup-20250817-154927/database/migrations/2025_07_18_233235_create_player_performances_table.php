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
        Schema::create('player_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->nullable()->constrained('matches')->onDelete('set null');
            $table->foreignId('competition_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('performance_date');
            
            // Dimensions physiques
            $table->decimal('physical_score', 5, 2)->nullable();
            $table->decimal('endurance_score', 5, 2)->nullable();
            $table->decimal('strength_score', 5, 2)->nullable();
            $table->decimal('speed_score', 5, 2)->nullable();
            $table->decimal('agility_score', 5, 2)->nullable();
            $table->integer('recovery_time')->nullable(); // en minutes
            $table->decimal('fatigue_level', 5, 2)->nullable();
            $table->decimal('muscle_mass', 5, 2)->nullable(); // en kg
            $table->decimal('body_fat_percentage', 5, 2)->nullable();
            $table->decimal('vo2_max', 5, 2)->nullable(); // ml/kg/min
            $table->decimal('lactate_threshold', 5, 2)->nullable(); // mmol/L
            
            // Dimensions techniques
            $table->decimal('technical_score', 5, 2)->nullable();
            $table->decimal('passing_accuracy', 5, 2)->nullable();
            $table->decimal('shooting_accuracy', 5, 2)->nullable();
            $table->decimal('dribbling_skill', 5, 2)->nullable();
            $table->decimal('tackling_effectiveness', 5, 2)->nullable();
            $table->decimal('heading_accuracy', 5, 2)->nullable();
            $table->decimal('crossing_accuracy', 5, 2)->nullable();
            $table->decimal('free_kick_accuracy', 5, 2)->nullable();
            $table->decimal('penalty_accuracy', 5, 2)->nullable();
            
            // Dimensions tactiques
            $table->decimal('tactical_score', 5, 2)->nullable();
            $table->decimal('positioning_awareness', 5, 2)->nullable();
            $table->decimal('decision_making', 5, 2)->nullable();
            $table->decimal('game_intelligence', 5, 2)->nullable();
            $table->decimal('team_work_rate', 5, 2)->nullable();
            $table->decimal('pressing_intensity', 5, 2)->nullable();
            $table->decimal('defensive_organization', 5, 2)->nullable();
            $table->decimal('attacking_movement', 5, 2)->nullable();
            
            // Dimensions mentales
            $table->decimal('mental_score', 5, 2)->nullable();
            $table->decimal('confidence_level', 5, 2)->nullable();
            $table->decimal('stress_management', 5, 2)->nullable();
            $table->decimal('focus_concentration', 5, 2)->nullable();
            $table->decimal('motivation_level', 5, 2)->nullable();
            $table->decimal('leadership_qualities', 5, 2)->nullable();
            $table->decimal('pressure_handling', 5, 2)->nullable();
            $table->decimal('mental_toughness', 5, 2)->nullable();
            
            // Dimensions sociales
            $table->decimal('social_score', 5, 2)->nullable();
            $table->decimal('team_cohesion', 5, 2)->nullable();
            $table->decimal('communication_skills', 5, 2)->nullable();
            $table->decimal('coachability', 5, 2)->nullable();
            $table->decimal('discipline_level', 5, 2)->nullable();
            $table->decimal('professional_attitude', 5, 2)->nullable();
            $table->decimal('media_handling', 5, 2)->nullable();
            $table->decimal('fan_interaction', 5, 2)->nullable();
            
            // Score composite
            $table->decimal('overall_performance_score', 5, 2)->nullable();
            $table->decimal('performance_trend', 5, 2)->nullable();
            $table->json('improvement_areas')->nullable();
            $table->json('strengths_highlighted')->nullable();
            
            // Métadonnées
            $table->enum('data_source', ['match', 'training', 'assessment', 'fifa_connect', 'medical_device'])->default('assessment');
            $table->enum('assessment_method', ['coach_evaluation', 'ai_analysis', 'sensor_data', 'video_analysis'])->default('coach_evaluation');
            $table->decimal('data_confidence_level', 5, 2)->nullable();
            $table->json('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['player_id', 'performance_date']);
            $table->index(['competition_id', 'performance_date']);
            $table->index(['team_id', 'performance_date']);
            $table->index(['data_source', 'performance_date']);
            $table->index(['overall_performance_score', 'performance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_performances');
    }
};
