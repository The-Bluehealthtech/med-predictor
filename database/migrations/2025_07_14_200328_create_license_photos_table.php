<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicensePhotosTable extends Migration
{
    public function up(): void
    {
        Schema::create('license_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('photo_path');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_photos');
    }
}
