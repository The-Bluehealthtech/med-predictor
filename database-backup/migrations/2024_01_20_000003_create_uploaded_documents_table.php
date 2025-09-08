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
        Schema::create('uploaded_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('athlete_id');
            $table->string('fifa_connect_id'); // Référence directe au FIFA Connect ID
            $table->unsignedBigInteger('uploaded_by')->nullable(); // User qui a uploadé
            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->enum('document_type', ['medical_record', 'imaging', 'lab_result', 'prescription', 'certificate', 'other'])->default('other');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('ai_analysis')->nullable(); // Résultats de l'analyse IA
            $table->json('metadata')->nullable(); // Métadonnées du document
            $table->enum('status', ['pending', 'processed', 'analyzed', 'archived'])->default('pending');
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['athlete_id', 'document_type']);
            $table->index('fifa_connect_id');
            $table->index('status');
            $table->index('uploaded_by');
            
            // Contraintes de clés étrangères
            $table->foreign('athlete_id')->references('id')->on('athletes')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaded_documents');
    }
}; 