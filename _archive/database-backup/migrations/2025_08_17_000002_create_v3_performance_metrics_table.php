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
        Schema::create('v3_performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->string('metric_type'); // Type de métrique (speed, endurance, etc.)
            $table->decimal('value', 10, 4); // Valeur de la métrique
            $table->string('unit'); // Unité de mesure (km/h, %, etc.)
            $table->string('category'); // Catégorie (match_performance, training, etc.)
            $table->string('subcategory')->nullable(); // Sous-catégorie
            $table->json('context')->nullable(); // Contexte de la mesure
            $table->string('source')->default('manual'); // Source de la donnée
            $table->decimal('confidence', 5, 4)->default(1.0000); // Niveau de confiance
            $table->timestamp('timestamp'); // Horodatage de la mesure
            $table->json('metadata')->nullable(); // Métadonnées supplémentaires
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['player_id', 'metric_type']);
            $table->index(['metric_type', 'category']);
            $table->index('timestamp');
            $table->index(['player_id', 'timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v3_performance_metrics');
    }
};
