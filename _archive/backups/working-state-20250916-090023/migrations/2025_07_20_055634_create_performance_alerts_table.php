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
        Schema::create('performance_alerts', function (Blueprint $table) {
            $table->id();
            $table->enum('alert_type', [
                'injury_risk', 
                'performance_decline', 
                'medical_alert', 
                'compliance_alert', 
                'transfer_alert',
                'fitness_alert',
                'tactical_alert',
                'social_alert'
            ]);
            $table->enum('alert_level', ['low', 'medium', 'high', 'critical']);
            $table->string('title');
            $table->text('description');
            $table->foreignId('player_id')->nullable()->constrained('players')->onDelete('cascade');
            $table->foreignId('club_id')->nullable()->constrained('clubs')->onDelete('cascade');
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->foreignId('metric_id')->nullable()->constrained('performance_metrics')->onDelete('cascade');
            $table->foreignId('dashboard_id')->nullable()->constrained('performance_dashboards')->onDelete('cascade');
            $table->decimal('trigger_value', 10, 4)->nullable();
            $table->decimal('threshold_value', 10, 4)->nullable();
            $table->enum('alert_condition', ['above', 'below', 'equals', 'trend', 'change']);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_acknowledged')->default(false);
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('acknowledged_at')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->boolean('notification_sent')->default(false);
            $table->json('notification_channels')->nullable();
            $table->text('ai_recommendation')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['player_id', 'alert_type']);
            $table->index(['club_id', 'alert_type']);
            $table->index(['team_id', 'alert_type']);
            $table->index(['alert_type', 'alert_level']);
            $table->index(['is_active', 'alert_level']);
            $table->index(['is_acknowledged', 'created_at']);
            $table->index(['is_resolved', 'created_at']);
            $table->index(['created_at']);
            $table->index('metric_id');
            $table->index('dashboard_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_alerts');
    }
};
