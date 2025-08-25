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
        Schema::create('player_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->string('document_type'); // medical_certificate, fitness_report, contract, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type'); // pdf, jpg, png, doc, etc.
            $table->integer('file_size')->comment('File size in bytes');
            $table->string('mime_type');
            $table->boolean('is_private')->default(true)->comment('Whether document is private to player');
            $table->date('expiry_date')->nullable()->comment('Document expiry date if applicable');
            $table->enum('status', ['active', 'expired', 'archived'])->default('active');
            $table->json('metadata')->nullable()->comment('Additional document metadata');
            $table->timestamps();
            
            $table->index(['player_id', 'document_type']);
            $table->index(['player_id', 'status']);
            $table->index(['expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_documents');
    }
}; 