<?php

namespace App\Services;

class MedicalTerminologyService
{
    // ICD-10 Codes for Medical Categories
    public const ICD_10_CODES = [
        'illness' => [
            'general' => ['Z00-Z99', 'A00-B99', 'C00-D49', 'E00-E89', 'F00-F99', 'G00-G99'],
            'heart_disease' => ['I00-I99'],
            'dental' => ['K00-K14'],
            'postural' => ['M40-M54'],
            'injury' => ['S00-T98']
        ],
        'aut' => ['Z51.8'], // Therapeutic use authorization
        'doping' => ['Z51.8'], // Therapeutic drug monitoring
        'blood_tests' => ['Z00.0'], // General examination
        'biological_profile' => ['Z00.1'], // Special screening
        'mapa' => ['I10-I15'], // Hypertension
        'mri' => ['Z01.8'], // Other special examination
        'ecg_effort' => ['Z01.8'], // Other special examination
        'scintigraphy' => ['Z01.8'] // Other special examination
    ];

    // SNOMED CT Codes
    public const SNOMED_CT_CODES = [
        'illness' => [
            'general' => ['363787002', '404684003', '243796009'],
            'heart_disease' => ['22298006', '194828000', '53741008'],
            'dental' => ['312536005', '312537001', '312538006'],
            'postural' => ['21522001', '300916003', '300917007'],
            'injury' => ['21522001', '300916003', '300917007']
        ],
        'aut' => ['416940007'], // Therapeutic authorization
        'doping' => ['416940007'], // Therapeutic drug monitoring
        'blood_tests' => ['363787002'], // Laboratory test
        'biological_profile' => ['363787002'], // Biological profile
        'mapa' => ['75367002'], // Ambulatory blood pressure monitoring
        'mri' => ['71651007'], // Magnetic resonance imaging
        'ecg_effort' => ['301095005'], // Exercise electrocardiogram
        'scintigraphy' => ['71651007'] // Nuclear medicine imaging
    ];

    // LOINC Codes
    public const LOINC_CODES = [
        'doping' => ['11556-8', '11557-6'], // Drug screening
        'blood_tests' => ['58410-2', '58409-4', '58408-6'], // CBC panel
        'mapa' => ['85354-9'], // Ambulatory BP monitoring
        'mri' => ['18748-4'], // MRI examination
        'ecg_effort' => ['11524-6'], // Exercise ECG
        'scintigraphy' => ['18748-4'] // Nuclear medicine
    ];

    public static function getMedicalCategories(): array
    {
        return [
            'illness' => 'Illness (General & Heart Diseases)',
            'doping' => 'Doping Control',
            'aut' => 'AUT (Therapeutic Use Authorization)',
            'blood_tests' => 'Blood Tests',
            'biological_profile' => 'Biological Profile',
            'dental_postural' => 'Dental & Postural',
            'injury' => 'Injuries (FIFA F-MARC & SCAT)',
            'heart_disease' => 'Heart Diseases',
            'mapa' => 'MAPA (Ambulatory BP Monitoring)',
            'mri' => 'MRI (Magnetic Resonance Imaging)',
            'ecg_effort' => 'ECG Effort (Exercise Electrocardiogram)',
            'scintigraphy' => 'Scintigraphy'
        ];
    }

    public static function getIcd10Codes(string $category): array
    {
        return self::ICD_10_CODES[$category] ?? [];
    }

    public static function getSnomedCtCodes(string $category): array
    {
        return self::SNOMED_CT_CODES[$category] ?? [];
    }

    public static function getLoincCodes(string $category): array
    {
        return self::LOINC_CODES[$category] ?? [];
    }

    public static function validateMedicalCode(string $code, string $type): bool
    {
        $codes = match($type) {
            'icd10' => array_merge(...array_values(self::ICD_10_CODES)),
            'snomed_ct' => array_merge(...array_values(self::SNOMED_CT_CODES)),
            'loinc' => array_merge(...array_values(self::LOINC_CODES)),
            default => []
        };

        return in_array($code, $codes);
    }
} 