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
        Schema::create('dental_annotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('health_record_id');
            $table->string('tooth_id'); // FDI notation (11, 12, 13, etc.)
            $table->integer('position_x')->nullable(); // Position X de la zone
            $table->integer('position_y')->nullable(); // Position Y de la zone
            $table->string('status')->default('normal'); // normal, selected, fixed, etc.
            $table->text('notes')->nullable(); // Notes libres pour la dent
            $table->json('metadata')->nullable(); // Données supplémentaires (couleur, type, etc.)
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['health_record_id', 'tooth_id']);
            $table->foreign('health_record_id')->references('id')->on('health_records')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dental_annotations');
    }
};
