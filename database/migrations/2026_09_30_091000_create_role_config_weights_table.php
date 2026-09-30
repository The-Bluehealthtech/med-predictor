<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Livrable 1, §5 : poids par famille de poste et dimension, rattachés à
     * une version immuable. position_family référence position_catalog.family
     * en clé logique (string, pas de FK stricte : plusieurs codes partagent
     * la même famille, ce n'est pas la clé primaire de position_catalog).
     */
    public function up(): void
    {
        Schema::create('role_config_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_config_version_id')->constrained('role_config_versions')->cascadeOnDelete();
            $table->string('position_family'); // référentiel logique : position_catalog.family
            $table->string('dimension_key'); // ex. passing, duels, defending, chance_creation, gk_shot_stopping
            $table->decimal('weight', 6, 4);
            $table->timestamps();

            $table->unique(['role_config_version_id', 'position_family', 'dimension_key'], 'role_config_weights_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_config_weights');
    }
};
