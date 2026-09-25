<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('short_name')->nullable()->after('name');
            $table->string('country')->after('short_name');
            $table->string('confederation')->nullable()->after('country');
            $table->integer('fifa_ranking')->nullable()->after('confederation');
            $table->string('association_logo_url')->nullable()->after('fifa_ranking');
            $table->string('nation_flag_url')->nullable()->after('association_logo_url');
            $table->string('fifa_version')->default('FIFA 24')->after('nation_flag_url');
            $table->timestamp('last_updated')->nullable()->after('fifa_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'short_name',
                'country',
                'confederation',
                'fifa_ranking',
                'association_logo_url',
                'nation_flag_url',
                'fifa_version',
                'last_updated'
            ]);
        });
    }
};
