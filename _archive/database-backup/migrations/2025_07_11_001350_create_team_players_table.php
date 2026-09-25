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
        Schema::create('team_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['starter', 'substitute', 'reserve', 'loan'])->default('substitute');
            $table->integer('squad_number')->nullable();
            $table->date('joined_date');
            $table->date('contract_end_date')->nullable();
            $table->string('position_preference')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'injured', 'suspended', 'loaned_out', 'retired'])->default('active');
            $table->timestamps();

            $table->unique(['team_id', 'player_id']);
            $table->unique(['team_id', 'squad_number']);
            $table->index(['team_id', 'role']);
            $table->index(['team_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_players');
    }
};
