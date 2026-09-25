<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Medical Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration settings for the medical module
    | including risk thresholds, compliance settings, and AI integration.
    |
    */

    // SCA Risk Analysis Settings
    'sca_risk_threshold' => env('SCA_RISK_THRESHOLD', 0.7),
    'sca_confidence_threshold' => env('SCA_CONFIDENCE_THRESHOLD', 0.8),

    // PCMA Compliance Settings
    'pcma_annual_requirement' => true,
    'pcma_types_required' => ['bpma', 'cardio', 'dental'],
    'pcma_expiry_days' => 365,

    // Injury Reporting Settings
    'injury_reporting_required' => true,
    'injury_reporting_deadline_hours' => 24,
    'concussion_reporting_deadline_hours' => 2,

    // Medical Notes Settings
    'medical_notes_approval_required' => true,
    'ai_generated_notes_approval_required' => true,
    'notes_retention_days' => 2555, // 7 years

    // Risk Alert Settings
    'risk_alert_retention_days' => 365,
    'critical_alert_response_hours' => 2,
    'high_alert_response_hours' => 24,
    'medium_alert_response_hours' => 72,
    'low_alert_response_hours' => 168,

    // FIFA Compliance Settings
    'fifa_compliance_enabled' => true,
    'fifa_reporting_required' => true,
    'fifa_data_retention_days' => 2555,

    // AI Integration Settings
    'ai_service_enabled' => env('AI_SERVICE_ENABLED', true),
    'ai_service_timeout' => env('AI_SERVICE_TIMEOUT', 30),
    'ai_service_retry_attempts' => env('AI_SERVICE_RETRY_ATTEMPTS', 3),

    // Notification Settings
    'notifications' => [
        'risk_alerts' => true,
        'pcma_expiry' => true,
        'injury_reports' => true,
        'ai_analysis_complete' => true,
    ],

    // Data Export Settings
    'export_formats' => ['pdf', 'csv', 'json'],
    'export_retention_days' => 30,

    // Privacy and Security
    'data_encryption_enabled' => true,
    'audit_logging_enabled' => true,
    'gdpr_compliance_enabled' => true,
    'hipaa_compliance_enabled' => env('HIPAA_COMPLIANCE_ENABLED', false),

    // Performance Settings
    'cache_enabled' => true,
    'cache_ttl_minutes' => 60,
    'pagination_default' => 15,
    'pagination_max' => 100,
]; 