<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Consentement du joueur au partage de ses données de santé hors du club (IHE PCF) :
 * politique de confidentialité de la fédération, versionnée et immuable, et consentements
 * signés électroniquement qui la référencent.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('privacy_policies')) {
            Schema::create('privacy_policies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('association_id')->index();
                $table->unsignedInteger('version');
                $table->string('title');
                $table->longText('body');
                $table->char('body_sha256', 64);
                $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('published_at');
                $table->timestamps();
                $table->unique(['association_id', 'version']);
            });
        }
        if (!Schema::hasTable('player_consents')) {
            Schema::create('player_consents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
                $table->foreignId('privacy_policy_id')->constrained('privacy_policies');
                $table->string('decision', 10);                    // permit | deny
                $table->string('status', 20)->default('pending_signature'); // pending_signature | active | superseded | revoked | cancelled
                $table->date('period_end')->nullable();
                $table->string('performer_type', 10);              // player | guardian
                $table->string('performer_name');
                $table->string('performer_relationship', 10)->nullable(); // v3-RoleCode : MTH, FTH, GUARD…
                $table->string('performer_email')->nullable();
                $table->foreignId('signature_request_id')->nullable()->constrained('document_signature_requests')->nullOnDelete();
                $table->char('document_sha256', 64)->nullable();
                $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('signed_at')->nullable();
                $table->timestamp('revoked_at')->nullable();
                $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('fhir_consent_id', 64)->nullable();
                $table->string('sync_error', 500)->nullable();
                $table->timestamps();
                $table->index(['player_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('player_consents');
        Schema::dropIfExists('privacy_policies');
    }
};
