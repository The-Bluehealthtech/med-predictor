<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Attestations des passeports médicaux : signature électronique simple du médecin
// (authentification FIT, confirmation par mot de passe, empreinte SHA-256 du contenu attesté).
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('passport_attestations')) {
            return;
        }
        Schema::create('passport_attestations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('signer_name');
            $table->string('signer_role', 40);
            $table->string('signer_license')->nullable();
            $table->string('purpose', 30);
            $table->char('content_sha256', 64);
            $table->json('content');
            $table->timestamp('signed_at');
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index(['player_id', 'signed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passport_attestations');
    }
};
