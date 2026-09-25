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
        Schema::table('health_records', function (Blueprint $table) {
            // EMR Visit Information
            $table->date('visit_date')->nullable()->after('player_id');
            $table->string('doctor_name')->nullable()->after('visit_date');
            $table->enum('visit_type', ['consultation', 'emergency', 'follow_up', 'pre_season', 'post_match', 'rehabilitation'])->nullable()->after('doctor_name');
            $table->text('chief_complaint')->nullable()->after('visit_type');
            $table->text('physical_examination')->nullable()->after('chief_complaint');
            $table->text('laboratory_results')->nullable()->after('physical_examination');
            $table->text('imaging_results')->nullable()->after('laboratory_results');
            $table->text('prescriptions')->nullable()->after('imaging_results');
            $table->text('follow_up_instructions')->nullable()->after('prescriptions');
            $table->text('visit_notes')->nullable()->after('follow_up_instructions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_records', function (Blueprint $table) {
            $table->dropColumn([
                'visit_date',
                'doctor_name',
                'visit_type',
                'chief_complaint',
                'physical_examination',
                'laboratory_results',
                'imaging_results',
                'prescriptions',
                'follow_up_instructions',
                'visit_notes',
            ]);
        });
    }
};
