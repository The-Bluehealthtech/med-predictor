<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fit_score_snapshots', function (Blueprint $table) {
            $table->string('input_signature', 64)
                ->nullable()
                ->after('calculation_version');

            $table->unique(
                ['player_id', 'calculation_version', 'input_signature'],
                'fit_snapshot_input_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('fit_score_snapshots', function (Blueprint $table) {
            $table->dropUnique('fit_snapshot_input_unique');
            $table->dropColumn('input_signature');
        });
    }
};
