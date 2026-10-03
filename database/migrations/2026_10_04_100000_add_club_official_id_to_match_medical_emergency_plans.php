<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('match_medical_emergency_plans') && !Schema::hasColumn('match_medical_emergency_plans','team_leader_club_official_id')) {
            Schema::table('match_medical_emergency_plans', function (Blueprint $table) {
                $table->foreignId('team_leader_club_official_id')->nullable()->after('team_leader_user_id')->constrained('club_officials')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('match_medical_emergency_plans') && Schema::hasColumn('match_medical_emergency_plans','team_leader_club_official_id')) {
            Schema::table('match_medical_emergency_plans', function (Blueprint $table) {
                $table->dropConstrainedForeignId('team_leader_club_official_id');
            });
        }
    }
};
