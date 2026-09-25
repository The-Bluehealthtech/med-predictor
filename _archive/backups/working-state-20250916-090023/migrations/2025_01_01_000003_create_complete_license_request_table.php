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
            
            // ========================================
            // 🆔 IDENTIFIANTS FIFA CONNECT ID
            // ========================================
            $table->string('fifa_connect_id')->unique()->comment('ID unique FIFA Connect');
            $table->string('fifa_license_request_number')->unique()->comment('Numéro de demande FIFA');
            $table->string('fifa_license_number')->nullable()->comment('Numéro de licence FIFA (après approbation)');
            
            // ========================================
            // 📋 INFORMATIONS DE LA DEMANDE
            // ========================================
            $table->enum('request_type', ['new_license', 'renewal', 'upgrade', 'transfer', 'replacement'])->default('new_license');
            $table->enum('license_type', ['player', 'staff', 'medical', 'coach', 'referee', 'administrative'])->default('player');
            $table->enum('license_category', ['amateur', 'semi_pro', 'professional', 'international'])->default('amateur');
            $table->enum('license_level', ['basic', 'intermediate', 'advanced', 'expert'])->default('basic');
            $table->enum('request_status', ['draft', 'submitted', 'under_review', 'additional_info_required', 'approved', 'rejected', 'expired'])->default('draft');
            $table->text('request_reason')->comment('Raison de la demande de licence');
            $table->enum('validity_period', ['1_year', '2_years', '3_years', '5_years'])->default('1_year');
            
            // ========================================
            // 👤 IDENTITÉ COMPLÈTE DU JOUEUR
            // ========================================
            
            // Informations personnelles de base
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('full_name')->comment('Nom complet officiel');
            $table->date('date_of_birth');
            $table->string('place_of_birth')->nullable();
            $table->string('nationality');
            $table->string('second_nationality')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('national_id_number')->nullable();
            $table->string('birth_certificate_number')->nullable();
            
            // Informations physiques
            $table->integer('height')->comment('Taille en cm');
            $table->integer('weight')->comment('Poids en kg');
            $table->enum('eye_color', ['brown', 'blue', 'green', 'gray', 'hazel', 'other'])->nullable();
            $table->enum('hair_color', ['black', 'brown', 'blonde', 'red', 'gray', 'white', 'other'])->nullable();
            $table->string('distinguishing_marks')->nullable()->comment('Marques distinctives');
            
            // Informations de contact
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            // Adresse
            $table->string('current_address')->nullable();
            $table->string('current_city')->nullable();
            $table->string('current_country')->nullable();
            $table->string('current_postal_code')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('permanent_city')->nullable();
            $table->string('permanent_country')->nullable();
            $table->string('permanent_postal_code')->nullable();
            
            // ========================================
            // 🏢 INFORMATIONS INSTITUTIONNELLES
            // ========================================
            
            // Club actuel
            $table->unsignedBigInteger('current_club_id')->nullable();
            $table->string('current_club_name')->nullable();
            $table->string('current_club_city')->nullable();
            $table->string('current_club_country')->nullable();
            $table->date('club_contract_start')->nullable();
            $table->date('club_contract_end')->nullable();
            $table->string('club_role')->nullable()->comment('Rôle dans le club');
            
            // Association nationale
            $table->unsignedBigInteger('national_association_id')->nullable();
            $table->string('national_association_name')->nullable();
            $table->string('national_association_code')->nullable();
            $table->string('national_association_country')->nullable();
            
            // Confédération
            $table->unsignedBigInteger('confederation_id')->nullable();
            $table->string('confederation_name')->nullable();
            $table->string('confederation_code')->nullable();
            
            // ========================================
            // 📚 HISTORIQUE DES LICENCES (VU PAR L'ASSOCIATION)
            // ========================================
            
            // Numéro de licence locale
            $table->string('local_license_number')->nullable()->comment('Numéro de licence locale du pays');
            
            // Licences précédentes
            $table->json('previous_licenses')->nullable()->comment('Historique des licences précédentes');
            $table->string('previous_license_numbers')->nullable()->comment('Numéros des licences précédentes');
            $table->date('last_license_issue_date')->nullable()->comment('Date de la dernière licence');
            $table->date('last_license_expiry_date')->nullable()->comment('Date d\'expiration de la dernière licence');
            $table->string('last_license_status')->nullable()->comment('Statut de la dernière licence');
            $table->string('last_license_category')->nullable()->comment('Catégorie de la dernière licence');
            $table->string('last_license_level')->nullable()->comment('Niveau de la dernière licence');
            $table->string('last_license_issuer')->nullable()->comment('Émetteur de la dernière licence');
            $table->string('last_license_country')->nullable()->comment('Pays de la dernière licence');
            
            // Suspensions et sanctions
            $table->json('disciplinary_history')->nullable()->comment('Historique disciplinaire complet');
            $table->boolean('has_active_suspension')->default(false);
            $table->date('suspension_start_date')->nullable();
            $table->date('suspension_end_date')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->string('suspension_issuer')->nullable()->comment('Qui a prononcé la suspension');
            $table->string('suspension_type')->nullable()->comment('Type de suspension');
            
            // Historique des transferts
            $table->json('transfer_history')->nullable()->comment('Historique des transferts entre clubs');
            $table->string('previous_clubs')->nullable()->comment('Clubs précédents');
            $table->date('last_transfer_date')->nullable()->comment('Date du dernier transfert');
            
            // ========================================
            // 🏥 INFORMATIONS MÉDICALES (VU PAR L'ASSOCIATION)
            // ========================================
            
            // Certificat médical
            $table->string('medical_certificate_number')->nullable();
            $table->date('medical_certificate_issue_date')->nullable();
            $table->date('medical_certificate_expiry_date')->nullable();
            $table->string('medical_certificate_issuer')->nullable();
            $table->text('medical_restrictions')->nullable()->comment('Restrictions médicales');
            $table->text('medical_conditions')->nullable()->comment('Conditions médicales');
            $table->string('medical_clearance_status')->nullable()->comment('Statut de l\'aptitude médicale');
            
            // Tests et examens
            $table->json('medical_tests')->nullable()->comment('Tests médicaux effectués avec résultats');
            $table->date('last_medical_checkup')->nullable();
            $table->string('medical_doctor_name')->nullable()->comment('Nom du médecin');
            $table->string('medical_clinic_name')->nullable()->comment('Nom de la clinique');
            $table->text('medical_notes')->nullable()->comment('Notes médicales additionnelles');
            
            // Antécédents médicaux
            $table->json('medical_history')->nullable()->comment('Antécédents médicaux du joueur');
            $table->boolean('has_chronic_conditions')->default(false)->comment('A des conditions chroniques');
            $table->text('chronic_conditions_details')->nullable()->comment('Détails des conditions chroniques');
            $table->boolean('takes_medication')->default(false)->comment('Prend des médicaments');
            $table->text('medication_details')->nullable()->comment('Détails des médicaments');
            
            // ========================================
            // 📄 DOCUMENTS REQUIS
            // ========================================
            
            // Documents d'identité
            $table->string('passport_copy_path')->nullable()->comment('Copie du passeport');
            $table->string('national_id_copy_path')->nullable()->comment('Copie de la carte d\'identité');
            $table->string('birth_certificate_copy_path')->nullable()->comment('Copie de l\'acte de naissance');
            $table->string('photo_path')->nullable()->comment('Photo d\'identité');
            
            // Documents sportifs
            $table->string('previous_license_copy_path')->nullable()->comment('Copie de la licence précédente');
            $table->string('club_contract_copy_path')->nullable()->comment('Copie du contrat de club');
            $table->string('medical_certificate_copy_path')->nullable()->comment('Copie du certificat médical');
            
            // Documents additionnels
            $table->json('additional_documents')->nullable()->comment('Documents additionnels requis');
            $table->text('document_notes')->nullable()->comment('Notes sur les documents');
            
            // ========================================
            // 🔐 PROCESSUS DE VALIDATION
            // ========================================
            
            // Demandeur
            $table->unsignedBigInteger('requested_by')->nullable()->comment('Utilisateur qui fait la demande');
            $table->timestamp('requested_at')->nullable()->comment('Date de la demande');
            $table->string('request_notes')->nullable()->comment('Notes du demandeur');
            
            // Validation club
            $table->unsignedBigInteger('club_approved_by')->nullable()->comment('Approbation par le club');
            $table->timestamp('club_approved_at')->nullable();
            $table->text('club_approval_notes')->nullable();
            
            // Validation association
            $table->unsignedBigInteger('association_reviewed_by')->nullable()->comment('Révision par l\'association');
            $table->timestamp('association_reviewed_at')->nullable();
            $table->text('association_review_notes')->nullable();
            $table->enum('association_decision', ['pending', 'approved', 'rejected', 'additional_info_required'])->default('pending');
            
            // Validation finale FIFA
            $table->unsignedBigInteger('fifa_approved_by')->nullable()->comment('Approbation finale FIFA');
            $table->timestamp('fifa_approved_at')->nullable();
            $table->text('fifa_approval_notes')->nullable();
            $table->enum('fifa_decision', ['pending', 'approved', 'rejected', 'additional_info_required'])->default('pending');
            
            // Rejet
            $table->text('rejection_reason')->nullable()->comment('Raison du rejet');
            $table->string('rejected_by')->nullable()->comment('Qui a rejeté');
            $table->timestamp('rejected_at')->nullable();
            $table->text('correction_instructions')->nullable()->comment('Instructions de correction');
            
            // ========================================
            // 📊 MÉTADONNÉES ET SUIVI
            // ========================================
            
            // Dates importantes
            $table->date('license_issue_date')->nullable()->comment('Date d\'émission de la licence');
            $table->date('license_expiry_date')->nullable()->comment('Date d\'expiration de la licence');
            $table->date('license_renewal_date')->nullable()->comment('Date de renouvellement');
            
            // Statut de la licence
            $table->enum('license_status', ['active', 'suspended', 'expired', 'cancelled', 'pending'])->default('pending');
            $table->json('license_restrictions')->nullable()->comment('Restrictions sur la licence');
            $table->text('license_notes')->nullable()->comment('Notes sur la licence');
            
            // Suivi et audit
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            
            // ========================================
            // 🔗 CLÉS ÉTRANGÈRES
            // ========================================
            
            $table->foreign('current_club_id')->references('id')->on('clubs')->onDelete('set null');
            $table->foreign('national_association_id')->references('id')->on('associations')->onDelete('set null');
            $table->foreign('confederation_id')->references('id')->on('confederations')->onDelete('set null');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('club_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('association_reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('fifa_approved_by')->references('id')->on('users')->onDelete('set null');
            
            // ========================================
            // 📍 INDEX POUR PERFORMANCE
            // ========================================
            
            $table->index(['fifa_connect_id']);
            $table->index(['local_license_number']);
            $table->index(['request_status']);
            $table->index(['license_type']);
            $table->index(['current_club_id']);
            $table->index(['national_association_id']);
            $table->index(['requested_by']);
            $table->index(['created_at']);
            $table->index(['license_status']);
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


