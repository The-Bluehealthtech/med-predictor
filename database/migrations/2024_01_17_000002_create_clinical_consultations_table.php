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
        Schema::create('clinical_consultations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Patient and Clinician references
            $table->uuid('patient_id');
            $table->unsignedBigInteger('clinician_id');
            
            // Consultation details
            $table->string('consultation_type')->default('initial'); // initial, follow_up, emergency
            $table->string('status')->default('scheduled'); // scheduled, in_progress, completed, cancelled
            
            // Clinical data
            $table->text('chief_complaint')->nullable();
            $table->text('history_present_illness')->nullable();
            $table->text('physical_exam')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();
            
            // AI-generated content
            $table->text('ai_summary')->nullable();
            $table->json('ai_insights')->nullable();
            $table->json('ai_recommendations')->nullable();
            
            // Additional data
            $table->json('vital_signs')->nullable();
            $table->json('medications')->nullable();
            $table->json('allergies')->nullable();
            $table->json('diagnosis_codes')->nullable();
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('patient_id');
            $table->index('clinician_id');
            $table->index('consultation_type');
            $table->index('status');
            $table->index('scheduled_at');
            
            // Foreign keys
            $table->foreign('patient_id')->references('id')->on('fhir_patients')->onDelete('cascade');
            $table->foreign('clinician_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_consultations');
    }
};
