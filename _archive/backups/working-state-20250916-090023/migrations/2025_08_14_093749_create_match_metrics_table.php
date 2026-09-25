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
        Schema::create('match_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_performance_id')->constrained('match_performances')->onDelete('cascade');
            
            // Métriques d'attaque
            $table->integer('shots_on_target')->default(0);
            $table->integer('total_shots')->default(0);
            $table->decimal('shot_accuracy', 5, 2)->default(0);
            $table->integer('key_passes')->default(0);
            $table->integer('successful_crosses')->default(0);
            $table->integer('successful_dribbles')->default(0);
            
            // Métriques physiques
            $table->decimal('distance', 8, 2)->default(0); // km
            $table->decimal('max_speed', 5, 2)->default(0); // km/h
            $table->decimal('avg_speed', 5, 2)->default(0); // km/h
            $table->integer('sprints')->default(0);
            $table->integer('accelerations')->default(0);
            $table->integer('decelerations')->default(0);
            $table->integer('direction_changes')->default(0);
            $table->integer('jumps')->default(0);
            
            // Métriques techniques
            $table->decimal('pass_accuracy', 5, 2)->default(0); // %
            $table->integer('long_passes')->default(0);
            $table->integer('crosses')->default(0);
            $table->integer('tackles')->default(0);
            $table->integer('interceptions')->default(0);
            $table->integer('clearances')->default(0);
            
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index('match_performance_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_metrics');
    }
};
