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
        Schema::create('license_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('association_id')->constrained('associations')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            // État de la demande
            $table->enum('status', [
                'draft',           // Brouillon (En cours)
                'submitted',       // Demande envoyée
                'pending',         // En attente de validation
                'approved',        // Validée
                'rejected',        // Rejetée
                'returned'         // Retournée pour corrections
            ])->default('draft');
            
            // Données de la demande
            $table->text('address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('legal_guardian')->nullable();
            $table->string('school_professional_status')->nullable();
            $table->string('parental_consent')->nullable();
            $table->string('license_type')->nullable();
            $table->text('previous_clubs')->nullable();
            $table->string('previous_license_number')->nullable();
            
            // Documents et validations
            $table->string('pcma_status')->nullable(); // Statut PCMA
            $table->string('medical_certificate_path')->nullable();
            $table->string('vaccination_card_path')->nullable();
            $table->string('insurance_path')->nullable();
            $table->string('cin_copy_path')->nullable();
            $table->string('birth_certificate_path')->nullable();
            $table->string('transfer_authorization_path')->nullable();
            $table->string('school_certificate_path')->nullable();
            
            // Suivi
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_requests');
    }
};
