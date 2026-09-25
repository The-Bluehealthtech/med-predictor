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
        Schema::table('competitions', function (Blueprint $table) {
            if (!Schema::hasColumn('competitions', 'fifa_connect_id')) {
                $table->string('fifa_connect_id')->nullable()->after('association_id');
            }
            
            if (!Schema::hasIndex('competitions', 'competitions_fifa_connect_id_index')) {
                $table->index(['fifa_connect_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            if (Schema::hasIndex('competitions', 'competitions_fifa_connect_id_index')) {
                $table->dropIndex(['fifa_connect_id']);
            }
            
            if (Schema::hasColumn('competitions', 'fifa_connect_id')) {
                $table->dropColumn('fifa_connect_id');
            }
        });
    }
};
