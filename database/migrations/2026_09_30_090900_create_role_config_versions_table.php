<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Livrable 1, §5 : une version de configuration de poids est immuable une
     * fois publiée. Aucune donnée n'est insérée par cette migration : les
     * poids réels attendront de vraies données, conformément à la règle du
     * mandat ("les données de démonstration ne servent jamais à régler des
     * poids, des seuils ou des paramètres de modèle").
     */
    public function up(): void
    {
        Schema::create('role_config_versions', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft | published | archived
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_config_versions');
    }
};
