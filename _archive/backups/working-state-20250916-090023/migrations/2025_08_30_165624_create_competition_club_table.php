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
        Schema::create('competition_club', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('competition_id');
            $table->unsignedBigInteger('club_id');
            $table->string('status')->default('registered'); // registered, qualified, eliminated, winner
            $table->integer('points')->default(0);
            $table->integer('goals_for')->default(0);
            $table->integer('goals_against')->default(0);
            $table->integer('goal_difference')->default(0);
            $table->integer('matches_played')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('draws')->default(0);
            $table->integer('losses')->default(0);
            $table->timestamp('registration_date')->nullable();
            $table->timestamps();

            // Clés étrangères
            $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');

            // Index unique pour éviter les doublons
            $table->unique(['competition_id', 'club_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_club');
    }
};
