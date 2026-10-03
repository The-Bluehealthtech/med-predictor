<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_medical_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->foreignId('health_record_id')->nullable()->constrained('health_records')->nullOnDelete();
            $table->unsignedBigInteger('injury_id')->nullable();
            $table->string('incident_type', 32);
            $table->unsignedSmallInteger('match_minute')->nullable();
            $table->string('mechanism', 255)->nullable();
            $table->boolean('contact')->nullable();
            $table->json('abcde_assessment')->nullable();
            $table->boolean('loss_of_consciousness')->default(false);
            $table->boolean('aed_used')->default(false);
            $table->boolean('oxygen_used')->default(false);
            $table->boolean('evacuated')->default(false);
            $table->string('evacuation_destination')->nullable();
            $table->foreignId('doctor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('doctor_name')->nullable();
            $table->text('initial_diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['match_id', 'match_minute']);
            $table->index(['player_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_medical_incidents');
    }
};
