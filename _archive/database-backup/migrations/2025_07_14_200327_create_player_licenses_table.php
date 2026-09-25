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
        Schema::create('player_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->foreignId('club_id')->nullable()->constrained('clubs')->onDelete('cascade');
            $table->string('season')->nullable();
            $table->string('license_number')->unique()->nullable();
            $table->string('registration_number')->nullable();
            $table->string('license_type')->nullable();
            $table->string('license_category')->nullable();
            $table->string('status')->nullable();
            $table->string('approval_status')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->json('bonus_structure')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->string('contract_type')->nullable();
            $table->text('disciplinary_record')->nullable();
            $table->string('documentation_status')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('fifa_connect_id')->nullable();
            $table->boolean('fitness_certificate')->nullable();
            $table->boolean('international_clearance')->nullable();
            $table->date('issue_date')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->text('medical_clearance')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('release_clause', 15, 2)->nullable();
            $table->date('renewal_date')->nullable();
            $table->string('transfer_status')->nullable();
            $table->string('visa_status')->nullable();
            $table->decimal('wage_agreement', 15, 2)->nullable();
            $table->boolean('work_permit')->nullable();
            $table->foreignId('submitted_by_club_admin_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('processed_by_license_agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('document_path')->nullable();
            $table->json('documents_payload')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_licenses');
    }
};
