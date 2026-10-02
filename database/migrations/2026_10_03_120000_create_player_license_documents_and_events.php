<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Circuit de licence : pièces justificatives (stockées en base, le service
 * n'ayant pas de disque persistant) et historique de la demande (suivi club
 * et fédération).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('player_license_documents')) {
            Schema::create('player_license_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('player_license_id')->constrained('player_licenses')->cascadeOnDelete();
                $table->string('document_type', 40);
                $table->string('original_name');
                $table->string('mime_type', 100);
                $table->unsignedInteger('size');
                $table->string('sha256', 64);
                $table->longText('content_base64'); // base64 : portable PostgreSQL / SQLite, sans piège d'encodage bytea
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index(['player_license_id', 'document_type']);
            });
        }
        if (!Schema::hasTable('player_license_events')) {
            Schema::create('player_license_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('player_license_id')->constrained('player_licenses')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 40);
                $table->text('message')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index(['player_license_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('player_license_events');
        Schema::dropIfExists('player_license_documents');
    }
};
