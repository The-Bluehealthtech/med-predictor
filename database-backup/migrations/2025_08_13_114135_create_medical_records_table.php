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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->string('record_type'); // consultation, prescription, test, vaccination, etc.
            $table->string('title');
            $table->text('description');
            $table->string('doctor_name');
            $table->string('medical_center');
            $table->date('record_date');
            $table->date('next_appointment')->nullable();
            $table->string('status'); // active, completed, pending, expired
            $table->json('medications')->nullable(); // Array of medications
            $table->json('test_results')->nullable(); // Array of test results
            $table->decimal('cost', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
