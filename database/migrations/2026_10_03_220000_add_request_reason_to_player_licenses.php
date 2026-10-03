<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Motif de la demande de licence (règle nationale : première licence,
 * renouvellement, transfert, prêt, retour de prêt, changement de niveau) et
 * enregistrement précédent concerné, clôturé à l'approbation quand le motif
 * l'exige (FIFA Connect : Status inactive et RegistrationValidTo).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('player_licenses', function (Blueprint $table) {
            if (!Schema::hasColumn('player_licenses', 'request_reason')) {
                $table->string('request_reason', 20)->nullable();
            }
            if (!Schema::hasColumn('player_licenses', 'previous_license_id')) {
                $table->foreignId('previous_license_id')->nullable()->constrained('player_licenses')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('player_licenses', 'previous_license_id')) {
            Schema::table('player_licenses', fn (Blueprint $table) => $table->dropConstrainedForeignId('previous_license_id'));
        }
        if (Schema::hasColumn('player_licenses', 'request_reason')) {
            Schema::table('player_licenses', fn (Blueprint $table) => $table->dropColumn('request_reason'));
        }
    }
};
