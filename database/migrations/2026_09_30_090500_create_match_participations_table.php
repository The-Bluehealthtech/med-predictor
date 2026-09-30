<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Livrable 1, §1 : "qui a joué où", séparé des statistiques produites
     * (player_match_detailed_stats) pour garder des jointures et des
     * contrôles de cohérence simples au Livrable 3.
     * detailed_position référence position_catalog.code en clé logique
     * (pas de contrainte FK stricte : la colonne reste un simple string pour
     * accepter, si besoin, un code non encore catalogué sans bloquer un import).
     */
    public function up(): void
    {
        Schema::create('match_participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('detailed_position'); // référentiel logique : position_catalog.code
            $table->boolean('is_starter');
            $table->unsignedSmallInteger('minute_in')->nullable();
            $table->unsignedSmallInteger('minute_out')->nullable();
            $table->unsignedSmallInteger('jersey_number')->nullable();
            $table->boolean('is_demo')->default(false);
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->timestamps();

            $table->unique(['match_id', 'player_id']);
            $table->index(['match_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_participations');
    }
};
