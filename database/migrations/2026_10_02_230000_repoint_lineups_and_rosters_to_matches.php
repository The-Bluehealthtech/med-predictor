<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Laravel 10 cannot drop foreign keys on SQLite without rebuilding
        // the whole table. Test/local SQLite keeps its existing constraint;
        // the canonical Render database is PostgreSQL.
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('lineups', function (Blueprint $table) {
            $table->dropForeign(['match_id']);
            $table->foreign('match_id')
                ->references('id')
                ->on('matches')
                ->nullOnDelete();
        });

        Schema::table('match_rosters', function (Blueprint $table) {
            $table->dropForeign(['match_id']);
            $table->foreign('match_id')
                ->references('id')
                ->on('matches')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('lineups', function (Blueprint $table) {
            $table->dropForeign(['match_id']);
            $table->foreign('match_id')
                ->references('id')
                ->on('game_matches')
                ->nullOnDelete();
        });

        Schema::table('match_rosters', function (Blueprint $table) {
            $table->dropForeign(['match_id']);
            $table->foreign('match_id')
                ->references('id')
                ->on('game_matches')
                ->cascadeOnDelete();
        });
    }
};
