<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('clubs')) {
            Schema::table('clubs', function (Blueprint $table) {
                if (!Schema::hasColumn('clubs','matchday_medical_contact_name')) $table->string('matchday_medical_contact_name')->nullable();
                if (!Schema::hasColumn('clubs','matchday_medical_contact_phone')) $table->string('matchday_medical_contact_phone',64)->nullable();
                if (!Schema::hasColumn('clubs','matchday_hospital_name')) $table->string('matchday_hospital_name')->nullable();
                if (!Schema::hasColumn('clubs','matchday_hospital_phone')) $table->string('matchday_hospital_phone',64)->nullable();
                if (!Schema::hasColumn('clubs','matchday_ambulance_contact')) $table->string('matchday_ambulance_contact',128)->nullable();
            });
        }
        if (Schema::hasTable('competitions')) {
            Schema::table('competitions', function (Blueprint $table) {
                if (!Schema::hasColumn('competitions','matchday_medical_contact_name')) $table->string('matchday_medical_contact_name')->nullable();
                if (!Schema::hasColumn('competitions','matchday_medical_contact_phone')) $table->string('matchday_medical_contact_phone',64)->nullable();
                if (!Schema::hasColumn('competitions','matchday_hospital_name')) $table->string('matchday_hospital_name')->nullable();
                if (!Schema::hasColumn('competitions','matchday_hospital_phone')) $table->string('matchday_hospital_phone',64)->nullable();
                if (!Schema::hasColumn('competitions','matchday_ambulance_contact')) $table->string('matchday_ambulance_contact',128)->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['clubs','competitions'] as $tableName) {
            if (!Schema::hasTable($tableName)) continue;
            $columns=['matchday_medical_contact_name','matchday_medical_contact_phone','matchday_hospital_name','matchday_hospital_phone','matchday_ambulance_contact'];
            $existing=array_values(array_filter($columns, fn($column)=>Schema::hasColumn($tableName,$column)));
            if ($existing) Schema::table($tableName, fn(Blueprint $table)=>$table->dropColumn($existing));
        }
    }
};
