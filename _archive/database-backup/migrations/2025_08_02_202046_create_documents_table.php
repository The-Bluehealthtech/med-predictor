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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', [
                'medical_report',
                'lab_result',
                'radiology',
                'prescription',
                'consent_form',
                'insurance_form',
                'referral',
                'discharge_summary',
                'progress_note',
                'other'
            ])->default('other');
            $table->string('file_name');
            $table->string('file_path');
            $table->bigInteger('file_size')->default(0);
            $table->string('mime_type')->nullable();
            $table->text('description')->nullable();
            $table->json('analysis_result')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', [
                'pending',
                'analyzing',
                'analyzed',
                'error',
                'archived'
            ])->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['visit_id', 'document_type']);
            $table->index(['status']);
            $table->index(['uploaded_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
