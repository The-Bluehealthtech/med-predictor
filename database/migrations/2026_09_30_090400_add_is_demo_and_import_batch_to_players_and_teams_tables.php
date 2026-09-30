<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extension de périmètre signalée au §7 du Livrable 1, adoptée par défaut
     * après validation générale du sens du travail : sans is_demo sur players
     * et teams, le bandeau "Données de démonstration" ne peut pas s'appliquer
     * à une fiche joueur ou équipe fictive consultée isolément (hors contexte
     * d'un match). Colonnes nullable/valeur par défaut, aucune donnée existante
     * modifiée ou supprimée.
     */
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->boolean('is_demo')->default(false);
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('is_demo')->default(false);
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('import_batch_id');
            $table->dropColumn('is_demo');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropConstrainedForeignId('import_batch_id');
            $table->dropColumn('is_demo');
        });
    }
};
