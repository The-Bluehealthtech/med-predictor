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
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "2024/25 Season"
            $table->string('short_name'); // e.g., "2024/25"
            $table->date('start_date');
            $table->date('end_date');
            $table->date('registration_start_date');
            $table->date('registration_end_date');
            $table->enum('status', ['upcoming', 'active', 'completed', 'archived'])->default('upcoming');
            $table->boolean('is_current')->default(false);
            $table->text('description')->nullable();
            $table->json('settings')->nullable(); // Season-specific settings
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['status', 'is_current']);
            $table->index(['start_date', 'end_date']);
            $table->unique(['short_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
}; 