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
            $table->foreignId('association_id')->nullable()->after('id')->constrained('associations')->onDelete('set null');
            $table->index('association_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropForeign(['association_id']);
            $table->dropIndex(['association_id']);
            $table->dropColumn('association_id');
        });
    }
};
