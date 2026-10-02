<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('player_detailed_stats', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->foreign('player_id')
                ->references('id')
                ->on('players')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('player_detailed_stats', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->foreign('player_id')
                ->references('id')
                ->on('joueurs')
                ->cascadeOnDelete();
        });
    }
};
