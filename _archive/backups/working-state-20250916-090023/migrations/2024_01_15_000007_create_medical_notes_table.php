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
        Schema::create('medical_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->json('note_json');
            $table->boolean('generated_by_ai')->default(false);
            $table->foreignId('approved_by_physician_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('signed_at')->nullable();
            $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected'])->default('draft');
            $table->string('note_type')->default('general');
            $table->json('ai_metadata')->nullable();
            $table->json('fifa_compliance_data')->nullable();
            $table->timestamps();
            
            $table->index(['athlete_id']);
            $table->index(['approved_by_physician_id']);
            $table->index(['status']);
            $table->index(['generated_by_ai']);
            $table->index(['signed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_notes');
    }
}; 