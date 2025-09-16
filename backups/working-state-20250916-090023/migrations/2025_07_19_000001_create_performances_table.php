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
        Schema::create('performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->date('match_date');
            $table->integer('distance_covered')->comment('Distance covered in meters');
            $table->integer('sprint_count')->comment('Number of sprints');
            $table->decimal('max_speed', 5, 2)->comment('Maximum speed in km/h');
            $table->decimal('avg_speed', 5, 2)->comment('Average speed in km/h');
            $table->integer('passes_completed')->default(0);
            $table->integer('passes_attempted')->default(0);
            $table->integer('tackles_won')->default(0);
            $table->integer('tackles_attempted')->default(0);
            $table->integer('shots_on_target')->default(0);
            $table->integer('shots_total')->default(0);
            $table->integer('goals_scored')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('yellow_cards')->default(0);
            $table->integer('red_cards')->default(0);
            $table->integer('minutes_played')->default(0);
            $table->decimal('rating', 3, 1)->comment('Performance rating out of 10');
            $table->text('notes')->nullable();
            $table->json('additional_metrics')->nullable();
            $table->timestamps();
            
            $table->index(['player_id', 'match_date']);
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performances');
    }
}; 