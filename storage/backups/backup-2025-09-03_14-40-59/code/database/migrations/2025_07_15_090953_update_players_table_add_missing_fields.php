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
        Schema::table('players', function (Blueprint $table) {
            $table->unsignedTinyInteger('weak_foot')->nullable()->after('preferred_foot');
            $table->unsignedTinyInteger('skill_moves')->nullable()->after('weak_foot');
            $table->unsignedTinyInteger('international_reputation')->nullable()->after('skill_moves');
            $table->string('body_type')->nullable()->after('work_rate');
            $table->boolean('real_face')->default(false)->after('body_type');
            $table->decimal('release_clause_eur', 15, 2)->nullable()->after('real_face');
            $table->decimal('value_eur', 15, 2)->nullable()->after('potential_rating');
            $table->decimal('wage_eur', 15, 2)->nullable()->after('value_eur');
            $table->date('contract_valid_until')->nullable()->after('age');
            $table->string('fifa_version')->nullable()->after('contract_valid_until');
            $table->timestamp('last_updated')->nullable()->after('fifa_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn([
                'weak_foot',
                'skill_moves',
                'international_reputation',
                'body_type',
                'real_face',
                'release_clause_eur',
                'value_eur',
                'wage_eur',
                'contract_valid_until',
                'fifa_version',
                'last_updated',
            ]);
        });
    }
};
