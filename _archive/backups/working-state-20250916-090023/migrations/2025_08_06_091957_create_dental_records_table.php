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
        Schema::create('dental_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Médecin qui a créé l'enregistrement
            $table->json('dental_data'); // Stockage des annotations dentaires au format JSON
            $table->text('notes')->nullable(); // Notes générales sur l'examen dentaire
            $table->enum('status', ['draft', 'completed', 'archived'])->default('draft');
            $table->timestamp('examined_at')->nullable(); // Date de l'examen
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['patient_id', 'status']);
            $table->index(['user_id']);
            $table->index(['examined_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dental_records');
    }
};
