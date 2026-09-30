<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cockpit "Statistiques avancées" : profil par dimension (radar).
     *
     * PerformanceScoreCalculator::calculate() calcule déjà un détail par
     * dimension (z-score, indicateurs, fiabilité) pour chaque joueur — il
     * n'était simplement pas conservé jusqu'à player_role_evaluations.
     * Cette colonne stocke ce détail UNIQUEMENT pour la ligne "famille
     * jouée" (role_fit_score = 0), sous forme JSON :
     *   { "<dimension_key>": { "z": float, "fiabilite": float (0-100) }, ... }
     * PerformanceScoreCalculator lui-même n'est pas modifié (engagement du
     * Livrable "moteur de calcul" respecté) : seul RoleFitEvaluator, qui
     * appelle déjà calculate(), transmet désormais ce détail en plus du
     * score agrégé qu'il transmettait déjà.
     */
    public function up(): void
    {
        Schema::table('player_role_evaluations', function (Blueprint $table) {
            $table->json('dimension_breakdown')->nullable()->after('role_fit_score');
        });
    }

    public function down(): void
    {
        Schema::table('player_role_evaluations', function (Blueprint $table) {
            $table->dropColumn('dimension_breakdown');
        });
    }
};
