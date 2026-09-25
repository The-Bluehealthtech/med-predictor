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
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('blood_pressure_systolic')->nullable();
            $table->integer('blood_pressure_diastolic')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable(); // 36.5 to 42.0
            $table->decimal('weight', 5, 2)->nullable(); // kg
            $table->decimal('height', 5, 2)->nullable(); // cm
            $table->decimal('bmi', 4, 2)->nullable();
            $table->string('blood_type', 5)->nullable(); // A+, B-, etc.
            $table->json('allergies')->nullable();
            $table->json('medications')->nullable();
            $table->json('medical_history')->nullable();
            $table->json('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->decimal('risk_score', 5, 4)->default(0.0000); // 0.0000 to 1.0000
            $table->decimal('prediction_confidence', 5, 4)->default(0.0000); // 0.0000 to 1.0000
            $table->timestamp('record_date');
            $table->timestamp('next_checkup_date')->nullable();
            $table->enum('status', ['active', 'archived', 'pending'])->default('active');
            $table->timestamps();
            
            $table->index(['user_id', 'record_date']);
            $table->index(['player_id', 'record_date']);
            $table->index(['status', 'record_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
