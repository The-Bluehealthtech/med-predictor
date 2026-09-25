<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'fifa_connect_match_competition_contexts',
            function (Blueprint $table) {
                $table->unsignedInteger('depth')
                    ->default(0)
                    ->after('parent_context_id');

                $table->index(
                    ['match_id', 'depth'],
                    'fc_match_comp_context_depth_idx'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'fifa_connect_match_competition_contexts',
            function (Blueprint $table) {
                $table->dropIndex(
                    'fc_match_comp_context_depth_idx'
                );
                $table->dropColumn('depth');
            }
        );
    }
};
