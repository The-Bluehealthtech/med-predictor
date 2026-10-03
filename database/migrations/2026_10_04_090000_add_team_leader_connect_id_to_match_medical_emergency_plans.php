<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('match_medical_emergency_plans','team_leader_person_fifa_id')) {
            Schema::table('match_medical_emergency_plans', fn (Blueprint $table) => $table->string('team_leader_person_fifa_id')->nullable()->after('team_leader_user_id'));
        }
    }
    public function down(): void {
        if (Schema::hasColumn('match_medical_emergency_plans','team_leader_person_fifa_id')) {
            Schema::table('match_medical_emergency_plans', fn (Blueprint $table) => $table->dropColumn('team_leader_person_fifa_id'));
        }
    }
};
