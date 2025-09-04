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
        Schema::create('player_license_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('club_id')->nullable();
            $table->unsignedBigInteger('association_id')->nullable();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('type_licence'); // Pro, Amateur, Jeunes
            $table->string('source_donnee'); // FIT, API-FIFA, API-Nationale
            $table->string('license_number')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Données additionnelles
            $table->timestamps();

            // Index pour les performances
            $table->index(['player_id', 'date_debut']);
            $table->index(['club_id']);
            $table->index(['association_id']);
            
            // Clés étrangères
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('set null');
            $table->foreign('association_id')->references('id')->on('associations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_license_histories');
    }
};
