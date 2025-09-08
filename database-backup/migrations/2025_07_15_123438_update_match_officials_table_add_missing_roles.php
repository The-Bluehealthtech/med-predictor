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
        Schema::table('match_officials', function (Blueprint $table) {
            // Drop the existing enum constraint
            $table->dropColumn('role');
        });

        Schema::table('match_officials', function (Blueprint $table) {
            // Recreate the enum with all required roles
            $table->enum('role', [
                'main_referee',
                'assistant_referee_1', 
                'assistant_referee_2',
                'fourth_official',
                'var_referee',
                'var_assistant',
                'match_observer'
            ])->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_officials', function (Blueprint $table) {
            // Drop the updated enum constraint
            $table->dropColumn('role');
        });

        Schema::table('match_officials', function (Blueprint $table) {
            // Restore the original enum
            $table->enum('role', [
                'main_referee',
                'assistant_referee_1', 
                'assistant_referee_2',
                'fourth_official'
            ])->after('user_id');
        });
    }
};
