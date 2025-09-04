<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('license_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('license_request_id');
            $table->string('file_path');
            $table->string('type'); // photo, certificat, etc.
            $table->timestamps();
            $table->foreign('license_request_id')->references('id')->on('license_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_request_attachments');
    }
};
