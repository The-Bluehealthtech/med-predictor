<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(
            'ALTER TABLE health_records
             DROP CONSTRAINT IF EXISTS health_records_status_check'
        );

        DB::statement(
            "ALTER TABLE health_records
             ADD CONSTRAINT health_records_status_check
             CHECK (status IN (
                 'active',
                 'archived',
                 'pending',
                 'hl7_report',
                 'review_required',
                 'approved',
                 'rejected'
             ))"
        );
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::table('health_records')
            ->whereIn('status', ['review_required', 'approved', 'rejected'])
            ->update(['status' => 'pending']);

        DB::statement(
            'ALTER TABLE health_records
             DROP CONSTRAINT IF EXISTS health_records_status_check'
        );

        DB::statement(
            "ALTER TABLE health_records
             ADD CONSTRAINT health_records_status_check
             CHECK (status IN (
                 'active',
                 'archived',
                 'pending',
                 'hl7_report'
             ))"
        );
    }
};
