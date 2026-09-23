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
        Schema::create('associations', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('fifa_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::table('license_requests', function (Blueprint $table) {
            $table->foreign('national_association_id')
                ->references('id')
                ->on('associations')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_requests', function (Blueprint $table) {
            $table->dropForeign(['national_association_id']);
        });

        Schema::dropIfExists('associations');
    }
};
