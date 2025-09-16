<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('match_events', function (Blueprint $table) {
            // Only add columns that do not already exist
            if (!Schema::hasColumn('match_events', 'event_type')) {
                $table->string('event_type')->nullable()->after('recorded_by_user_id');
            }
            if (!Schema::hasColumn('match_events', 'extra_time_minute')) {
                $table->integer('extra_time_minute')->nullable()->after('minute');
            }
            if (!Schema::hasColumn('match_events', 'period')) {
                $table->string('period')->nullable()->after('extra_time_minute');
            }
            if (!Schema::hasColumn('match_events', 'event_data')) {
                $table->json('event_data')->nullable()->after('period');
            }
            if (!Schema::hasColumn('match_events', 'location')) {
                $table->string('location')->nullable()->after('event_data');
            }
            if (!Schema::hasColumn('match_events', 'severity')) {
                $table->string('severity')->nullable()->after('location');
            }
            if (!Schema::hasColumn('match_events', 'is_confirmed')) {
                $table->boolean('is_confirmed')->default(true)->after('severity');
            }
            if (!Schema::hasColumn('match_events', 'is_contested')) {
                $table->boolean('is_contested')->default(false)->after('is_confirmed');
            }
            if (!Schema::hasColumn('match_events', 'contest_reason')) {
                $table->text('contest_reason')->nullable()->after('is_contested');
            }
            if (!Schema::hasColumn('match_events', 'recorded_at')) {
                $table->timestamp('recorded_at')->nullable()->after('contest_reason');
            }
        });

        // Copy data from 'type' to 'event_type' if event_type exists
        if (Schema::hasColumn('match_events', 'event_type')) {
            DB::statement('UPDATE match_events SET event_type = type');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_events', function (Blueprint $table) {
            $drops = [];
            foreach ([
                'event_type',
                'extra_time_minute',
                'period',
                'event_data',
                'location',
                'severity',
                'is_confirmed',
                'is_contested',
                'contest_reason',
                'recorded_at'
            ] as $col) {
                if (Schema::hasColumn('match_events', $col)) {
                    $drops[] = $col;
                }
            }
            if ($drops) {
                $table->dropColumn($drops);
            }
        });
    }
};
