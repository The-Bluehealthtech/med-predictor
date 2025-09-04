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
            $table->integer('fitness_score')->nullable()->after('potential_rating');
            $table->integer('injury_risk')->nullable()->after('fitness_score');
            $table->integer('market_value')->nullable()->after('injury_risk');
            $table->string('availability')->default('available')->after('market_value');
            $table->integer('form_percentage')->nullable()->after('availability');
            $table->integer('morale_percentage')->nullable()->after('form_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn([
                'fitness_score',
                'injury_risk',
                'market_value',
                'availability',
                'form_percentage',
                'morale_percentage'
            ]);
        });
    }
};
