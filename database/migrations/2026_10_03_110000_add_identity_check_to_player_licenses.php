<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Circuit de licence : résultat de la vérification d'identité auprès du
 * registre FIFA ID (facultative) et réponse du club à une demande de complément.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('player_licenses', function (Blueprint $table) {
            if (!Schema::hasColumn('player_licenses', 'identity_check_status')) {
                $table->string('identity_check_status', 30)->nullable();
            }
            if (!Schema::hasColumn('player_licenses', 'identity_checked_at')) {
                $table->timestamp('identity_checked_at')->nullable();
            }
            if (!Schema::hasColumn('player_licenses', 'identity_check')) {
                $table->json('identity_check')->nullable();
            }
            if (!Schema::hasColumn('player_licenses', 'club_response')) {
                $table->text('club_response')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('player_licenses', function (Blueprint $table) {
            foreach (['identity_check_status', 'identity_checked_at', 'identity_check', 'club_response'] as $column) {
                if (Schema::hasColumn('player_licenses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
