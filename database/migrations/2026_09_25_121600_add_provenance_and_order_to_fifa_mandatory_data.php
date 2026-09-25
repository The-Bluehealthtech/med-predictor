<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fifa_connect_mandatory_data', function (Blueprint $table) {
            $table->string('source_reference')->nullable();
            $table->string('payload_hash', 64)->nullable();
            $table->timestamp('received_at')->nullable();
        });
        Schema::table('fifa_connect_mandatory_parts', function (Blueprint $table) {
            $table->unsignedSmallInteger('order_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fifa_connect_mandatory_parts', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });
        Schema::table('fifa_connect_mandatory_data', function (Blueprint $table) {
            $table->dropColumn(['source_reference', 'payload_hash', 'received_at']);
        });
    }
};
