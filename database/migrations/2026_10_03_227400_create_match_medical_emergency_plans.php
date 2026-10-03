<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('match_medical_emergency_plans')) {
            return;
        }

        Schema::create('match_medical_emergency_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->unique()->constrained('matches')->cascadeOnDelete();
            $table->string('protocol_name')->default('FIFA Emergency Care Protocols');
            $table->string('protocol_version')->default('v3 - March 2025');
            $table->string('status', 24)->default('draft');
            $table->string('stadium')->nullable();
            $table->string('nearest_hospital')->nullable();
            $table->string('nearest_hospital_phone', 64)->nullable();
            $table->string('ambulance_contact', 128)->nullable();
            $table->foreignId('team_leader_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('team_leader_name')->nullable();
            $table->string('team_leader_phone', 64)->nullable();
            $table->json('role_assignments')->nullable();
            $table->json('equipment_checklist')->nullable();
            $table->json('timeline_checklist')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_medical_emergency_plans');
    }
};
