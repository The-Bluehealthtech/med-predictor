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
        Schema::table('player_licenses', function (Blueprint $table) {
            if (!Schema::hasColumn('player_licenses', 'issued_date')) {
                $table->date('issued_date')->nullable()->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_licenses', function (Blueprint $table) {
            if (Schema::hasColumn('player_licenses', 'issued_date')) {
                $table->dropColumn('issued_date');
            }
        });
    }
};
