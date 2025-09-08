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
        Schema::create('player_passports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->string('fifa_connect_id')->nullable();
            $table->string('passport_number')->unique();
            $table->enum('passport_type', ['electronic', 'physical', 'temporary'])->default('electronic');
            $table->enum('status', ['active', 'suspended', 'expired', 'revoked', 'pending_validation'])->default('pending_validation');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->date('renewal_date')->nullable();
            $table->string('issuing_authority');
            $table->string('issuing_country');
            $table->string('registration_number')->nullable();
            
            // Informations personnelles FIFA
            $table->string('fifa_name')->nullable();
            $table->string('fifa_first_name')->nullable();
            $table->string('fifa_last_name')->nullable();
            $table->date('fifa_date_of_birth')->nullable();
            $table->string('fifa_nationality')->nullable();
            $table->string('fifa_second_nationality')->nullable();
            $table->string('fifa_place_of_birth')->nullable();
            $table->string('fifa_country_of_birth')->nullable();
            
            // Informations de licence
            $table->enum('license_type', ['professional', 'amateur', 'youth', 'international'])->nullable();
            $table->enum('license_category', ['A', 'B', 'C', 'D', 'E'])->nullable();
            $table->enum('license_status', ['valid', 'pending', 'expired', 'suspended', 'revoked'])->default('pending');
            $table->date('license_issue_date')->nullable();
            $table->date('license_expiry_date')->nullable();
            
            // Informations de transfert
            $table->enum('transfer_status', ['registered', 'pending_transfer', 'transferred', 'free_agent'])->default('registered');
            $table->foreignId('current_club_id')->nullable()->constrained('clubs')->onDelete('set null');
            $table->foreignId('previous_club_id')->nullable()->constrained('clubs')->onDelete('set null');
            $table->json('transfer_history')->nullable();
            $table->enum('itc_status', ['not_required', 'not_requested', 'requested', 'approved', 'rejected'])->default('not_required');
            $table->timestamp('itc_request_date')->nullable();
            $table->timestamp('itc_response_date')->nullable();
            $table->string('itc_response_code')->nullable();
            $table->text('itc_response_message')->nullable();
            
            // Informations médicales
            $table->boolean('medical_clearance')->default(false);
            $table->date('medical_clearance_date')->nullable();
            $table->date('medical_clearance_expiry')->nullable();
            $table->boolean('fitness_certificate')->default(false);
            $table->date('fitness_certificate_date')->nullable();
            $table->date('fitness_certificate_expiry')->nullable();
            $table->enum('doping_test_status', ['not_tested', 'pending', 'passed', 'failed'])->default('not_tested');
            $table->date('doping_test_date')->nullable();
            $table->enum('doping_test_result', ['negative', 'positive', 'inconclusive'])->nullable();
            
            // Informations disciplinaires
            $table->json('disciplinary_record')->nullable();
            $table->json('suspensions')->nullable();
            $table->json('warnings')->nullable();
            $table->json('fines')->nullable();
            $table->integer('disciplinary_points')->default(0);
            
            // Informations de performance
            $table->json('performance_history')->nullable();
            $table->json('achievements')->nullable();
            $table->json('awards')->nullable();
            $table->integer('international_caps')->default(0);
            $table->integer('international_goals')->default(0);
            
            // Documents et signatures
            $table->string('photo_url')->nullable();
            $table->string('signature_url')->nullable();
            $table->string('document_hash')->nullable();
            $table->text('digital_signature')->nullable();
            $table->json('certification_chain')->nullable();
            
            // Conformité et audit
            $table->enum('compliance_status', ['compliant', 'non_compliant', 'pending_review'])->default('pending_review');
            $table->timestamp('last_audit_date')->nullable();
            $table->json('audit_results')->nullable();
            $table->boolean('gdpr_consent')->default(false);
            $table->string('data_retention_policy')->nullable();
            
            // Métadonnées
            $table->string('version')->default('1.0');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['player_id', 'status']);
            $table->index(['fifa_connect_id']);
            $table->index(['passport_number']);
            $table->index(['current_club_id', 'status']);
            $table->index(['expiry_date', 'status']);
            $table->index(['compliance_status']);
            $table->index(['license_status']);
            $table->index(['itc_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_passports');
    }
};
