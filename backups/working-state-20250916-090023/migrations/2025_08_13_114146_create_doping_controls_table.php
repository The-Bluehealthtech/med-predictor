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
        Schema::create('doping_controls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->string('control_type'); // random, post_match, targeted, routine
            $table->string('location');
            $table->date('control_date');
            $table->time('control_time')->nullable();
            $table->string('result'); // negative, positive, pending
            $table->text('notes')->nullable();
            $table->string('control_authority'); // UEFA, FIFA, national, club
            $table->string('sample_id')->nullable();
            $table->date('next_control')->nullable();
            $table->json('substances_tested')->nullable(); // Array of tested substances
            $table->timestamps();
            
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doping_controls');
    }
};
