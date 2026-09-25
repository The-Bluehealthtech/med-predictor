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
            // Medical Imaging Files
            $table->string('ecg_file')->nullable();
            $table->date('ecg_date')->nullable();
            $table->string('ecg_interpretation')->nullable();
            $table->text('ecg_notes')->nullable();
            
            $table->string('mri_file')->nullable();
            $table->date('mri_date')->nullable();
            $table->string('mri_type')->nullable();
            $table->string('mri_findings')->nullable();
            $table->text('mri_notes')->nullable();
            
            $table->string('xray_file')->nullable();
            $table->string('ct_scan_file')->nullable();
            $table->string('ultrasound_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcmas', function (Blueprint $table) {
            $table->dropColumn([
                'ecg_file', 'ecg_date', 'ecg_interpretation', 'ecg_notes',
                'mri_file', 'mri_date', 'mri_type', 'mri_findings', 'mri_notes',
                'xray_file', 'ct_scan_file', 'ultrasound_file'
            ]);
        });
    }
};
