<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('joueurs', function (Blueprint $table) {
            // === CHAMPS MANQUANTS POUR LA PAGE ===
            
            // Informations personnelles supplémentaires
            $table->string('pied_fort')->nullable()->comment('Pied fort (Gauche/Droit)');
            $table->string('langues_parlees')->nullable()->comment('Langues parlées (JSON)');
            $table->string('passeport')->nullable()->comment('Numéro de passeport');
            $table->string('permis_conduire')->nullable()->comment('Numéro de permis de conduire');
            
            // Statistiques avancées
            $table->integer('tirs_cadres')->default(0)->comment('Tirs cadrés');
            $table->integer('tirs_totaux')->default(0)->comment('Tirs totaux');
            $table->decimal('precision_tirs', 5, 2)->default(0)->comment('Précision des tirs en %');
            $table->integer('passes_cles')->default(0)->comment('Passes clés');
            $table->integer('centres_reussis')->default(0)->comment('Centres réussis');
            $table->integer('dribbles_reussis')->default(0)->comment('Dribbles réussis');
            $table->integer('passes_longues')->default(0)->comment('Passes longues');
            $table->integer('tacles_reussis')->default(0)->comment('Tacles réussis');
            $table->integer('interceptions')->default(0)->comment('Interceptions');
            $table->integer('degaugements')->default(0)->comment('Dégagements');
            $table->integer('fautes_commises')->default(0)->comment('Fautes commises');
            $table->integer('cartons_jaunes')->default(0)->comment('Cartons jaunes');
            $table->integer('cartons_rouges')->default(0)->comment('Cartons rouges');
            
            // Statistiques physiques avancées
            $table->decimal('distance_parcourue_km', 8, 2)->default(0)->comment('Distance parcourue en km');
            $table->decimal('vitesse_maximale_kmh', 5, 2)->default(0)->comment('Vitesse maximale en km/h');
            $table->decimal('vitesse_moyenne_kmh', 5, 2)->default(0)->comment('Vitesse moyenne en km/h');
            $table->integer('sprints')->default(0)->comment('Nombre de sprints');
            $table->integer('accelerations')->default(0)->comment('Nombre d\'accélérations');
            $table->integer('decelerations')->default(0)->comment('Nombre de décélérations');
            $table->integer('changements_direction')->default(0)->comment('Changements de direction');
            $table->integer('sautes')->default(0)->comment('Nombre de sauts');
            
            // Données de santé avancées
            $table->integer('frequence_cardiaque')->nullable()->comment('Fréquence cardiaque en bpm');
            $table->string('tension_arterielle')->nullable()->comment('Tension artérielle (systolique/diastolique)');
            $table->decimal('temperature', 4, 1)->nullable()->comment('Température en °C');
            $table->integer('saturation_o2')->nullable()->comment('Saturation en O2 en %');
            $table->integer('niveau_hydratation')->nullable()->comment('Niveau d\'hydratation en %');
            $table->decimal('cortisol_stress', 6, 2)->nullable()->comment('Niveau de cortisol en µg/dL');
            
            // Données de récupération
            $table->decimal('score_recuperation', 5, 2)->nullable()->comment('Score de récupération (0-100)');
            $table->decimal('fatigue_musculaire', 5, 2)->nullable()->comment('Fatigue musculaire (0-100)');
            $table->decimal('fatigue_centrale', 5, 2)->nullable()->comment('Fatigue centrale (0-100)');
            $table->integer('temps_recuperation_heures')->nullable()->comment('Temps de récupération en heures');
            $table->decimal('score_preparation', 5, 2)->nullable()->comment('Score de préparation (0-100)');
            
            // Données de sommeil
            $table->integer('duree_sommeil_heures')->nullable()->comment('Durée du sommeil en heures');
            $table->integer('sommeil_profond_pourcentage')->nullable()->comment('Pourcentage de sommeil profond');
            $table->integer('sommeil_rem_pourcentage')->nullable()->comment('Pourcentage de sommeil REM');
            $table->integer('efficacite_sommeil')->nullable()->comment('Efficacité du sommeil en %');
            $table->decimal('qualite_sommeil', 3, 1)->nullable()->comment('Qualité du sommeil (0-10)');
            
            // Données de stress et bien-être
            $table->decimal('niveau_stress', 5, 2)->nullable()->comment('Niveau de stress (0-100)');
            $table->decimal('niveau_anxiete', 5, 2)->nullable()->comment('Niveau d\'anxiété (0-100)');
            $table->decimal('score_humeur', 3, 1)->nullable()->comment('Score d\'humeur (0-10)');
            $table->decimal('niveau_energie', 5, 2)->nullable()->comment('Niveau d\'énergie (0-100)');
            $table->decimal('score_concentration', 5, 2)->nullable()->comment('Score de concentration (0-100)');
            
            // Données d'activité
            $table->integer('nombre_pas')->nullable()->comment('Nombre de pas');
            $table->decimal('calories_brulées', 6, 2)->nullable()->comment('Calories brûlées');
            $table->decimal('minutes_actives', 5, 2)->nullable()->comment('Minutes d\'activité');
            $table->decimal('minutes_exercice', 5, 2)->nullable()->comment('Minutes d\'exercice');
            $table->integer('heures_debout')->nullable()->comment('Heures debout');
            $table->decimal('distance_marchee_km', 6, 2)->nullable()->comment('Distance marchée en km');
            
            // Données SDOH (Social Determinants of Health)
            $table->decimal('score_environnement_vie', 5, 2)->nullable()->comment('Score environnement de vie (0-100)');
            $table->decimal('score_soutien_social', 5, 2)->nullable()->comment('Score soutien social (0-100)');
            $table->decimal('score_acces_soins', 5, 2)->nullable()->comment('Score accès aux soins (0-100)');
            $table->decimal('score_situation_financiere', 5, 2)->nullable()->comment('Score situation financière (0-100)');
            $table->decimal('score_bien_etre_mental', 5, 2)->nullable()->comment('Score bien-être mental (0-100)');
            $table->decimal('score_sdoh_global', 5, 2)->nullable()->comment('Score SDOH global (0-100)');
            
            // Données des appareils connectés
            $table->json('appareils_connectes')->nullable()->comment('Liste des appareils connectés');
            $table->boolean('smartwatch_connecte')->default(false)->comment('Smartwatch connecté');
            $table->boolean('smartphone_connecte')->default(false)->comment('Smartphone connecté');
            $table->boolean('tracker_connecte')->default(false)->comment('Tracker connecté');
            $table->timestamp('derniere_synchronisation')->nullable()->comment('Dernière synchronisation');
            
            // Données de dopage
            $table->date('dernier_controle_dopage')->nullable()->comment('Date du dernier contrôle anti-dopage');
            $table->enum('resultat_controle_dopage', ['negatif', 'positif', 'en_attente'])->nullable();
            $table->date('prochain_controle_dopage')->nullable()->comment('Date du prochain contrôle');
            $table->json('historique_controles_dopage')->nullable()->comment('Historique des contrôles');
            
            // Données de performance avancées
            $table->json('evolution_performance')->nullable()->comment('Évolution des performances (JSON)');
            $table->json('statistiques_saison')->nullable()->comment('Statistiques de la saison (JSON)');
            $table->json('objectifs_performance')->nullable()->comment('Objectifs de performance (JSON)');
            $table->json('recommandations_sante')->nullable()->comment('Recommandations de santé (JSON)');
            
            // Métadonnées
            $table->json('donnees_supplementaires')->nullable()->comment('Données supplémentaires (JSON)');
            $table->timestamp('derniere_mise_a_jour_donnees')->nullable()->comment('Dernière mise à jour des données');
            $table->enum('qualite_donnees', ['excellent', 'good', 'fair', 'poor'])->default('good');
        });
    }

    public function down(): void
    {
        Schema::table('joueurs', function (Blueprint $table) {
            // Suppression des champs ajoutés
            $table->dropColumn([
                'pied_fort', 'langues_parlees', 'passeport', 'permis_conduire',
                'tirs_cadres', 'tirs_totaux', 'precision_tirs', 'passes_cles', 'centres_reussis',
                'dribbles_reussis', 'passes_longues', 'tacles_reussis', 'interceptions',
                'degaugements', 'fautes_commises', 'cartons_jaunes', 'cartons_rouges',
                'distance_parcourue_km', 'vitesse_maximale_kmh', 'vitesse_moyenne_kmh',
                'sprints', 'accelerations', 'decelerations', 'changements_direction', 'sautes',
                'frequence_cardiaque', 'tension_arterielle', 'temperature', 'saturation_o2',
                'niveau_hydratation', 'cortisol_stress', 'score_recuperation', 'fatigue_musculaire',
                'fatigue_centrale', 'temps_recuperation_heures', 'score_preparation',
                'duree_sommeil_heures', 'sommeil_profond_pourcentage', 'sommeil_rem_pourcentage',
                'efficacite_sommeil', 'qualite_sommeil', 'niveau_stress', 'niveau_anxiete',
                'score_humeur', 'niveau_energie', 'score_concentration', 'nombre_pas',
                'calories_brulées', 'minutes_actives', 'minutes_exercice', 'heures_debout',
                'distance_marchee_km', 'score_environnement_vie', 'score_soutien_social',
                'score_acces_soins', 'score_situation_financiere', 'score_bien_etre_mental',
                'score_sdoh_global', 'appareils_connectes', 'smartwatch_connecte',
                'smartphone_connecte', 'tracker_connecte', 'derniere_synchronisation',
                'dernier_controle_dopage', 'resultat_controle_dopage', 'prochain_controle_dopage',
                'historique_controles_dopage', 'evolution_performance', 'statistiques_saison',
                'objectifs_performance', 'recommandations_sante', 'donnees_supplementaires',
                'derniere_mise_a_jour_donnees', 'qualite_donnees'
            ]);
        });
    }
};








