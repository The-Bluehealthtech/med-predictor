<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CORRECTIF proposé lors de la validation du moteur de calcul (voir
     * docs/role-evaluation/05-proposition-moteur-calcul.md, question ouverte
     * n°3, validée par Izhar : "migration corrective").
     *
     * Comble deux lacunes de champs identifiées en comparant
     * config/player_performance_score.php (moteur de score existant, phase
     * 1, app/Services/PerformanceScoreCalculator.php) aux colonnes de
     * player_match_detailed_stats ajoutées au Livrable 1 :
     *
     * 1) `penalties_conceded` : l'indicateur de discipline du moteur existant
     *    l'attend (cfg['indicators']['penalties_conceded']). Aucune colonne
     *    ne l'enregistrait nulle part dans player_match_detailed_stats (ni
     *    les 96 colonnes d'origine, ni les 22 ajoutées au Livrable 1).
     *
     * 2) Sorties de gardien : le moteur distingue `high_claim_rate` (sorties
     *    aériennes, tentées vs réussies) et `sweeper_rate` (sorties au sol /
     *    libéro, tentées vs réussies). Le Livrable 1 avait fusionné ces deux
     *    notions dans une seule colonne `gk_claims_exits` ("option A",
     *    volontairement minimale à l'époque, avant que ce besoin ne soit
     *    identifié). Cette migration AJOUTE les quatre colonnes détaillées
     *    nécessaires ; elle NE SUPPRIME NI NE RENOMME `gk_claims_exits`,
     *    conformément à la règle du mandat de ne modifier/supprimer aucune
     *    donnée existante sans accord écrit — même s'il s'agit d'une colonne
     *    que j'ai moi-même ajoutée au Livrable 1. Elle devient simplement
     *    inutilisée par le nouveau moteur ; sa suppression éventuelle
     *    attendra un accord explicite séparé.
     *
     * Toutes les nouvelles colonnes sont nullable, comme les autres colonnes
     * ajoutées au Livrable 1 : "Données non disponibles" doit rester NULL,
     * jamais 0 (règle du mandat, Livrable 2).
     *
     * NON EXÉCUTÉE : comme toute migration de ce mandat, en attente de
     * `php artisan migrate` après votre accord écrit.
     */
    public function up(): void
    {
        Schema::table('player_match_detailed_stats', function (Blueprint $table) {
            $table->unsignedSmallInteger('penalties_conceded')->nullable();

            $table->unsignedSmallInteger('gk_high_claims_attempted')->nullable();
            $table->unsignedSmallInteger('gk_high_claims_completed')->nullable();
            $table->unsignedSmallInteger('gk_sweeper_actions_attempted')->nullable();
            $table->unsignedSmallInteger('gk_sweeper_actions_completed')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('player_match_detailed_stats', function (Blueprint $table) {
            $table->dropColumn([
                'penalties_conceded',
                'gk_high_claims_attempted',
                'gk_high_claims_completed',
                'gk_sweeper_actions_attempted',
                'gk_sweeper_actions_completed',
            ]);
        });
    }
};
