<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fichiers médicaux (examens du PCMA, documents reçus au pré-accueil) conservés en base :
 * le service Render n'a pas de disque persistant, un fichier écrit sur le disque local est
 * perdu au déploiement suivant.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('medical_files')) {
            Schema::create('medical_files', function (Blueprint $table) {
                $table->id();
                $table->string('owner_type', 40);          // pcma | visit_document
                $table->string('field', 40)->nullable();   // ex. ecg_file
                $table->string('file_name');
                $table->string('mime_type', 120)->nullable();
                $table->unsignedBigInteger('size');
                $table->char('sha256', 64);
                $table->longText('content_base64');
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_files');
    }
};
