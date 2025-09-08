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
        Schema::table('account_requests', function (Blueprint $table) {
            $table->string('fifa_connect_type')->nullable()->after('football_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_requests', function (Blueprint $table) {
            $table->dropColumn('fifa_connect_type');
        });
    }
};
