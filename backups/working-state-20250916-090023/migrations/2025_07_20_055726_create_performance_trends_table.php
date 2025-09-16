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
        Schema::create('performance_trends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('metric_id')->constrained('performance_metrics')->onDelete('cascade');
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->enum('trend_period', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly']);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('initial_value', 10, 4);
            $table->decimal('final_value', 10, 4);
            $table->decimal('change_percentage', 8, 4);
            $table->enum('trend_direction', ['increasing', 'decreasing', 'stable']);
            $table->enum('trend_strength', ['weak', 'moderate', 'strong']);
            $table->integer('data_points_count');
            $table->decimal('confidence_level', 3, 2)->default(1.00);
            $table->text('analysis_notes')->nullable();
            $table->text('ai_insights')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['player_id', 'trend_period']);
            $table->index(['metric_id', 'trend_period']);
            $table->index(['trend_direction', 'trend_strength']);
            $table->index(['start_date', 'end_date']);
            $table->index(['change_percentage']);
            $table->index(['confidence_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_trends');
    }
};
