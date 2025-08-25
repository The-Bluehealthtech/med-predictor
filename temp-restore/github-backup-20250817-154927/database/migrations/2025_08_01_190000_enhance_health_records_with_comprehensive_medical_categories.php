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
            // Medical Record Categories using HL7 ICD-10, SNOMED CT, LOINC terminology
            
            // 1. ILLNESS - General and Heart Diseases (ICD-10: I00-I99)
            $table->json('illness_records')->nullable()->comment('ICD-10 codes for general illnesses');
            $table->json('heart_disease_records')->nullable()->comment('ICD-10 codes for cardiovascular diseases');
            $table->string('icd_10_primary_diagnosis')->nullable()->comment('Primary ICD-10 diagnosis code');
            $table->string('icd_10_secondary_diagnosis')->nullable()->comment('Secondary ICD-10 diagnosis codes');
            $table->string('snomed_ct_condition')->nullable()->comment('SNOMED CT condition identifier');
            
            // 2. DOPING CONTROL (LOINC: 11556-8, 11557-6)
            $table->json('doping_tests')->nullable()->comment('Doping test results and history');
            $table->string('doping_test_status')->nullable()->comment('Current doping test status');
            $table->timestamp('last_doping_test_date')->nullable();
            $table->timestamp('next_doping_test_date')->nullable();
            $table->string('doping_test_lab')->nullable()->comment('Laboratory performing doping tests');
            $table->string('loinc_doping_panel')->nullable()->comment('LOINC code for doping panel');
            
            // 3. AUT - Therapeutic Use Authorization (SNOMED CT: 416940007)
            $table->json('aut_records')->nullable()->comment('AUT - Autorisation d\'Usage Thérapeutique');
            $table->string('aut_status')->nullable()->comment('AUT approval status');
            $table->timestamp('aut_approval_date')->nullable();
            $table->timestamp('aut_expiry_date')->nullable();
            $table->string('aut_authorized_substance')->nullable()->comment('Substance authorized for therapeutic use');
            $table->string('aut_authorizing_physician')->nullable();
            $table->text('aut_medical_justification')->nullable();
            $table->string('snomed_ct_aut')->nullable()->comment('SNOMED CT code for therapeutic authorization');
            
            // 4. BLOOD TESTS (LOINC: 58410-2, 58409-4, 58408-6)
            $table->json('blood_test_results')->nullable()->comment('Comprehensive blood test results');
            $table->string('blood_test_panel')->nullable()->comment('LOINC code for blood test panel');
            $table->timestamp('last_blood_test_date')->nullable();
            $table->timestamp('next_blood_test_date')->nullable();
            $table->json('hematology_results')->nullable()->comment('Complete blood count and hematology');
            $table->json('biochemistry_results')->nullable()->comment('Biochemical markers');
            $table->json('hormone_results')->nullable()->comment('Hormonal profile');
            $table->json('vitamin_results')->nullable()->comment('Vitamin levels');
            $table->json('mineral_results')->nullable()->comment('Mineral and electrolyte levels');
            
            // 5. BIOLOGICAL PROFILE (SNOMED CT: 363787002)
            $table->json('biological_profile')->nullable()->comment('Comprehensive biological profile');
            $table->string('biological_age')->nullable()->comment('Calculated biological age');
            $table->json('metabolic_markers')->nullable()->comment('Metabolic health markers');
            $table->json('inflammatory_markers')->nullable()->comment('Inflammation markers');
            $table->json('oxidative_stress_markers')->nullable()->comment('Oxidative stress indicators');
            $table->string('snomed_ct_biological_profile')->nullable()->comment('SNOMED CT code for biological profile');
            
            // 6. DENTAL AND POSTURAL (ICD-10: K00-K14, M40-M54)
            $table->json('dental_records')->nullable()->comment('Dental health records');
            $table->json('postural_assessment')->nullable()->comment('Postural analysis and assessment');
            $table->string('dental_health_status')->nullable()->comment('Overall dental health status');
            $table->string('postural_alignment')->nullable()->comment('Postural alignment assessment');
            $table->json('dental_treatments')->nullable()->comment('Dental treatment history');
            $table->json('postural_corrections')->nullable()->comment('Postural correction interventions');
            $table->string('icd_10_dental')->nullable()->comment('ICD-10 codes for dental conditions');
            $table->string('icd_10_postural')->nullable()->comment('ICD-10 codes for postural disorders');
            
            // 7. INJURIES - FIFA F-MARC and SCAT (SNOMED CT: 21522001)
            $table->json('injury_records')->nullable()->comment('Comprehensive injury history');
            $table->json('fifa_fmarc_assessments')->nullable()->comment('FIFA F-MARC injury assessments');
            $table->json('scat_assessments')->nullable()->comment('SCAT concussion assessments');
            $table->string('injury_severity')->nullable()->comment('Injury severity classification');
            $table->string('injury_mechanism')->nullable()->comment('Mechanism of injury');
            $table->string('injury_location')->nullable()->comment('Anatomical location of injury');
            $table->timestamp('injury_date')->nullable();
            $table->timestamp('return_to_play_date')->nullable();
            $table->string('snomed_ct_injury')->nullable()->comment('SNOMED CT code for injury type');
            $table->string('icd_10_injury')->nullable()->comment('ICD-10 code for injury');
            
            // 8. HEART DISEASES (ICD-10: I00-I99, LOINC: 8867-4)
            $table->json('heart_disease_assessments')->nullable()->comment('Cardiovascular disease assessments');
            $table->string('cardiac_risk_factors')->nullable()->comment('Cardiac risk factor assessment');
            $table->json('cardiac_markers')->nullable()->comment('Cardiac biomarkers');
            $table->string('heart_disease_status')->nullable()->comment('Current heart disease status');
            $table->string('icd_10_cardiac')->nullable()->comment('ICD-10 codes for cardiac conditions');
            $table->string('snomed_ct_cardiac')->nullable()->comment('SNOMED CT code for cardiac conditions');
            
            // 9. MAPA - Ambulatory Blood Pressure Monitoring (LOINC: 85354-9)
            $table->json('mapa_results')->nullable()->comment('MAPA - Mesure ambulatoire de la P.A.');
            $table->string('mapa_status')->nullable()->comment('MAPA test status');
            $table->timestamp('mapa_test_date')->nullable();
            $table->json('mapa_24h_profile')->nullable()->comment('24-hour blood pressure profile');
            $table->json('mapa_day_night_ratio')->nullable()->comment('Day/night blood pressure ratio');
            $table->string('mapa_dipper_status')->nullable()->comment('Dipper/non-dipper status');
            $table->string('loinc_mapa')->nullable()->comment('LOINC code for ambulatory BP monitoring');
            
            // 10. MRI - Magnetic Resonance Imaging (LOINC: 18748-4)
            $table->json('mri_results')->nullable()->comment('MRI examination results');
            $table->string('mri_body_part')->nullable()->comment('Anatomical region imaged');
            $table->string('mri_technique')->nullable()->comment('MRI technique used');
            $table->timestamp('mri_date')->nullable();
            $table->string('mri_facility')->nullable()->comment('MRI facility name');
            $table->string('mri_radiologist')->nullable()->comment('Radiologist interpreting MRI');
            $table->json('mri_findings')->nullable()->comment('Detailed MRI findings');
            $table->string('loinc_mri')->nullable()->comment('LOINC code for MRI examination');
            $table->string('snomed_ct_mri')->nullable()->comment('SNOMED CT code for MRI procedure');
            
            // 11. ECG EFFORT - Exercise Electrocardiogram (LOINC: 11524-6)
            $table->json('ecg_effort_results')->nullable()->comment('ECG effort test results');
            $table->string('ecg_effort_status')->nullable()->comment('ECG effort test status');
            $table->timestamp('ecg_effort_date')->nullable();
            $table->integer('ecg_effort_duration')->nullable()->comment('Test duration in minutes');
            $table->integer('ecg_effort_max_hr')->nullable()->comment('Maximum heart rate achieved');
            $table->string('ecg_effort_interpretation')->nullable()->comment('ECG effort interpretation');
            $table->json('ecg_effort_findings')->nullable()->comment('Detailed ECG effort findings');
            $table->string('loinc_ecg_effort')->nullable()->comment('LOINC code for exercise ECG');
            $table->string('snomed_ct_ecg_effort')->nullable()->comment('SNOMED CT code for exercise ECG');
            
            // 12. SCINTIGRAPHY (LOINC: 18748-4)
            $table->json('scintigraphy_results')->nullable()->comment('Scintigraphy examination results');
            $table->string('scintigraphy_type')->nullable()->comment('Type of scintigraphy');
            $table->string('scintigraphy_isotope')->nullable()->comment('Radioisotope used');
            $table->timestamp('scintigraphy_date')->nullable();
            $table->string('scintigraphy_facility')->nullable()->comment('Scintigraphy facility');
            $table->string('scintigraphy_radiologist')->nullable()->comment('Radiologist interpreting scintigraphy');
            $table->json('scintigraphy_findings')->nullable()->comment('Detailed scintigraphy findings');
            $table->string('loinc_scintigraphy')->nullable()->comment('LOINC code for scintigraphy');
            $table->string('snomed_ct_scintigraphy')->nullable()->comment('SNOMED CT code for scintigraphy');
            
            // Additional Medical Terminology Fields
            $table->json('icd_10_codes')->nullable()->comment('All ICD-10 codes for this record');
            $table->json('snomed_ct_codes')->nullable()->comment('All SNOMED CT codes for this record');
            $table->json('loinc_codes')->nullable()->comment('All LOINC codes for this record');
            $table->string('medical_record_type')->nullable()->comment('Type of medical record');
            $table->string('medical_record_category')->nullable()->comment('Primary medical category');
            
            // Enhanced Status Enum
            $table->enum('status', ['active', 'archived', 'pending', 'hl7_report', 'review_required', 'approved', 'rejected'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_records', function (Blueprint $table) {
            // Remove all the new columns
            $table->dropColumn([
                'illness_records', 'heart_disease_records', 'icd_10_primary_diagnosis', 'icd_10_secondary_diagnosis', 'snomed_ct_condition',
                'doping_tests', 'doping_test_status', 'last_doping_test_date', 'next_doping_test_date', 'doping_test_lab', 'loinc_doping_panel',
                'aut_records', 'aut_status', 'aut_approval_date', 'aut_expiry_date', 'aut_authorized_substance', 'aut_authorizing_physician', 'aut_medical_justification', 'snomed_ct_aut',
                'blood_test_results', 'blood_test_panel', 'last_blood_test_date', 'next_blood_test_date', 'hematology_results', 'biochemistry_results', 'hormone_results', 'vitamin_results', 'mineral_results',
                'biological_profile', 'biological_age', 'metabolic_markers', 'inflammatory_markers', 'oxidative_stress_markers', 'snomed_ct_biological_profile',
                'dental_records', 'postural_assessment', 'dental_health_status', 'postural_alignment', 'dental_treatments', 'postural_corrections', 'icd_10_dental', 'icd_10_postural',
                'injury_records', 'fifa_fmarc_assessments', 'scat_assessments', 'injury_severity', 'injury_mechanism', 'injury_location', 'injury_date', 'return_to_play_date', 'snomed_ct_injury', 'icd_10_injury',
                'heart_disease_assessments', 'cardiac_risk_factors', 'cardiac_markers', 'heart_disease_status', 'icd_10_cardiac', 'snomed_ct_cardiac',
                'mapa_results', 'mapa_status', 'mapa_test_date', 'mapa_24h_profile', 'mapa_day_night_ratio', 'mapa_dipper_status', 'loinc_mapa',
                'mri_results', 'mri_body_part', 'mri_technique', 'mri_date', 'mri_facility', 'mri_radiologist', 'mri_findings', 'loinc_mri', 'snomed_ct_mri',
                'ecg_effort_results', 'ecg_effort_status', 'ecg_effort_date', 'ecg_effort_duration', 'ecg_effort_max_hr', 'ecg_effort_interpretation', 'ecg_effort_findings', 'loinc_ecg_effort', 'snomed_ct_ecg_effort',
                'scintigraphy_results', 'scintigraphy_type', 'scintigraphy_isotope', 'scintigraphy_date', 'scintigraphy_facility', 'scintigraphy_radiologist', 'scintigraphy_findings', 'loinc_scintigraphy', 'snomed_ct_scintigraphy',
                'icd_10_codes', 'snomed_ct_codes', 'loinc_codes', 'medical_record_type', 'medical_record_category'
            ]);
            
            // Revert status enum
            $table->enum('status', ['active', 'archived', 'pending', 'hl7_report'])->default('active')->change();
        });
    }
}; 