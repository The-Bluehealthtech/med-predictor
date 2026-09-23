<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_club', function (Blueprint $table) {
            $table->id();

            $table->foreignId('competition_id')
                ;

            $table->foreignId('club_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('status')->default('registered');

            $table->integer('points')->default(0);
            $table->integer('goals_for')->default(0);
            $table->integer('goals_against')->default(0);
            $table->integer('goal_difference')->default(0);

            $table->integer('matches_played')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('draws')->default(0);
            $table->integer('losses')->default(0);

            $table->dateTime('registration_date')->nullable();

            $table->timestamps();

            $table->unique(['competition_id', 'club_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_club');
    }
};
