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
        Schema::create('pcmas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['bpma', 'cardio', 'dental', 'neurological', 'orthopedic']);
            $table->json('result_json');
            $table->enum('status', ['pending', 'completed', 'failed', 'cleared', 'not_cleared'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('assessor_id')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->json('fifa_compliance_data')->nullable();
            $table->timestamps();
            
            $table->index(['athlete_id']);
            $table->index(['type']);
            $table->index(['status']);
            $table->index(['completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pcmas');
    }
}; 