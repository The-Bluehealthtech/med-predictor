<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->string('fifa_connect_id')->nullable()->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->dropColumn('fifa_connect_id');
        });
    }
}; 