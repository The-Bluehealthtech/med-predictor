<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FIT API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'intégration avec l'API FIT
    |
    */

    // URL de base de l'API FIT
    'api_base_url' => env('FIT_API_BASE_URL', 'http://localhost:8000/api'),

    // Clé d'API pour l'authentification
    'api_key' => env('FIT_API_KEY', ''),

    // Timeout pour les appels API (en secondes)
    'timeout' => env('FIT_API_TIMEOUT', 30),

    // Endpoints de l'API FIT
    'endpoints' => [
        'health' => '/health',
        'pcma_submit' => '/pcma/submit',
        'pcma_get' => '/pcma/{id}',
        'pcma_update' => '/pcma/{id}',
        'pcma_delete' => '/pcma/{id}',
    ],

    // Configuration des tentatives de reconnexion
    'retry' => [
        'attempts' => env('FIT_API_RETRY_ATTEMPTS', 3),
        'delay' => env('FIT_API_RETRY_DELAY', 1000), // millisecondes
    ],

    // Configuration du cache
    'cache' => [
        'enabled' => env('FIT_API_CACHE_ENABLED', true),
        'ttl' => env('FIT_API_CACHE_TTL', 300), // secondes
    ],

    // Configuration des logs
    'logging' => [
        'enabled' => env('FIT_API_LOGGING_ENABLED', true),
        'level' => env('FIT_API_LOG_LEVEL', 'info'),
    ],

    // Configuration de la validation
    'validation' => [
        'positions' => [
            'fr' => ['attaquant', 'défenseur', 'milieu', 'gardien'],
            'en' => ['striker', 'defender', 'midfielder', 'goalkeeper'],
            'ar' => ['مهاجم', 'مدافع', 'وسط', 'حارس']
        ],
        'age_range' => [
            'min' => 1,
            'max' => 100
        ]
    ],

    // Configuration des statuts PCMA
    'pcma_statuses' => [
        'pending_medical_review' => 'En attente de révision médicale',
        'under_review' => 'En cours de révision',
        'approved' => 'Approuvé',
        'rejected' => 'Rejeté',
        'completed' => 'Terminé'
    ],

    // Configuration des sources de données
    'data_sources' => [
        'google_assistant_voice' => 'Assistant Google Vocal',
        'web_form' => 'Formulaire Web',
        'mobile_app' => 'Application Mobile',
        'api_integration' => 'Intégration API'
    ]
];
