<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g., 'senior_player', 'youth_player', 'coach', 'referee'
            $table->text('description')->nullable();
            $table->json('required_fields'); // Fields required for this template
            $table->json('optional_fields')->nullable(); // Optional fields
            $table->json('validation_rules')->nullable(); // Validation rules
            $table->integer('validity_period_months')->default(12); // How long the license is valid
            $table->decimal('fee', 10, 2)->default(0.00); // License fee
            $table->boolean('requires_medical_check')->default(true);
            $table->boolean('requires_documents')->default(true);
            $table->json('document_requirements')->nullable(); // Required documents
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_templates');
    }
};
