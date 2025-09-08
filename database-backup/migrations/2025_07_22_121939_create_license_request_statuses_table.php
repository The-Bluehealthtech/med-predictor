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
        Schema::create('license_request_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('license_request_id');
            $table->enum('status', ['pending', 'validated', 'refused', 'cancelled']);
            $table->unsignedBigInteger('user_id'); // auteur du changement
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->foreign('license_request_id')->references('id')->on('license_requests')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_request_statuses');
    }
};
