<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE players
                 ALTER COLUMN fifa_connect_id DROP NOT NULL'
            );
        } elseif ($driver === 'mysql') {
            Schema::table('players', function (Blueprint $table) {
                $table->string('fifa_connect_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE players
                 ALTER COLUMN fifa_connect_id SET NOT NULL'
            );
        } elseif ($driver === 'mysql') {
            Schema::table('players', function (Blueprint $table) {
                $table->string('fifa_connect_id')->nullable(false)->change();
            });
        }
    }
};
