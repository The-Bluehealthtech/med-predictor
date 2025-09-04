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
        Schema::table('pcmas', function (Blueprint $table) {
            // Add new FIFA-compliant fields
            $table->json('medical_history')->nullable()->after('result_json');
            $table->json('physical_examination')->nullable()->after('medical_history');
            $table->json('cardiovascular_investigations')->nullable()->after('physical_examination');
            $table->json('final_statement')->nullable()->after('cardiovascular_investigations');
            $table->json('scat_assessment')->nullable()->after('final_statement');
            
            // Add assessment date field
            $table->date('assessment_date')->nullable()->after('assessor_id');
            
            // Add FIFA-specific fields
            $table->string('fifa_id')->nullable()->after('assessment_date');
            $table->string('competition_name')->nullable()->after('fifa_id');
            $table->string('competition_date')->nullable()->after('competition_name');
            $table->string('team_name')->nullable()->after('competition_date');
            $table->string('position')->nullable()->after('team_name');
            
            // Add compliance tracking
            $table->boolean('fifa_compliant')->default(false)->after('status');
            $table->timestamp('fifa_approved_at')->nullable()->after('fifa_compliant');
            $table->string('fifa_approved_by')->nullable()->after('fifa_approved_at');
            
            // Add anatomical annotations support
            $table->json('anatomical_annotations')->nullable()->after('scat_assessment');
            
            // Add document attachments
            $table->json('attachments')->nullable()->after('anatomical_annotations');
            
            // Add version tracking
            $table->string('form_version')->default('1.0')->after('attachments');
            $table->timestamp('last_updated_at')->nullable()->after('form_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcmas', function (Blueprint $table) {
            $table->dropColumn([
                'medical_history',
                'physical_examination', 
                'cardiovascular_investigations',
                'final_statement',
                'scat_assessment',
                'assessment_date',
                'fifa_id',
                'competition_name',
                'competition_date',
                'team_name',
                'position',
                'fifa_compliant',
                'fifa_approved_at',
                'fifa_approved_by',
                'anatomical_annotations',
                'attachments',
                'form_version',
                'last_updated_at'
            ]);
        });
    }
}; 