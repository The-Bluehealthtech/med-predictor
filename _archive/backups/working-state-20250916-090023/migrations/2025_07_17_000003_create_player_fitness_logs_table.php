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
        Schema::create('player_fitness_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->date('log_date');
            $table->string('session_type')->nullable()->comment('training, match, recovery, etc.');
            $table->integer('duration_minutes')->nullable()->comment('Session duration in minutes');
            $table->decimal('distance_km', 5, 2)->nullable()->comment('Distance covered in km');
            $table->integer('calories_burned')->nullable();
            $table->decimal('max_heart_rate', 5, 1)->nullable();
            $table->decimal('avg_heart_rate', 5, 1)->nullable();
            $table->decimal('max_speed_kmh', 4, 1)->nullable()->comment('Maximum speed in km/h');
            $table->decimal('avg_speed_kmh', 4, 1)->nullable()->comment('Average speed in km/h');
            $table->integer('sprints_count')->nullable()->comment('Number of sprints');
            $table->decimal('fatigue_level', 3, 1)->nullable()->comment('Subjective fatigue (1-10)');
            $table->decimal('energy_level', 3, 1)->nullable()->comment('Subjective energy (1-10)');
            $table->text('notes')->nullable()->comment('Player notes about the session');
            $table->json('metrics')->nullable()->comment('Additional fitness metrics');
            $table->boolean('is_completed')->default(true);
            $table->timestamps();
            
            $table->index(['player_id', 'log_date']);
            $table->index(['player_id', 'session_type']);
            $table->unique(['player_id', 'log_date', 'session_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_fitness_logs');
    }
}; 