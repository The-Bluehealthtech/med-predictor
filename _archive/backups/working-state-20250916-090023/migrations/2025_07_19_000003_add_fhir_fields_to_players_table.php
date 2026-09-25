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
            if (!Schema::hasColumn('players', 'fifa_id')) {
                $table->string('fifa_id')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('players', 'fhir_patient_id')) {
                $table->string('fhir_patient_id')->nullable()->unique()->after('fifa_id');
            }
            if (!Schema::hasColumn('players', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('players', 'nationality')) {
                $table->string('nationality')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('players', 'position')) {
                $table->string('position')->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('players', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('position');
            }
            if (!Schema::hasColumn('players', 'fifa_data')) {
                $table->json('fifa_data')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('players', 'fhir_data')) {
                $table->json('fhir_data')->nullable()->after('fifa_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn([
                'fifa_id',
                'fhir_patient_id',
                'date_of_birth',
                'nationality',
                'position',
                'gender',
                'fifa_data',
                'fhir_data'
            ]);
        });
    }
}; 