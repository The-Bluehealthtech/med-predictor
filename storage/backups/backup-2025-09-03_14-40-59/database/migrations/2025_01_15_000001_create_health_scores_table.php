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
        Schema::create('health_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->integer('score')->comment('Health score from 0-100');
            $table->enum('trend', ['improving', 'stable', 'worsening'])->default('stable');
            $table->json('contributing_factors')->nullable()->comment('Factors that influenced the score');
            $table->json('metrics')->nullable()->comment('Detailed metrics used in calculation');
            $table->json('ai_analysis')->nullable()->comment('AI-generated insights');
            $table->date('calculated_date')->comment('Date for which score was calculated');
            $table->timestamps();

            // Indexes
            $table->index(['athlete_id', 'calculated_date']);
            $table->index(['calculated_date']);
            $table->index(['score']);
            $table->index(['trend']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_scores');
    }
}; 