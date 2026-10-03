<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_medical_incidents', function (Blueprint $table) {
            $table->string('protocol_code', 32)->nullable()->after('incident_type');
            $table->string('protocol_version', 64)->nullable()->after('protocol_code');
            $table->json('protocol_actions')->nullable()->after('abcde_assessment');
            $table->timestamp('protocol_activated_at')->nullable()->after('protocol_actions');
        });
    }

    public function down(): void
    {
        Schema::table('match_medical_incidents', function (Blueprint $table) {
            $table->dropColumn(['protocol_code','protocol_version','protocol_actions','protocol_activated_at']);
        });
    }
};
