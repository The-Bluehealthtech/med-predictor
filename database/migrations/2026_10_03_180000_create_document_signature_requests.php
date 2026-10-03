<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('document_signature_requests')) {
            return;
        }

        Schema::create('document_signature_requests', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 64);
            $table->string('workflow', 120);
            $table->string('document_type', 120);
            $table->string('document_reference', 255);
            $table->string('signer_type', 64);
            $table->unsignedBigInteger('signer_id')->nullable();
            $table->string('signer_role', 64)->nullable();
            $table->string('signer_name')->nullable();
            $table->string('signer_email')->nullable();
            $table->string('status', 32)->default('pending');
            $table->string('external_reference')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
            $table->index(['workflow', 'document_type', 'document_reference'], 'doc_sig_doc_idx');
            $table->index(['signer_type', 'signer_id'], 'doc_sig_signer_idx');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('document_signature_requests');
    }
};
