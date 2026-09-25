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
        Schema::create('license_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Professional, Amateur, Youth, International, etc.
            $table->string('code')->unique(); // prof, amat, youth, intl, etc.
            $table->text('description')->nullable();
            $table->json('requirements')->nullable(); // Required documents, conditions
            $table->json('validation_rules')->nullable(); // Business rules for validation
            $table->json('approval_process')->nullable(); // Steps in approval process
            $table->integer('validity_period_months')->default(12);
            $table->decimal('fee_amount', 10, 2)->default(0.00);
            $table->string('fee_currency', 3)->default('USD');
            $table->boolean('requires_medical_clearance')->default(true);
            $table->boolean('requires_fitness_certificate')->default(true);
            $table->boolean('requires_contract')->default(false);
            $table->boolean('requires_work_permit')->default(false);
            $table->boolean('requires_international_clearance')->default(false);
            $table->json('age_restrictions')->nullable(); // Min/max age requirements
            $table->json('position_restrictions')->nullable(); // Position-specific requirements
            $table->json('experience_requirements')->nullable(); // Years of experience needed
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_association_approval')->default(true);
            $table->boolean('requires_club_approval')->default(true);
            $table->integer('max_players_per_club')->nullable(); // Limit per club
            $table->integer('max_players_total')->nullable(); // Global limit
            $table->json('document_templates')->nullable(); // Template documents
            $table->json('notification_settings')->nullable(); // Who gets notified
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['code', 'is_active']);
            $table->index(['requires_association_approval', 'requires_club_approval']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_types');
    }
}; 