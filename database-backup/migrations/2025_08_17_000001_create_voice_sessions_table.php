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
        Schema::create('voice_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('player_name');
            $table->string('current_field')->default('poste');
            $table->json('session_data');
            $table->enum('language', ['fr', 'en', 'ar'])->default('fr');
            $table->enum('status', ['active', 'completed', 'expired', 'error'])->default('active');
            $table->integer('error_count')->default(0);
            $table->boolean('fallback_requested')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Index pour les performances
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['language', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voice_sessions');
    }
};
