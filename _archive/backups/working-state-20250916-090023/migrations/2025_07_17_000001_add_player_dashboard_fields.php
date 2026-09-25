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
        Schema::table('players', function (Blueprint $table) {
            // General Health Score (GHS) fields
            $table->decimal('ghs_physical_score', 5, 2)->nullable()->comment('Physical health score (0-100)');
            $table->decimal('ghs_mental_score', 5, 2)->nullable()->comment('Mental health score (0-100)');
            $table->decimal('ghs_civic_score', 5, 2)->nullable()->comment('Civic engagement score (0-100)');
            $table->decimal('ghs_sleep_score', 5, 2)->nullable()->comment('Sleep quality score (0-100)');
            $table->decimal('ghs_overall_score', 5, 2)->nullable()->comment('Overall GHS score (0-100)');
            $table->string('ghs_color_code', 10)->nullable()->comment('Color code: blue, green, red');
            $table->json('ghs_ai_suggestions')->nullable()->comment('AI-generated health suggestions');
            $table->timestamp('ghs_last_updated')->nullable();
            
            // Player contribution and data ownership
            $table->decimal('contribution_score', 5, 2)->default(0.00)->comment('Player contribution percentage');
            $table->decimal('data_value_estimate', 10, 2)->default(0.00)->comment('Estimated value of player data');
            $table->integer('matches_contributed')->default(0)->comment('Number of matches contributed to');
            $table->integer('training_sessions_logged')->default(0)->comment('Number of training sessions logged');
            $table->integer('health_records_contributed')->default(0)->comment('Number of health records contributed');
            
            // Injury risk assessment
            $table->decimal('injury_risk_score', 5, 4)->default(0.0000)->comment('ML-generated injury risk (0-1)');
            $table->string('injury_risk_level', 20)->nullable()->comment('Risk level: low, medium, high');
            $table->text('injury_risk_reason')->nullable()->comment('Reason for risk assessment');
            $table->json('weekly_health_tips')->nullable()->comment('Weekly AI-generated health tips');
            $table->timestamp('injury_risk_last_assessed')->nullable();
            
            // Match availability
            $table->boolean('match_availability')->default(true)->comment('Player availability for matches');
            $table->timestamp('last_availability_update')->nullable();
            
            // Data export tracking
            $table->timestamp('last_data_export')->nullable();
            $table->integer('data_export_count')->default(0)->comment('Number of times data was exported');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn([
                'ghs_physical_score',
                'ghs_mental_score', 
                'ghs_civic_score',
                'ghs_sleep_score',
                'ghs_overall_score',
                'ghs_color_code',
                'ghs_ai_suggestions',
                'ghs_last_updated',
                'contribution_score',
                'data_value_estimate',
                'matches_contributed',
                'training_sessions_logged',
                'health_records_contributed',
                'injury_risk_score',
                'injury_risk_level',
                'injury_risk_reason',
                'weekly_health_tips',
                'injury_risk_last_assessed',
                'match_availability',
                'last_availability_update',
                'last_data_export',
                'data_export_count'
            ]);
        });
    }
}; 