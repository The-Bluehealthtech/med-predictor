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
        Schema::create('joueurs', function (Blueprint $table) {
            $table->id();
            $table->string('fifa_id')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->string('nationalite');
            $table->string('poste');
            $table->integer('taille_cm');
            $table->integer('poids_kg');
            $table->string('club');
            $table->string('club_logo')->nullable();
            $table->string('pays_drapeau')->nullable();
            $table->string('photo_url')->nullable();
            // $table->integer('age')->virtualAs('TIMESTAMPDIFF(YEAR, date_naissance, CURDATE())');
            
            // Statistiques sportives
            $table->integer('buts')->default(0);
            $table->integer('passes_decisives')->default(0);
            $table->integer('matchs')->default(0);
            $table->integer('minutes_jouees')->default(0);
            $table->decimal('note_moyenne', 3, 1)->default(0.0);
            
            // Données FIFA
            $table->integer('fifa_ovr')->default(0);
            $table->integer('fifa_pot')->default(0);
            $table->integer('score_fit')->default(0);
            $table->integer('risque_blessure')->default(0);
            $table->decimal('valeur_marchande', 10, 2)->default(0.00);
            
            // Données médicales
            $table->json('historique_blessures')->nullable();
            $table->json('donnees_sante')->nullable();
            $table->json('statistiques_physiques')->nullable();
            $table->json('statistiques_techniques')->nullable();
            $table->json('statistiques_offensives')->nullable();
            
            // Données des appareils connectés
            $table->json('donnees_devices')->nullable();
            $table->json('donnees_dopage')->nullable();
            $table->json('donnees_sdoh')->nullable();
            
            // Notifications et alertes
            $table->json('notifications')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('joueurs');
    }
};
