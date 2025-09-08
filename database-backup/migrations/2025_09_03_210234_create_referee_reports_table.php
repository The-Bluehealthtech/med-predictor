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
        Schema::create('referee_reports', function (Blueprint $table) {
            $table->id();
            
            // Références
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade');
            $table->foreignId('referee_id')->constrained('users')->onDelete('cascade');
            
            // Informations générales
            $table->string('competition_name');
            $table->string('home_team');
            $table->string('away_team');
            $table->date('match_date');
            $table->time('kickoff_time');
            $table->string('venue');
            $table->integer('attendance')->nullable();
            
            // Conditions
            $table->string('weather')->nullable();
            $table->string('pitch_condition')->nullable();
            
            // Officiels
            $table->string('main_referee');
            $table->string('assistant_referee_1');
            $table->string('assistant_referee_2');
            $table->string('fourth_official');
            $table->string('var_referee')->nullable();
            $table->string('avar_referee')->nullable();
            
            // Résultat
            $table->string('final_score');
            $table->string('half_time_score')->nullable();
            $table->integer('extra_time_minutes')->default(0);
            $table->boolean('penalty_shootout')->default(false);
            $table->string('penalty_shootout_score')->nullable();
            
            // Événements (JSON)
            $table->json('goals')->nullable();
            $table->json('yellow_cards')->nullable();
            $table->json('red_cards')->nullable();
            $table->json('substitutions')->nullable();
            $table->json('injuries')->nullable();
            
            // Discipline & Santé
            $table->text('disciplinary_incidents')->nullable();
            $table->text('crowd_incidents')->nullable();
            $table->text('safety_issues')->nullable();
            
            // Observations
            $table->text('general_comments');
            $table->text('match_quality_assessment')->nullable();
            $table->integer('match_rating')->nullable(); // 1-10
            
            // Validation
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->string('electronic_signature')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referee_reports');
    }
};
