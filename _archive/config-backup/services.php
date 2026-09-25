<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'ai' => [
        'base_url' => env('AI_BASE_URL', 'http://localhost:3001'),
        'timeout' => env('AI_TIMEOUT', 30),
        'api_key' => env('AI_API_KEY'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'fhir' => [
        'base_url' => env('FHIR_BASE_URL', 'http://localhost:8080/fhir'),
        'timeout' => env('FHIR_TIMEOUT', 30),
        'retry_attempts' => env('FHIR_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('FHIR_RETRY_DELAY', 5),
        'audit_logging' => env('FHIR_AUDIT_LOGGING', true),
        'batch_size' => env('FHIR_BATCH_SIZE', 100),
        'version' => env('FHIR_VERSION', 'R4'),
    ],

    'pacs' => [
        'base_url' => env('PACS_BASE_URL', 'http://localhost:8042/dicom-web'),
        'timeout' => env('PACS_TIMEOUT', 60),
        'username' => env('PACS_USERNAME'),
        'password' => env('PACS_PASSWORD'),
    ],

    'fifa_connect' => [
        'base_url' => env('FIFA_CONNECT_BASE_URL', 'https://api.fifa.com/v1'),
        'api_key' => env('FIFA_CONNECT_API_KEY'),
        'timeout' => env('FIFA_CONNECT_TIMEOUT', 30),
        'rate_limit_delay' => env('FIFA_CONNECT_RATE_LIMIT_DELAY', 1),
        'cache_ttl' => env('FIFA_CONNECT_CACHE_TTL', 3600),
        'retry_attempts' => env('FIFA_CONNECT_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('FIFA_CONNECT_RETRY_DELAY', 5),
        'webhook_secret' => env('FIFA_WEBHOOK_SECRET'),
        'compliance_check' => env('FIFA_COMPLIANCE_CHECK', true),
        'mock_mode' => env('FIFA_CONNECT_MOCK_MODE', false),
    ],

    'fifa_tms' => [
        'base_url' => env('FIFA_TMS_BASE_URL', 'https://api.fifa.com/tms/v1'),
        'api_key' => env('FIFA_TMS_API_KEY'),
        'timeout' => env('FIFA_TMS_TIMEOUT', 15),
        'cache_ttl' => env('FIFA_TMS_CACHE_TTL', 3600),
        'retry_attempts' => env('FIFA_TMS_RETRY_ATTEMPTS', 2),
        'retry_delay' => env('FIFA_TMS_RETRY_DELAY', 3),
        'mock_mode' => env('FIFA_TMS_MOCK_MODE', false),
        'rate_limit' => env('FIFA_TMS_RATE_LIMIT', 100), // requêtes par minute
    ],

    'hl7_fhir' => [
        'base_url' => env('HL7_FHIR_BASE_URL', 'https://fhir.example.com'),
        'client_id' => env('HL7_FHIR_CLIENT_ID'),
        'client_secret' => env('HL7_FHIR_CLIENT_SECRET'),
        'timeout' => env('HL7_FHIR_TIMEOUT', 30),
        'retry_attempts' => env('HL7_FHIR_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('HL7_FHIR_RETRY_DELAY', 5),
        'audit_logging' => env('HL7_FHIR_AUDIT_LOGGING', true),
        'batch_size' => env('HL7_FHIR_BATCH_SIZE', 100),
        'version' => env('HL7_FHIR_VERSION', 'R4'),
    ],

    'icd11' => [
        'client_id' => env('ICD11_CLIENT_ID'),
        'client_secret' => env('ICD11_CLIENT_SECRET'),
        'base_url' => env('ICD11_BASE_URL', 'https://icd.who.int/icdapi'),
        'timeout' => env('ICD11_TIMEOUT', 30),
        'cache_ttl' => env('ICD11_CACHE_TTL', 3600),
        'retry_attempts' => env('ICD11_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('ICD11_RETRY_DELAY', 5),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'timeout' => env('OPENAI_TIMEOUT', 60),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
    ],

    'google' => [
        'api_key' => env('GOOGLE_API_KEY'),
        'base_url' => env('GOOGLE_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'timeout' => env('GOOGLE_TIMEOUT', 60),
        'model' => env('GOOGLE_MODEL', 'gemini-pro'),
        'max_tokens' => env('GOOGLE_MAX_TOKENS', 1000),
        'temperature' => env('GOOGLE_TEMPERATURE', 0.7),
    ],

];
