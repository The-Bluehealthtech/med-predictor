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
        Schema::create('transfer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained('transfers')->onDelete('cascade');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Informations du document
            $table->string('document_type'); // passport, contract, medical_certificate, etc.
            $table->string('document_name');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->integer('file_size'); // en bytes
            
            // Statut de validation
            $table->enum('validation_status', [
                'pending', 'approved', 'rejected', 'expired'
            ])->default('pending');
            $table->text('validation_notes')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            
            // Informations FIFA
            $table->string('fifa_document_id')->nullable();
            $table->json('fifa_metadata')->nullable();
            
            // Métadonnées
            $table->timestamps();
            
            // Index
            $table->index(['transfer_id', 'document_type']);
            $table->index(['validation_status']);
            $table->index(['uploaded_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_documents');
    }
};
