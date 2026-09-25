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
        Schema::create('risk_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['sca', 'injury', 'concussion', 'cardiac', 'other'])->default('other');
            $table->string('source'); // e.g., 'AI_Agent_ECG', 'AI_Agent_Injury'
            $table->decimal('score', 3, 2); // 0.00 to 1.00
            $table->text('message');
            $table->boolean('resolved')->default(false);
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->json('ai_metadata')->nullable();
            $table->json('recommendations')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('acknowledged_at')->nullable();
            $table->json('fifa_alert_data')->nullable();
            $table->timestamps();
            
            $table->index(['athlete_id']);
            $table->index(['type']);
            $table->index(['source']);
            $table->index(['resolved']);
            $table->index(['priority']);
            $table->index(['score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_alerts');
    }
}; 