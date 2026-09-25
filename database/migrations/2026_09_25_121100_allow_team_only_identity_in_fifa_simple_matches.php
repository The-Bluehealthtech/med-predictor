<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'fifa_connect_match_teams',
            function (Blueprint $table) {
                $table->string('organisation_fifa_id')
                    ->nullable()
                    ->change();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'fifa_connect_match_teams',
            function (Blueprint $table) {
                $table->string('organisation_fifa_id')
                    ->nullable(false)
                    ->change();
            }
        );
    }
};
