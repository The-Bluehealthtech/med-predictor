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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'role')) {
                    $table->enum('role', ['doctor', 'secretary', 'admin', 'athlete'])->default('athlete')->after('email');
                }
                if (!Schema::hasColumn('users', 'fifa_connect_id')) {
                    $table->string('fifa_connect_id')->nullable()->after('role');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'role')) {
                    $table->dropColumn('role');
                }
                if (Schema::hasColumn('users', 'fifa_connect_id')) {
                    $table->dropColumn('fifa_connect_id');
                }
            });
        }
    }
}; 