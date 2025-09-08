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
        Schema::table('voice_sessions', function (Blueprint $table) {
            $table->string('dialogflow_session')->nullable()->after('id');
            $table->index('dialogflow_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voice_sessions', function (Blueprint $table) {
            $table->dropIndex(['dialogflow_session']);
            $table->dropColumn('dialogflow_session');
        });
    }
};
