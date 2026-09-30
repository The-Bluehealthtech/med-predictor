<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Livrable 1, §6 : résultats calculés, jamais écrits directement par un
     * import (pas d'import_batch_id direct : ce sont des données dérivées).
     * source_import_batch_id/source_demo_batch_id tracent de quel(s) lot(s)
     * les données d'entrée provenaient, pour piloter le bandeau "Données de
     * démonstration" côté interface même sur un résultat calculé.
     * match_id nullable + period_start/period_end permettent une évaluation
     * agrégée sur une fenêtre de matchs, en plus d'une évaluation par match.
     */
    public function up(): void
    {
        Schema::create('player_role_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('match_id')->nullable()->constrained('matches')->cascadeOnDelete();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('position_family_evaluated'); // référentiel logique : position_catalog.family
            $table->foreignId('role_config_version_id')->constrained('role_config_versions');
            $table->string('model_version');
            $table->decimal('score', 6, 2);
            $table->decimal('reliability', 5, 4)->nullable();
            $table->decimal('interval_low', 6, 2)->nullable();
            $table->decimal('interval_high', 6, 2)->nullable();
            $table->decimal('role_fit_score', 6, 2)->nullable();
            $table->boolean('is_demo')->default(false);
            $table->foreignId('source_import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->foreignId('source_demo_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->timestamp('computed_at');
            $table->timestamps();

            $table->index(['player_id', 'position_family_evaluated']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_role_evaluations');
    }
};
