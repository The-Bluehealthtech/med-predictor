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
        Schema::create('medical_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('appointment_type'); // 'onsite', 'telemedicine'
            $table->string('status')->default('pending'); // 'pending', 'confirmed', 'completed', 'cancelled'
            $table->dateTime('appointment_date');
            $table->integer('duration_minutes')->default(30);
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->string('video_meeting_url')->nullable(); // URL pour la session vidéo
            $table->string('video_meeting_id')->nullable(); // ID de la réunion vidéo
            $table->string('video_meeting_password')->nullable(); // Mot de passe de la réunion
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->json('medical_data')->nullable(); // données médicales
            $table->timestamps();
            
            $table->index(['patient_id', 'appointment_date']);
            $table->index(['doctor_id', 'appointment_date']);
            $table->index(['status', 'appointment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_appointments');
    }
};
