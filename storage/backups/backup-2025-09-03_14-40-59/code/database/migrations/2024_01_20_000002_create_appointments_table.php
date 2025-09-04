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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('athlete_id');
            $table->string('fifa_connect_id'); // Référence directe au FIFA Connect ID
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('appointment_date');
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled'])->default('scheduled');
            $table->enum('type', ['consultation', 'examination', 'follow_up', 'emergency'])->default('consultation');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Pour stocker des données supplémentaires
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['athlete_id', 'appointment_date']);
            $table->index('fifa_connect_id');
            $table->index('status');
            
            // Contraintes de clés étrangères
            $table->foreign('athlete_id')->references('id')->on('athletes')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
}; 