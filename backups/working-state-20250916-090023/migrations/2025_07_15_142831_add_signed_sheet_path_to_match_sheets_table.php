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
        Schema::table('match_sheets', function (Blueprint $table) {
            $table->string('signed_sheet_path')->nullable()->after('user_action_log');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_sheets', function (Blueprint $table) {
            $table->dropColumn('signed_sheet_path');
        });
    }
};
