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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->dateTime('visit_date');
            $table->enum('visit_type', [
                'consultation',
                'emergency',
                'follow_up',
                'pre_season',
                'post_match',
                'rehabilitation',
                'routine_checkup',
                'injury_assessment',
                'cardiac_evaluation',
                'concussion_assessment'
            ]);
            $table->enum('status', [
                'Planifié',
                'Enregistré',
                'En cours',
                'Terminé',
                'Annulé'
            ])->default('Planifié');
            $table->text('notes')->nullable();
            $table->json('administrative_data')->nullable(); // Données administratives (facturation, consentements, etc.)
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['visit_date', 'status']);
            $table->index(['athlete_id', 'visit_date']);
            $table->index(['doctor_id', 'visit_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
