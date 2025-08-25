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
        Schema::table('health_records', function (Blueprint $table) {
            // Add 'hl7_report' to the status enum
            $table->enum('status', ['active', 'archived', 'pending', 'hl7_report'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_records', function (Blueprint $table) {
            // Remove 'hl7_report' from the status enum
            $table->enum('status', ['active', 'archived', 'pending'])->default('active')->change();
        });
    }
};
