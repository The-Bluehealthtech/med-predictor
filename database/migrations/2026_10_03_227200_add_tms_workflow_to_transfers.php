<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transfers')) {
            return;
        }

        Schema::table('transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('transfers', 'tms_transfer_id')) {
                $table->string('tms_transfer_id')->nullable()->after('fifa_transfer_id');
            }
            if (!Schema::hasColumn('transfers', 'tms_sync_status')) {
                $table->string('tms_sync_status', 32)->default('not_ready')->after('tms_transfer_id');
            }
            if (!Schema::hasColumn('transfers', 'tms_remote_status')) {
                $table->string('tms_remote_status', 64)->nullable()->after('tms_sync_status');
            }
            if (!Schema::hasColumn('transfers', 'tms_prepared_at')) {
                $table->timestamp('tms_prepared_at')->nullable()->after('tms_remote_status');
            }
            if (!Schema::hasColumn('transfers', 'tms_prepared_by')) {
                $table->foreignId('tms_prepared_by')->nullable()->after('tms_prepared_at')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('transfers', 'tms_last_synced_at')) {
                $table->timestamp('tms_last_synced_at')->nullable()->after('tms_prepared_by');
            }
            if (!Schema::hasColumn('transfers', 'tms_payload_sha256')) {
                $table->string('tms_payload_sha256', 64)->nullable()->after('tms_last_synced_at');
            }
            if (!Schema::hasColumn('transfers', 'tms_snapshot')) {
                $table->json('tms_snapshot')->nullable()->after('tms_payload_sha256');
            }
            if (!Schema::hasColumn('transfers', 'tms_last_response')) {
                $table->json('tms_last_response')->nullable()->after('tms_snapshot');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('transfers')) {
            return;
        }

        Schema::table('transfers', function (Blueprint $table) {
            $columns = [
                'tms_last_response', 'tms_snapshot', 'tms_payload_sha256',
                'tms_last_synced_at', 'tms_prepared_by', 'tms_prepared_at',
                'tms_remote_status', 'tms_sync_status', 'tms_transfer_id',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('transfers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
