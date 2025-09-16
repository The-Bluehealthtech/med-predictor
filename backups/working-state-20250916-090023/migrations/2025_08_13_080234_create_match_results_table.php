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
        Schema::create('match_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('club_id');
            $table->string('opponent');
            $table->enum('result', ['W', 'D', 'L']); // W = Win, D = Draw, L = Loss
            $table->date('match_date');
            $table->string('competition');
            $table->integer('goals_scored')->default(0);
            $table->integer('assists')->default(0);
            $table->decimal('rating', 3, 1)->default(0.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_results');
    }
};
