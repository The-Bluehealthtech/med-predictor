<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Assistant Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'intégration Google Assistant avec FIT
    |
    */

    'enabled' => env('GOOGLE_ASSISTANT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */
    'webhook' => [
        'url' => env('GOOGLE_ASSISTANT_WEBHOOK_URL', '/google-assistant/webhook'),
        'secret' => env('GOOGLE_ASSISTANT_WEBHOOK_SECRET'),
        'timeout' => env('GOOGLE_ASSISTANT_WEBHOOK_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'enabled' => env('GOOGLE_ASSISTANT_AUTH_ENABLED', true),
        'method' => env('GOOGLE_ASSISTANT_AUTH_METHOD', 'jwt'), // jwt, oauth, api_key
        'api_key' => env('GOOGLE_ASSISTANT_API_KEY'),
        'jwt_secret' => env('GOOGLE_ASSISTANT_JWT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Voice Processing
    |--------------------------------------------------------------------------
    */
    'voice' => [
        'default_language' => env('GOOGLE_ASSISTANT_DEFAULT_LANGUAGE', 'fr'),
        'supported_languages' => ['fr', 'en', 'ar'],
        'confidence_threshold' => env('GOOGLE_ASSISTANT_CONFIDENCE_THRESHOLD', 0.7),
        'max_retries' => env('GOOGLE_ASSISTANT_MAX_RETRIES', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | PCMA Configuration
    |--------------------------------------------------------------------------
    */
    'pcma' => [
        'enabled' => env('GOOGLE_ASSISTANT_PCMA_ENABLED', true),
        'max_session_duration' => env('GOOGLE_ASSISTANT_MAX_SESSION_DURATION', 30), // minutes
        'required_fields' => [
            'poste',
            'age',
            'antecedents',
            'derniere_blessure',
            'statut'
        ],
        'field_validation' => [
            'poste' => [
                'type' => 'enum',
                'values' => ['défenseur', 'milieu', 'attaquant', 'gardien'],
                'required' => true
            ],
            'age' => [
                'type' => 'integer',
                'min' => 16,
                'max' => 50,
                'required' => true
            ],
            'antecedents' => [
                'type' => 'boolean',
                'required' => true
            ],
            'derniere_blessure' => [
                'type' => 'date',
                'required' => false
            ],
            'statut' => [
                'type' => 'enum',
                'values' => ['apte', 'inapte temporairement', 'inapte définitivement'],
                'required' => true
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    */
    'errors' => [
        'log_voice_errors' => env('GOOGLE_ASSISTANT_LOG_VOICE_ERRORS', true),
        'max_error_count' => env('GOOGLE_ASSISTANT_MAX_ERROR_COUNT', 5),
        'fallback_to_web' => env('GOOGLE_ASSISTANT_FALLBACK_TO_WEB', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('GOOGLE_ASSISTANT_LOGGING_ENABLED', true),
        'level' => env('GOOGLE_ASSISTANT_LOG_LEVEL', 'info'),
        'channels' => ['google_assistant'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Development
    |--------------------------------------------------------------------------
    */
    'development' => [
        'enabled' => env('GOOGLE_ASSISTANT_DEV_MODE', false),
        'test_user_id' => env('GOOGLE_ASSISTANT_TEST_USER_ID'),
        'mock_responses' => env('GOOGLE_ASSISTANT_MOCK_RESPONSES', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */
    'security' => [
        'rate_limiting' => [
            'enabled' => env('GOOGLE_ASSISTANT_RATE_LIMITING', true),
            'max_requests_per_minute' => env('GOOGLE_ASSISTANT_MAX_REQUESTS_PER_MINUTE', 60),
        ],
        'ip_whitelist' => env('GOOGLE_ASSISTANT_IP_WHITELIST', ''),
        'user_agent_validation' => env('GOOGLE_ASSISTANT_USER_AGENT_VALIDATION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration
    |--------------------------------------------------------------------------
    */
    'integration' => [
        'fit_api' => [
            'base_url' => env('FIT_API_BASE_URL', 'http://localhost:8000/api'),
            'timeout' => env('FIT_API_TIMEOUT', 30),
            'retry_attempts' => env('FIT_API_RETRY_ATTEMPTS', 3),
        ],
        'webhook_retry' => [
            'enabled' => env('GOOGLE_ASSISTANT_WEBHOOK_RETRY', true),
            'max_attempts' => env('GOOGLE_ASSISTANT_WEBHOOK_MAX_ATTEMPTS', 3),
            'delay_seconds' => env('GOOGLE_ASSISTANT_WEBHOOK_DELAY_SECONDS', 5),
        ],
    ],
];
