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
        Schema::create('postural_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Clinician
            $table->string('assessment_type')->default('routine'); // routine, injury, follow_up
            $table->string('view')->default('anterior'); // anterior, posterior, lateral
            $table->json('annotations')->nullable(); // Store all annotations
            $table->json('markers')->nullable(); // Store marker positions
            $table->json('angles')->nullable(); // Store angle measurements
            $table->text('clinical_notes')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('status')->default('active'); // active, archived
            $table->timestamp('assessment_date');
            $table->timestamps();
            
            $table->index(['player_id', 'assessment_date']);
            $table->index(['user_id', 'assessment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postural_assessments');
    }
}; 