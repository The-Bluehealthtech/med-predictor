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
        Schema::table('clubs', function (Blueprint $table) {
            // Champs FIFA existants (déjà présents)
            // $table->string('fifa_connect_id')->nullable()->unique();
            
            // Nouveaux champs FIFA pour les transferts
            $table->string('fifa_club_id')->nullable()->unique(); // ID FIFA unique du club
            $table->foreignId('federation_id')->nullable()->constrained('federations')->onDelete('set null');
            $table->enum('fifa_license_status', [
                'active', 'suspended', 'expired', 'revoked', 'pending'
            ])->default('active');
            $table->date('fifa_license_expiry')->nullable();
            
            // Informations de transfert
            $table->boolean('can_conduct_transfers')->default(true);
            $table->boolean('can_sign_foreign_players')->default(true);
            $table->integer('foreign_player_limit')->nullable();
            $table->integer('current_foreign_players')->default(0);
            
            // Informations financières
            $table->decimal('transfer_budget', 15, 2)->nullable();
            $table->string('budget_currency', 3)->default('EUR');
            
            // Métadonnées FIFA
            $table->json('fifa_club_data')->nullable(); // Données complètes du club FIFA
            $table->timestamp('fifa_last_sync')->nullable();
            
            // Index
            $table->index(['fifa_club_id']);
            $table->index(['federation_id']);
            $table->index(['fifa_license_status']);
            $table->index(['can_conduct_transfers']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropForeign(['federation_id']);
            $table->dropIndex(['fifa_club_id', 'federation_id', 'fifa_license_status', 'can_conduct_transfers']);
            $table->dropColumn([
                'fifa_club_id', 'federation_id', 'fifa_license_status', 'fifa_license_expiry',
                'can_conduct_transfers', 'can_sign_foreign_players', 'foreign_player_limit',
                'current_foreign_players', 'transfer_budget', 'budget_currency',
                'fifa_club_data', 'fifa_last_sync'
            ]);
        });
    }
};
