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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['first_team', 'reserve', 'youth', 'academy'])->default('first_team');
            $table->string('formation')->nullable();
            $table->string('tactical_style')->nullable();
            $table->text('playing_philosophy')->nullable();
            $table->string('coach_name')->nullable();
            $table->string('assistant_coach')->nullable();
            $table->string('fitness_coach')->nullable();
            $table->string('goalkeeper_coach')->nullable();
            $table->string('scout')->nullable();
            $table->string('medical_staff')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('season')->nullable();
            $table->string('competition_level')->nullable();
            $table->decimal('budget_allocation', 15, 2)->nullable();
            $table->string('training_facility')->nullable();
            $table->string('home_ground')->nullable();
            $table->string('logo_url')->nullable();
            $table->foreignId('captain_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('association_id')->nullable()->constrained('associations')->onDelete('set null');
            $table->integer('founded_year')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('colors')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
