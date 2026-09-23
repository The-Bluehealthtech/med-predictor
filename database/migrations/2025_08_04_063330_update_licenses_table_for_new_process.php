<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn(['name', 'type']);

            $table->string('license_type')->after('id');
            $table->string('applicant_name')->after('license_type');
            $table->date('date_of_birth')->after('applicant_name');
            $table->string('nationality')->after('date_of_birth');
            $table->string('position')->after('nationality');
            $table->string('email')->after('position');
            $table->string('phone')->after('email');

            $table->foreignId('player_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('club_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('association_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->text('license_reason');
            $table->enum('validity_period', ['1_year', '2_years', '3_years', '5_years']);
            $table->json('documents')->nullable();

            $table->foreignId('requested_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('requested_at')->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::table('licenses')
                ->whereNull('status')
                ->update(['status' => 'pending']);

            DB::statement(
                "ALTER TABLE licenses
                 ALTER COLUMN status SET DEFAULT 'pending'"
            );

            DB::statement(
                'ALTER TABLE licenses
                 ALTER COLUMN status SET NOT NULL'
            );

            DB::statement(
                'ALTER TABLE licenses
                 DROP CONSTRAINT IF EXISTS licenses_status_check'
            );

            DB::statement(
                "ALTER TABLE licenses
                 ADD CONSTRAINT licenses_status_check
                 CHECK (status IN ('pending', 'approved', 'rejected'))"
            );
        } elseif ($driver === 'mysql') {
            DB::statement(
                "ALTER TABLE licenses
                 MODIFY status ENUM('pending', 'approved', 'rejected')
                 NOT NULL DEFAULT 'pending'"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE licenses
                 DROP CONSTRAINT IF EXISTS licenses_status_check'
            );

            DB::statement(
                'ALTER TABLE licenses
                 ALTER COLUMN status DROP DEFAULT'
            );

            DB::statement(
                'ALTER TABLE licenses
                 ALTER COLUMN status DROP NOT NULL'
            );
        } elseif ($driver === 'mysql') {
            DB::statement(
                'ALTER TABLE licenses
                 MODIFY status VARCHAR(255) NULL'
            );
        }

        Schema::table('licenses', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->dropForeign(['club_id']);
            $table->dropForeign(['association_id']);
            $table->dropForeign(['requested_by']);
            $table->dropForeign(['approved_by']);

            $table->dropColumn([
                'license_type',
                'applicant_name',
                'date_of_birth',
                'nationality',
                'position',
                'email',
                'phone',
                'player_id',
                'club_id',
                'association_id',
                'license_reason',
                'validity_period',
                'documents',
                'requested_by',
                'requested_at',
                'approved_by',
                'approved_at',
                'rejection_reason',
            ]);

            $table->string('name')->nullable();
            $table->string('type')->nullable();
        });
    }
};
