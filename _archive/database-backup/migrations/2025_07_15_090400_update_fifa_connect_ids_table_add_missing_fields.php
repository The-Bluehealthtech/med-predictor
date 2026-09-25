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
        Schema::table('fifa_connect_ids', function (Blueprint $table) {
            $table->string('fifa_id')->unique()->after('id');
            $table->string('entity_type')->after('fifa_id');
            $table->unsignedBigInteger('entity_id')->nullable()->after('entity_type');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('entity_id');
            $table->json('metadata')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fifa_connect_ids', function (Blueprint $table) {
            $table->dropColumn(['fifa_id', 'entity_type', 'entity_id', 'status', 'metadata']);
        });
    }
};
