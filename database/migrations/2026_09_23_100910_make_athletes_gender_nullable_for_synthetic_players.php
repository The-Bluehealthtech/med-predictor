<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement(
                'ALTER TABLE athletes ALTER COLUMN gender DROP NOT NULL'
            );
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            if (DB::table('athletes')->whereNull('gender')->exists()) {
                throw new \RuntimeException(
                    'Rollback impossible: certains athletes ont gender=NULL.'
                );
            }

            DB::statement(
                'ALTER TABLE athletes ALTER COLUMN gender SET NOT NULL'
            );
        }
    }
};
