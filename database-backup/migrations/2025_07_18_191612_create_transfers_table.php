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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->foreignId('club_origin_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('club_destination_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('federation_origin_id')->nullable()->constrained('federations')->onDelete('set null');
            $table->foreignId('federation_destination_id')->nullable()->constrained('federations')->onDelete('set null');
            
            // Informations du transfert
            $table->enum('transfer_type', ['permanent', 'loan', 'free_agent'])->default('permanent');
            $table->enum('transfer_status', [
                'draft', 'pending', 'submitted', 'under_review', 'approved', 'rejected', 'cancelled'
            ])->default('draft');
            $table->enum('itc_status', [
                'not_requested', 'requested', 'pending', 'approved', 'rejected', 'expired'
            ])->default('not_requested');
            
            // Dates importantes
            $table->date('transfer_window_start');
            $table->date('transfer_window_end');
            $table->date('transfer_date');
            $table->date('contract_start_date');
            $table->date('contract_end_date')->nullable();
            $table->date('itc_request_date')->nullable();
            $table->date('itc_response_date')->nullable();
            
            // Informations financières
            $table->decimal('transfer_fee', 15, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->enum('payment_status', ['pending', 'partial', 'completed'])->default('pending');
            
            // Informations FIFA
            $table->string('fifa_transfer_id')->nullable()->unique(); // ID FIFA du transfert
            $table->string('fifa_itc_id')->nullable(); // ID ITC FIFA
            $table->json('fifa_payload')->nullable(); // Données envoyées à FIFA
            $table->json('fifa_response')->nullable(); // Réponse de FIFA
            $table->text('fifa_error_message')->nullable(); // Message d'erreur FIFA
            
            // Informations spécifiques
            $table->boolean('is_minor_transfer')->default(false); // Transfert de mineur
            $table->boolean('is_international')->default(false); // Transfert international
            $table->text('special_conditions')->nullable(); // Conditions spéciales
            $table->text('notes')->nullable(); // Notes internes
            
            // Métadonnées
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['player_id', 'transfer_status']);
            $table->index(['club_origin_id', 'transfer_status']);
            $table->index(['club_destination_id', 'transfer_status']);
            $table->index(['fifa_transfer_id']);
            $table->index(['transfer_date']);
            $table->index(['itc_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
