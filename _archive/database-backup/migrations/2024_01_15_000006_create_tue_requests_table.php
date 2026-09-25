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
        Schema::create('tue_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->string('medication');
            $table->text('reason');
            $table->foreignId('physician_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->date('request_date');
            $table->date('approved_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('supporting_documents')->nullable();
            $table->json('fifa_tue_data')->nullable();
            $table->timestamps();
            
            $table->index(['athlete_id']);
            $table->index(['physician_id']);
            $table->index(['status']);
            $table->index(['request_date']);
            $table->index(['approved_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tue_requests');
    }
}; 