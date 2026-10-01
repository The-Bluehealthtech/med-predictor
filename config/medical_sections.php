<?php
// Version de collecte : noms issus des formulaires et colonnes existants.
// Aucun seuil médical ni résultat calculé n'est défini dans cette configuration.
$groups = [
    'dental'=>['column'=>'dental_records','date'=>'dental_appointment_date','fields'=>[
        'dental_data'=>'json','dental_notes'=>'text','dental_treatment_plan'=>'text',
        'dental_health_status'=>'text','icd_10_dental'=>'text',
        'dental_appointment_date'=>'date','dental_appointment_time'=>'time','dental_appointment_reason'=>'text']],
    'scat'=>['column'=>'scat_assessments','date'=>'scat_date','fields'=>[
        'scat_assessments'=>'text','scat_date'=>'date','scat_time'=>'time','scat_context'=>'text','scat_evaluator'=>'text',
        'scat_red_flags'=>'list','scat_observable_signs'=>'list','scat_cervical_normal'=>'boolean',
        'scat_diagnosis'=>'text','scat_follow_up_plan'=>'text',
        'scat_headache'=>'symptom','scat_nausea'=>'symptom','scat_dizziness'=>'symptom','scat_fatigue'=>'symptom',
        'scat_light_sensitivity'=>'symptom','scat_noise_sensitivity'=>'symptom',
        'scat_orientation_month'=>'text','scat_orientation_date'=>'text','scat_orientation_year'=>'text',
        'scat_orientation_time'=>'text','scat_orientation_day'=>'text',
        'scat_immediate_memory_trial1'=>'text','scat_immediate_memory_trial2'=>'text','scat_immediate_memory_trial3'=>'text',
        'scat_concentration_digits'=>'text','scat_concentration_months'=>'text','scat_delayed_recall'=>'text',
        'scat_mbess_firm_feet'=>'balance','scat_mbess_firm_single'=>'balance','scat_mbess_firm_tandem'=>'balance',
        'scat_mbess_foam_feet'=>'balance','scat_mbess_foam_single'=>'balance','scat_mbess_foam_tandem'=>'balance']],
    'mapa'=>['column'=>'mapa_results','date'=>'mapa_date','fields'=>[
        'mapa_date'=>'date','mapa_reason'=>'text','mapa_device'=>'text','mapa_conclusion'=>'text','mapa_dipping'=>'text',
        'mapa_pas_24h'=>'number','mapa_pad_24h'=>'number','mapa_fc_24h'=>'number',
        'mapa_pas_day'=>'number','mapa_pad_day'=>'number','mapa_fc_day'=>'number','mapa_load_day'=>'number',
        'mapa_pas_night'=>'number','mapa_pad_night'=>'number','mapa_fc_night'=>'number','mapa_load_night'=>'number',
        'mapa_day_start'=>'time','mapa_day_end'=>'time','mapa_night_start'=>'time','mapa_night_end'=>'time']],
    'imaging'=>['column'=>'imaging_results','date'=>'imaging_date','fields'=>[
        'imaging_data'=>'json','imaging_type'=>'text','imaging_date'=>'date','imaging_facility'=>'text',
        'imaging_radiologist'=>'text','imaging_indication'=>'text','imaging_technique'=>'text',
        'imaging_findings'=>'text','imaging_conclusion'=>'text']],
    'mri'=>['column'=>'mri_results','date'=>'mri_date','fields'=>[
        'mri_results'=>'text','mri_date'=>'date','mri_body_part'=>'text','mri_technique'=>'text',
        'mri_facility'=>'text','mri_radiologist'=>'text','mri_findings'=>'text']],
    'ecg_effort'=>['column'=>'ecg_effort_results','date'=>'ecg_effort_date','fields'=>[
        'ecg_effort_results'=>'text','ecg_effort_date'=>'date','ecg_effort_duration'=>'number',
        'ecg_effort_max_fc'=>'number','ecg_effort_interpretation'=>'text','ecg_effort_findings'=>'text']],
    'scintigraphy'=>['column'=>'scintigraphy_results','date'=>'scintigraphy_date','fields'=>[
        'scintigraphy_results'=>'text','scintigraphy_date'=>'date','scintigraphy_type'=>'text',
        'scintigraphy_isotope'=>'text','scintigraphy_facility'=>'text','scintigraphy_radiologist'=>'text',
        'scintigraphy_findings'=>'text']],
    'fmarc'=>['column'=>'fifa_fmarc_assessments','date'=>'injury_date','fields'=>[
        'injury_records'=>'text','injury_date'=>'date','injury_location'=>'text','injury_severity'=>'text',
        'injury_mechanism'=>'text','return_to_play_date'=>'date',
        // Champs explicitement visibles dans le PDF fourni, page 30 ; stockage JSON existant.
        'context'=>'text','match_minute'=>'nonnegative_integer','injury_type'=>'text',
        'absence_cause'=>'text','absence_days'=>'nonnegative_integer','expected_return_date'=>'date']],
    'illness'=>['column'=>'illness_records','date'=>null,'fields'=>[
        'diagnosis'=>'text','symptoms'=>'list','treatment_plan'=>'text','icd_10_primary_diagnosis'=>'text',
        // Cause et absence demandées par le formulaire F-MARC, PDF page 32.
        'illness_cause'=>'text','absence_days'=>'nonnegative_integer']],
    'biological'=>['column'=>'biological_profile','date'=>null,'fields'=>[]],
    'laboratory'=>['column'=>'blood_test_results','date'=>null,'fields'=>[
        'blood_test_panel'=>'text','blood_test_result_value'=>'text','blood_test_result_unit'=>'text',
        'blood_test_result_status'=>'text','blood_test_normal_range'=>'text','blood_test_reference'=>'text',
        'blood_test_interpretation'=>'text','laboratory_results'=>'text']],
];
return ['version'=>'2026-10-01.1','max_file_kb'=>10240,'max_files'=>10,'sections'=>$groups,
    'file_inputs'=>['imaging_file'=>'imaging','mri_files'=>'mri','ecg_file'=>'imaging',
        'ecg_effort_file'=>'ecg_effort','scintigraphy_file'=>'scintigraphy','ct_files'=>'imaging','xray_file'=>'imaging']];
