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
        Schema::create('lineup_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lineup_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->boolean('is_substitute')->default(false);
            $table->integer('position_order')->default(0);
            $table->string('assigned_position')->nullable();
            $table->text('tactical_instructions')->nullable();
            $table->string('fitness_status')->default('fit');
            $table->decimal('expected_performance', 3, 1)->nullable();
            $table->timestamps();

            $table->unique(['lineup_id', 'player_id']);
            $table->index(['lineup_id', 'is_substitute']);
            $table->index(['lineup_id', 'position_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lineup_players');
    }
};
