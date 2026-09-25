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
        Schema::create('content_management', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // logo, image, document, banner, etc.
            $table->string('category'); // club, association, competition, system, etc.
            $table->string('name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_url');
            $table->string('mime_type');
            $table->integer('file_size'); // in bytes
            $table->json('metadata')->nullable(); // width, height, alt text, etc.
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('entity_id')->nullable(); // club_id, association_id, etc.
            $table->string('entity_type')->nullable(); // App\Models\Club, App\Models\Association, etc.
            $table->timestamps();

            $table->index(['type', 'category']);
            $table->index(['entity_type', 'entity_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_management');
    }
}; 