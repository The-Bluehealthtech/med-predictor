<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            if (!Schema::hasColumn('competitions', 'fifa_connect_id')) {
                $table->string('fifa_connect_id')->nullable();
            }
            if (!Schema::hasColumn('competitions', 'require_federation_license')) {
                $table->boolean('require_federation_license')->default(false);
            }
            if (!Schema::hasColumn('competitions', 'fifa_sync_enabled')) {
                $table->boolean('fifa_sync_enabled')->default(false);
            }
            if (!Schema::hasColumn('competitions', 'fifa_sync_status')) {
                $table->string('fifa_sync_status')->default('pending');
            }
            if (!Schema::hasColumn('competitions', 'fifa_sync_date')) {
                $table->timestamp('fifa_sync_date')->nullable();
            }
            if (!Schema::hasColumn('competitions', 'fifa_last_error')) {
                $table->text('fifa_last_error')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Volontairement vide : ces colonnes peuvent provenir
        // de migrations antérieures du schéma consolidé.
    }
};
