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
        Schema::create('ai_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->string('prediction_type'); // performance, injury, market_value, etc.
            $table->json('input_data'); // Données d'entrée pour l'IA
            $table->json('prediction_result'); // Résultat de la prédiction
            $table->decimal('confidence_score', 5, 4); // Score de confiance (0.0000 à 1.0000)
            $table->decimal('accuracy_score', 5, 4); // Score de précision (0.0000 à 1.0000)
            $table->string('model_version'); // Version du modèle ML utilisé
            $table->decimal('processing_time', 8, 4); // Temps de traitement en secondes
            $table->string('cache_status')->default('miss'); // hit, miss, expired
            $table->json('metadata')->nullable(); // Métadonnées supplémentaires
            $table->timestamp('expires_at')->nullable(); // Expiration de la prédiction
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['player_id', 'prediction_type']);
            $table->index(['prediction_type', 'created_at']);
            $table->index('expires_at');
            $table->index('confidence_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_predictions');
    }
};
