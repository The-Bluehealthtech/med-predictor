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
        Schema::table('confederations', function (Blueprint $table) {
            // Renommer 'code' en 'short_name' pour la cohérence avec associations
            $table->renameColumn('code', 'short_name');
            
            // Champs FIFA Connect
            $table->string('fifa_ranking')->nullable()->after('country');
            $table->string('fifa_version', 20)->nullable()->after('fifa_ranking');
            $table->enum('fifa_sync_status', ['pending', 'syncing', 'synced', 'failed'])->default('pending')->after('fifa_version');
            $table->timestamp('fifa_sync_date')->nullable()->after('fifa_sync_status');
            $table->text('fifa_last_error')->nullable()->after('fifa_sync_date');
            
            // Logo de confédération (remplace logo_path)
            $table->string('confederation_logo_url', 500)->nullable()->after('fifa_last_error');
            
            // Informations supplémentaires
            $table->integer('founded_year')->nullable()->after('confederation_logo_url');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('founded_year');
            
            // Supprimer l'ancien champ logo_path
            $table->dropColumn('logo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('confederations', function (Blueprint $table) {
            // Restaurer l'ancien champ logo_path
            $table->string('logo_path', 255)->nullable();
            
            // Supprimer les nouveaux champs
            $table->dropColumn([
                'short_name',
                'fifa_ranking',
                'fifa_version',
                'fifa_sync_status',
                'fifa_sync_date',
                'fifa_last_error',
                'confederation_logo_url',
                'founded_year',
                'status'
            ]);
            
            // Restaurer l'ancien champ 'code'
            $table->string('code', 10)->unique();
        });
    }
};
