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
        Schema::create('match_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->enum('result', ['W', 'D', 'L']); // Win, Draw, Loss
            $table->date('match_date');
            $table->string('opponent')->nullable();
            $table->string('competition')->nullable();
            $table->string('venue')->nullable();
            $table->integer('goals_scored')->default(0);
            $table->integer('assists')->default(0);
            $table->decimal('rating', 3, 1)->nullable(); // Rating sur 10
            $table->integer('minutes_played')->default(90);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_performances');
    }
};
