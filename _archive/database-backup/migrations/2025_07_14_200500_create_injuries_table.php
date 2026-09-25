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
        Schema::create('injuries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('type');
            $table->string('body_zone');
            $table->enum('severity', ['mild', 'moderate', 'severe', 'critical'])->default('mild');
            $table->text('description');
            $table->enum('status', ['open', 'resolved', 'recurring'])->default('open');
            $table->integer('estimated_recovery_days')->nullable();
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->foreignId('diagnosed_by')->constrained('users')->onDelete('cascade');
            $table->json('treatment_plan')->nullable();
            $table->json('rehabilitation_progress')->nullable();
            $table->json('fifa_injury_data')->nullable();
            $table->timestamps();
            
            $table->index(['athlete_id']);
            $table->index(['date']);
            $table->index(['status']);
            $table->index(['severity']);
            $table->index(['body_zone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('injuries');
    }
}; 