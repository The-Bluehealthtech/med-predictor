<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasColumn('performance_metrics', 'fifa_connect_id')
            && !Schema::hasColumn(
                'performance_metrics',
                'fifa_connect_record_id'
            )
        ) {
            Schema::table(
                'performance_metrics',
                function (Blueprint $table) {
                    $table->renameColumn(
                        'fifa_connect_id',
                        'fifa_connect_record_id'
                    );
                }
            );
        }

        if (
            Schema::hasColumn('pcmas', 'fifa_connect_id')
            && !Schema::hasColumn(
                'pcmas',
                'fifa_connect_record_id'
            )
        ) {
            Schema::table(
                'pcmas',
                function (Blueprint $table) {
                    $table->renameColumn(
                        'fifa_connect_id',
                        'fifa_connect_record_id'
                    );
                }
            );
        }
    }

    public function down(): void
    {
        if (
            Schema::hasColumn(
                'performance_metrics',
                'fifa_connect_record_id'
            )
            && !Schema::hasColumn(
                'performance_metrics',
                'fifa_connect_id'
            )
        ) {
            Schema::table(
                'performance_metrics',
                function (Blueprint $table) {
                    $table->renameColumn(
                        'fifa_connect_record_id',
                        'fifa_connect_id'
                    );
                }
            );
        }

        if (
            Schema::hasColumn('pcmas', 'fifa_connect_record_id')
            && !Schema::hasColumn('pcmas', 'fifa_connect_id')
        ) {
            Schema::table(
                'pcmas',
                function (Blueprint $table) {
                    $table->renameColumn(
                        'fifa_connect_record_id',
                        'fifa_connect_id'
                    );
                }
            );
        }
    }
};
