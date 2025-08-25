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
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->enum('type', ['technical', 'physical', 'tactical', 'recovery', 'mental']);
            $table->string('location')->default('Centre d\'entraînement');
            $table->string('coach')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};
