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
        Schema::create('fhir_patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // FHIR Patient Resource Fields
            $table->string('resource_type')->default('Patient');
            $table->json('identifier')->nullable()->comment('Array of identifiers (SSN, Medical Record Number, etc.)');
            $table->boolean('active')->default(true);
            $table->json('name')->comment('Array of human names (family, given, prefix, suffix)');
            $table->json('telecom')->nullable()->comment('Array of contact details (phone, email, etc.)');
            $table->enum('gender', ['male', 'female', 'other', 'unknown']);
            $table->date('birth_date');
            $table->json('address')->nullable()->comment('Array of addresses');
            $table->json('marital_status')->nullable()->comment('Marital status coding');
            $table->json('multiple_birth')->nullable()->comment('Multiple birth indicator or number');
            $table->json('photo')->nullable()->comment('Array of patient photos');
            $table->json('contact')->nullable()->comment('Array of contact persons');
            $table->json('communication')->nullable()->comment('Array of language preferences');
            $table->json('general_practitioner')->nullable()->comment('Array of general practitioners');
            $table->json('managing_organization')->nullable()->comment('Organization that manages the patient');
            $table->json('link')->nullable()->comment('Array of links to other patients');
            
            // FIT Specific Fields
            $table->string('fit_patient_id')->unique()->comment('FIT internal patient ID');
            $table->string('preferred_language')->default('fr');
            $table->json('emergency_contact')->nullable()->comment('Emergency contact information');
            $table->json('insurance_info')->nullable()->comment('Insurance information');
            $table->json('medical_history')->nullable()->comment('Summary of medical history');
            $table->json('allergies')->nullable()->comment('Array of known allergies');
            $table->json('medications')->nullable()->comment('Current medications');
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable()->comment('User who created the patient record');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('User who last updated the patient record');
            $table->timestamps();
            
            // Indexes
            $table->index('fit_patient_id');
            $table->index('active');
            $table->index('gender');
            $table->index('birth_date');
            $table->index('created_by');
            $table->index('updated_by');
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fhir_patients');
    }
};
