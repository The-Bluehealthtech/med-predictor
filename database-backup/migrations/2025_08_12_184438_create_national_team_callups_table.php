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
        Schema::create('national_team_callups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->date('callup_date');
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->enum('type', ['national', 'training', 'friendly', 'qualifier', 'tournament']);
            $table->string('opponents')->nullable(); // Équipes adverses
            $table->string('venue')->nullable(); // Lieu
            $table->time('meeting_time')->nullable(); // Heure de RDV
            $table->boolean('is_confirmed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('national_team_callups');
    }
};
