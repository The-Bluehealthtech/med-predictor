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
        Schema::table('match_events', function (Blueprint $table) {
            $table->foreignId('match_id')->nullable()->after('match_sheet_id')->constrained('game_matches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_events', function (Blueprint $table) {
            $table->dropForeign(['match_id']);
            $table->dropColumn('match_id');
        });
    }
};
