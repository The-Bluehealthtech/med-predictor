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
        Schema::table('match_rosters', function (Blueprint $table) {
            $table->foreignId('player_id')->nullable()->constrained('players')->onDelete('cascade');
            $table->boolean('is_starter')->default(false);
            $table->string('position')->nullable();
            $table->integer('jersey_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_rosters', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->dropColumn(['player_id', 'is_starter', 'position', 'jersey_number']);
        });
    }
};
