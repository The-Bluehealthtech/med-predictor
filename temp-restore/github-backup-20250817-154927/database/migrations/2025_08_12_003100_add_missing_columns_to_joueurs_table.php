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
        Schema::table('joueurs', function (Blueprint $table) {
            // Colonnes d'adhérence (Pillar 5)
            $table->decimal('taux_presence_entrainements', 5, 2)->nullable()->comment('Pourcentage de présence aux entraînements');
            $table->string('score_adherence_protocole')->nullable()->comment('Score d\'adhérence au protocole de rééducation');
            $table->decimal('disponibilite_generale', 5, 2)->nullable()->comment('Pourcentage de disponibilité générale');
            
            // Colonnes de valeur marchande (Pillar 4)
            $table->decimal('valeur_marchande_euros', 12, 2)->nullable()->comment('Valeur marchande en euros');
            $table->integer('duree_contrat_restante')->nullable()->comment('Durée du contrat restante en années');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('joueurs', function (Blueprint $table) {
            $table->dropColumn([
                'taux_presence_entrainements',
                'score_adherence_protocole',
                'disponibilite_generale',
                'valeur_marchande_euros',
                'duree_contrat_restante',
                'passeport',
                'permis_conduire'
            ]);
        });
    }
};
