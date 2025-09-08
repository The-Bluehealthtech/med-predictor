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
        Schema::create('medical_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('health_record_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('prediction_type'); // 'injury_risk', 'performance_prediction', 'health_condition'
            $table->string('predicted_condition');
            $table->decimal('risk_probability', 5, 4); // 0.0000 to 1.0000
            $table->decimal('confidence_score', 5, 4); // 0.0000 to 1.0000
            $table->json('prediction_factors')->nullable();
            $table->json('recommendations')->nullable();
            $table->timestamp('prediction_date');
            $table->timestamp('valid_until')->nullable();
            $table->enum('status', ['active', 'expired', 'verified', 'false_positive'])->default('active');
            $table->string('ai_model_version')->default('1.0');
            $table->json('prediction_notes')->nullable();
            $table->timestamps();
            
            $table->index(['player_id', 'prediction_type']);
            $table->index(['health_record_id', 'status']);
            $table->index(['prediction_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_predictions');
    }
};
