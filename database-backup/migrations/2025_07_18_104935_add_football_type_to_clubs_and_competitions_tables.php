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
        Schema::table('clubs', function (Blueprint $table) {
            $table->string('football_type')->default('11aside')->after('status');
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->string('football_type')->default('11aside')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn('football_type');
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('football_type');
        });
    }
};
