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
        Schema::table('licenses', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['name', 'type']);
            
            // Add new columns for the complete license process
            $table->string('license_type')->after('id'); // player, staff, medical
            $table->string('applicant_name')->after('license_type');
            $table->date('date_of_birth')->after('applicant_name');
            $table->string('nationality')->after('date_of_birth');
            $table->string('position')->after('nationality');
            $table->string('email')->after('position');
            $table->string('phone')->after('email');
            $table->foreignId('player_id')->nullable()->constrained()->onDelete('cascade')->after('phone');
            $table->foreignId('club_id')->nullable()->constrained()->onDelete('cascade')->after('player_id');
            $table->foreignId('association_id')->nullable()->constrained()->onDelete('cascade')->after('club_id');
            $table->text('license_reason')->after('association_id');
            $table->enum('validity_period', ['1_year', '2_years', '3_years', '5_years'])->after('license_reason');
            $table->json('documents')->nullable()->after('validity_period'); // Store uploaded documents paths
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null')->after('documents');
            $table->timestamp('requested_at')->nullable()->after('requested_by');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('requested_at');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');
            
            // Update status column to use enum
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            // Drop new columns
            $table->dropForeign(['club_id', 'association_id', 'requested_by', 'approved_by']);
            $table->dropColumn([
                'license_type',
                'applicant_name',
                'date_of_birth',
                'nationality',
                'position',
                'email',
                'phone',
                'club_id',
                'association_id',
                'license_reason',
                'validity_period',
                'documents',
                'requested_by',
                'requested_at',
                'approved_by',
                'approved_at',
                'rejection_reason'
            ]);
            
            // Restore old columns
            $table->string('name');
            $table->string('type');
            
            // Restore old status column
            $table->string('status')->change();
        });
    }
};
