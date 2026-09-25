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
        Schema::table('associations', function (Blueprint $table) {
            // Renommer code en short_name pour plus de clarté
            $table->renameColumn('code', 'short_name');
            
            // Ajouter les colonnes FIFA Connect manquantes
            $table->string('confederation', 100)->nullable()->after('country');
            $table->integer('fifa_ranking')->nullable()->after('confederation');
            $table->string('fifa_version', 50)->nullable()->after('fifa_ranking');
            $table->enum('fifa_sync_status', ['pending', 'synced', 'failed'])->default('pending')->after('fifa_version');
            $table->timestamp('fifa_sync_date')->nullable()->after('fifa_sync_status');
            $table->text('fifa_last_error')->nullable()->after('fifa_sync_date');
            
            // Ajouter les colonnes pour les logos et drapeaux
            $table->string('association_logo_url', 500)->nullable()->after('fifa_last_error');
            $table->string('nation_flag_url', 500)->nullable()->after('association_logo_url');
            
            // Ajouter la colonne founded_year
            $table->integer('founded_year')->nullable()->after('nation_flag_url');
            
            // Modifier le statut pour inclure 'suspended'
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées
            $table->dropColumn([
                'confederation',
                'fifa_ranking', 
                'fifa_version',
                'fifa_sync_status',
                'fifa_sync_date',
                'fifa_last_error',
                'association_logo_url',
                'nation_flag_url',
                'founded_year'
            ]);
            
            // Renommer short_name en code
            $table->renameColumn('short_name', 'code');
            
            // Remettre le statut original
            $table->enum('status', ['active', 'inactive'])->default('active')->change();
        });
    }
};
