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
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->string('fifa_id')->unique();
            $table->string('name');
            $table->date('dob');
            $table->string('nationality', 3); // ISO 3166-1 alpha-3
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->string('position')->nullable();
            $table->string('jersey_number')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->string('blood_type')->nullable();
            $table->json('emergency_contact')->nullable();
            $table->json('medical_history')->nullable();
            $table->json('allergies')->nullable();
            $table->json('medications')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index(['fifa_id']);
            $table->index(['team_id']);
            $table->index(['nationality']);
            $table->index(['active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
}; 