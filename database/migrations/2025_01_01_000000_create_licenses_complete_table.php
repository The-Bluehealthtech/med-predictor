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
        Schema::create('licenses_complete', function (Blueprint $table) {
            $table->id();
            $table->string('fifa_connect_id')->unique(); // FIFA CONNECT ID unique
            $table->string('license_type'); // player, staff, medical, coach, referee
            $table->string('applicant_name');
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('position')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('player_id')->nullable();
            $table->unsignedBigInteger('club_id')->nullable();
            $table->unsignedBigInteger('association_id')->nullable();
            $table->text('license_reason');
            $table->enum('validity_period', ['1_year', '2_years', '3_years', '5_years'])->default('1_year');
            $table->json('documents')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Champs FIFA CONNECT ID
            $table->string('fifa_license_number')->unique();
            $table->enum('fifa_license_category', ['amateur', 'semi_pro', 'professional', 'international'])->default('amateur');
            $table->enum('fifa_license_level', ['basic', 'intermediate', 'advanced', 'expert'])->default('basic');
            $table->date('fifa_license_issued_date')->nullable();
            $table->date('fifa_license_expiry_date')->nullable();
            $table->enum('fifa_license_status', ['active', 'suspended', 'expired', 'pending'])->default('pending');
            $table->json('fifa_license_restrictions')->nullable();
            $table->text('fifa_license_notes')->nullable();
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('player_id')->references('id')->on('players')->onDelete('set null');
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('set null');
            $table->foreign('association_id')->references('id')->on('associations')->onDelete('set null');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses_complete');
    }
};


