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
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('match_sheets', function (Blueprint $table) {
                $table->dropForeign(['match_id']);
                $table->foreign('match_id')->references('id')->on('game_matches')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('match_sheets', function (Blueprint $table) {
                $table->dropForeign(['match_id']);
                $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');
            });
        }
    }
};
