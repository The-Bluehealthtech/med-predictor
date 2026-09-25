<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // PostgreSQL/MySQL were handled by the 2025-09-01 migration.
        // SQLite was omitted there, preventing local demo players without a FIFA ID.
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('players', function (Blueprint $table) {
                $table->string('fifa_connect_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Reinstating NOT NULL would discard valid players without a FIFA ID.
    }
};
