<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transfer_documents')) {
            return;
        }

        Schema::table('transfer_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('transfer_documents', 'storage_disk')) {
                $table->string('storage_disk', 64)->default('local')->after('file_path');
            }
            if (!Schema::hasColumn('transfer_documents', 'sha256')) {
                $table->string('sha256', 64)->nullable()->after('file_size');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('transfer_documents')) {
            return;
        }

        Schema::table('transfer_documents', function (Blueprint $table) {
            if (Schema::hasColumn('transfer_documents', 'sha256')) {
                $table->dropColumn('sha256');
            }
            if (Schema::hasColumn('transfer_documents', 'storage_disk')) {
                $table->dropColumn('storage_disk');
            }
        });
    }
};
