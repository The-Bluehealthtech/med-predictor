<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transfer_payments')) {
            return;
        }

        Schema::table('transfer_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('transfer_payments', 'proof_status')) {
                $table->string('proof_status', 24)->default('missing')->after('payment_notes');
            }
            if (!Schema::hasColumn('transfer_payments', 'proof_file_path')) {
                $table->string('proof_file_path')->nullable()->after('proof_status');
            }
            if (!Schema::hasColumn('transfer_payments', 'proof_storage_disk')) {
                $table->string('proof_storage_disk', 64)->default('local')->after('proof_file_path');
            }
            if (!Schema::hasColumn('transfer_payments', 'proof_file_name')) {
                $table->string('proof_file_name')->nullable()->after('proof_storage_disk');
            }
            if (!Schema::hasColumn('transfer_payments', 'proof_mime_type')) {
                $table->string('proof_mime_type', 128)->nullable()->after('proof_file_name');
            }
            if (!Schema::hasColumn('transfer_payments', 'proof_file_size')) {
                $table->unsignedBigInteger('proof_file_size')->nullable()->after('proof_mime_type');
            }
            if (!Schema::hasColumn('transfer_payments', 'proof_sha256')) {
                $table->string('proof_sha256', 64)->nullable()->after('proof_file_size');
            }
            if (!Schema::hasColumn('transfer_payments', 'proof_uploaded_at')) {
                $table->timestamp('proof_uploaded_at')->nullable()->after('proof_sha256');
            }
            if (!Schema::hasColumn('transfer_payments', 'proof_uploaded_by')) {
                $table->foreignId('proof_uploaded_by')->nullable()->after('proof_uploaded_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('transfer_payments', 'proof_validated_at')) {
                $table->timestamp('proof_validated_at')->nullable()->after('proof_uploaded_by');
            }
            if (!Schema::hasColumn('transfer_payments', 'proof_validated_by')) {
                $table->foreignId('proof_validated_by')->nullable()->after('proof_validated_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('transfer_payments', 'proof_validation_notes')) {
                $table->text('proof_validation_notes')->nullable()->after('proof_validated_by');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('transfer_payments')) {
            return;
        }

        Schema::table('transfer_payments', function (Blueprint $table) {
            foreach ([
                'proof_validation_notes','proof_validated_by','proof_validated_at','proof_uploaded_by',
                'proof_uploaded_at','proof_sha256','proof_file_size','proof_mime_type','proof_file_name',
                'proof_storage_disk','proof_file_path','proof_status',
            ] as $column) {
                if (Schema::hasColumn('transfer_payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
