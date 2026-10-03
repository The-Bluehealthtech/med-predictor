<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_medical_emergency_plans', function (Blueprint $table) {
            $table->string('connect_match_fifa_id')->nullable()->after('protocol_version');
            $table->json('connect_role_assignments')->nullable()->after('role_assignments');
        });
    }

    public function down(): void
    {
        Schema::table('match_medical_emergency_plans', function (Blueprint $table) {
            $table->dropColumn(['connect_match_fifa_id','connect_role_assignments']);
        });
    }
};
