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
            $table->foreignId('season_id')->nullable()->after('association_id')->constrained('seasons')->onDelete('set null');
            $table->index(['season_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropForeign(['season_id']);
            $table->dropIndex(['season_id', 'status']);
            $table->dropColumn('season_id');
        });
    }
}; 