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
        Schema::create('performance_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->string('type')->comment('Type of recommendation');
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'implemented', 'cancelled'])->default('pending');
            $table->text('implementation_notes')->nullable();
            $table->date('target_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->json('ai_analysis_data')->nullable();
            $table->timestamps();
            
            $table->index(['player_id', 'status']);
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_recommendations');
    }
}; 