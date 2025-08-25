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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('level', ['national', 'club', 'academy', 'regional'])->default('club');
            $table->string('federation_id')->nullable();
            $table->string('fifa_team_id')->nullable();
            $table->timestamps();
            
            $table->index(['federation_id']);
            $table->index(['fifa_team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
}; 