<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE health_records
                 DROP CONSTRAINT IF EXISTS health_records_status_check'
            );

            DB::statement(
                "ALTER TABLE health_records
                 ADD CONSTRAINT health_records_status_check
                 CHECK (status IN ('active', 'archived', 'pending', 'hl7_report'))"
            );
        } elseif ($driver === 'mysql') {
            DB::statement(
                "ALTER TABLE health_records
                 MODIFY status ENUM('active', 'archived', 'pending', 'hl7_report')
                 NOT NULL DEFAULT 'active'"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::table('health_records')
                ->where('status', 'hl7_report')
                ->update(['status' => 'pending']);

            DB::statement(
                'ALTER TABLE health_records
                 DROP CONSTRAINT IF EXISTS health_records_status_check'
            );

            DB::statement(
                "ALTER TABLE health_records
                 ADD CONSTRAINT health_records_status_check
                 CHECK (status IN ('active', 'archived', 'pending'))"
            );
        } elseif ($driver === 'mysql') {
            DB::table('health_records')
                ->where('status', 'hl7_report')
                ->update(['status' => 'pending']);

            DB::statement(
                "ALTER TABLE health_records
                 MODIFY status ENUM('active', 'archived', 'pending')
                 NOT NULL DEFAULT 'active'"
            );
        }
    }
};
