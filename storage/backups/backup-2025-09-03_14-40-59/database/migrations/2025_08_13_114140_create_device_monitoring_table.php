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
        Schema::create('device_monitoring', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->string('device_type'); // smartwatch, gps_tracker, heart_monitor, etc.
            $table->string('device_name');
            $table->string('device_model');
            $table->string('serial_number')->nullable();
            $table->string('status'); // active, inactive, maintenance, broken
            $table->date('activation_date');
            $table->date('last_sync')->nullable();
            $table->date('next_maintenance')->nullable();
            $table->json('current_data')->nullable(); // Current sensor data
            $table->json('settings')->nullable(); // Device settings
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_monitoring');
    }
};
