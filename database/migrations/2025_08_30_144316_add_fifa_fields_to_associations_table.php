<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            if (!Schema::hasColumn('associations', 'fifa_sync_status')) {
                $table->enum('fifa_sync_status', ['pending', 'synced', 'failed'])->default('pending');
            }
            if (!Schema::hasColumn('associations', 'fifa_sync_date')) {
                $table->timestamp('fifa_sync_date')->nullable();
            }
            if (!Schema::hasColumn('associations', 'fifa_last_error')) {
                $table->text('fifa_last_error')->nullable();
            }
            if (!Schema::hasColumn('associations', 'founded_year')) {
                $table->integer('founded_year')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Volontairement vide : ces colonnes peuvent provenir
        // de migrations antérieures du schéma consolidé.
    }
};
