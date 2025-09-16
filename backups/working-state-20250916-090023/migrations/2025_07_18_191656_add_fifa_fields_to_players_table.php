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
        Schema::table('players', function (Blueprint $table) {
            // Champs FIFA existants (déjà présents)
            // $table->string('fifa_connect_id')->nullable()->unique();
            
            // Nouveaux champs FIFA pour les transferts
            $table->string('fifa_player_id')->nullable()->unique(); // ID FIFA unique du joueur
            $table->string('fifa_passport_number')->nullable(); // Numéro de passeport FIFA
            $table->date('fifa_passport_expiry')->nullable(); // Date d'expiration du passeport FIFA
            $table->enum('fifa_license_status', [
                'active', 'suspended', 'expired', 'revoked', 'pending'
            ])->default('active');
            $table->date('fifa_license_expiry')->nullable();
            
            // Informations de transfert
            $table->boolean('is_transfer_eligible')->default(true);
            $table->date('last_transfer_date')->nullable();
            $table->integer('transfer_count')->default(0);
            
            // Informations de contrat
            $table->foreignId('current_club_id')->nullable()->constrained('clubs')->onDelete('set null');
            $table->foreignId('current_contract_id')->nullable()->constrained('contracts')->onDelete('set null');
            
            // Métadonnées FIFA
            $table->json('fifa_player_data')->nullable(); // Données complètes du joueur FIFA
            $table->timestamp('fifa_last_sync')->nullable();
            
            // Index
            $table->index(['fifa_player_id']);
            $table->index(['fifa_license_status']);
            $table->index(['current_club_id']);
            $table->index(['is_transfer_eligible']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['current_club_id', 'current_contract_id']);
            $table->dropIndex(['fifa_player_id', 'fifa_license_status', 'current_club_id', 'is_transfer_eligible']);
            $table->dropColumn([
                'fifa_player_id', 'fifa_passport_number', 'fifa_passport_expiry',
                'fifa_license_status', 'fifa_license_expiry', 'is_transfer_eligible',
                'last_transfer_date', 'transfer_count', 'current_club_id', 'current_contract_id',
                'fifa_player_data', 'fifa_last_sync'
            ]);
        });
    }
};
