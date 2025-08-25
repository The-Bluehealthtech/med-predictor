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
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Secrétaire qui a créé le RDV
            $table->dateTime('appointment_date');
            $table->integer('duration_minutes')->default(30); // Durée en minutes
            $table->enum('appointment_type', [
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
                'Confirmé',
                'Enregistré',
                'En cours',
                'Terminé',
                'Annulé',
                'No-show'
            ])->default('Planifié');
            $table->text('reason')->nullable(); // Motif du rendez-vous
            $table->text('notes')->nullable(); // Notes administratives
            $table->json('reminder_settings')->nullable(); // Paramètres de rappel
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['appointment_date', 'status']);
            $table->index(['athlete_id', 'appointment_date']);
            $table->index(['doctor_id', 'appointment_date']);
            $table->index(['created_by', 'appointment_date']);
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
