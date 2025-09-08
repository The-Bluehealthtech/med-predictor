<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FIT V3 Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration spécifique pour la version 3 de FIT
    | avec Intelligence Artificielle et Machine Learning
    |
    */

    'version' => '3.0.0',
    'codename' => 'AI-Powered Football Intelligence',
    'release_date' => '2025-08-17',

    /*
    |--------------------------------------------------------------------------
    | Intelligence Artificielle et Machine Learning
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'enabled' => env('FIT_AI_ENABLED', true),
        'python_path' => env('FIT_PYTHON_PATH', '/usr/bin/python3'),
        'ml_models_path' => storage_path('app/ml-models'),
        'prediction_cache_ttl' => env('FIT_ML_CACHE_TTL', 3600),
        
        'algorithms' => [
            'performance_prediction' => [
                'enabled' => true,
                'model' => 'xgboost_performance_v1',
                'accuracy_threshold' => 0.85,
                'update_frequency' => 'daily',
            ],
            'injury_prediction' => [
                'enabled' => true,
                'model' => 'lstm_injury_v1',
                'accuracy_threshold' => 0.80,
                'update_frequency' => 'weekly',
            ],
            'market_value_prediction' => [
                'enabled' => true,
                'model' => 'random_forest_market_v1',
                'accuracy_threshold' => 0.75,
                'update_frequency' => 'monthly',
            ],
        ],

        'api' => [
            'endpoint' => env('FIT_AI_API_ENDPOINT', 'http://localhost:8002'),
            'timeout' => env('FIT_AI_API_TIMEOUT', 30),
            'retry_attempts' => env('FIT_AI_API_RETRY', 3),
            'rate_limit' => env('FIT_AI_API_RATE_LIMIT', 100),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | APIs Sportives Avancées
    |--------------------------------------------------------------------------
    */
    'apis' => [
        'fifa_tms_pro' => [
            'enabled' => env('FIFA_TMS_PRO_ENABLED', false),
            'base_url' => env('FIFA_TMS_PRO_BASE_URL'),
            'api_key' => env('FIFA_TMS_PRO_API_KEY'),
            'timeout' => env('FIFA_TMS_PRO_TIMEOUT', 30),
            'cache_ttl' => env('FIFA_TMS_PRO_CACHE_TTL', 1800),
        ],

        'transfermarkt' => [
            'enabled' => env('TRANSFERMARKT_ENABLED', false),
            'base_url' => env('TRANSFERMARKT_BASE_URL', 'https://www.transfermarkt.com'),
            'api_key' => env('TRANSFERMARKT_API_KEY'),
            'timeout' => env('TRANSFERMARKT_TIMEOUT', 15),
            'cache_ttl' => env('TRANSFERMARKT_CACHE_TTL', 3600),
        ],

        'whoscored' => [
            'enabled' => env('WHOSCORED_ENABLED', false),
            'base_url' => env('WHOSCORED_BASE_URL', 'https://api.whoscored.com'),
            'api_key' => env('WHOSCORED_API_KEY'),
            'timeout' => env('WHOSCORED_TIMEOUT', 20),
            'cache_ttl' => env('WHOSCORED_CACHE_TTL', 7200),
        ],

        'opta' => [
            'enabled' => env('OPTA_ENABLED', false),
            'base_url' => env('OPTA_BASE_URL'),
            'api_key' => env('OPTA_API_KEY'),
            'timeout' => env('OPTA_TIMEOUT', 25),
            'cache_ttl' => env('OPTA_CACHE_TTL', 3600),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Interface Utilisateur Moderne
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'pwa' => [
            'enabled' => env('FIT_PWA_ENABLED', true),
            'name' => 'FIT V3 - Football Intelligence',
            'short_name' => 'FIT V3',
            'theme_color' => '#1e40af',
            'background_color' => '#ffffff',
            'display' => 'standalone',
        ],

        'themes' => [
            'default' => 'light',
            'available' => ['light', 'dark', 'auto'],
            'custom_colors' => [
                'primary' => '#1e40af',
                'secondary' => '#64748b',
                'accent' => '#f59e0b',
                'success' => '#10b981',
                'warning' => '#f59e0b',
                'error' => '#ef4444',
            ],
        ],

        'components' => [
            'design_system' => true,
            'responsive_breakpoints' => [
                'sm' => 640,
                'md' => 768,
                'lg' => 1024,
                'xl' => 1280,
                '2xl' => 1536,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Médical Avancé
    |--------------------------------------------------------------------------
    */
    'medical' => [
        'ai_enabled' => env('FIT_MEDICAL_AI_ENABLED', true),
        'wearables_integration' => env('FIT_WEARABLES_ENABLED', false),
        
        'biometric_tracking' => [
            'heart_rate' => true,
            'sleep_quality' => true,
            'stress_level' => true,
            'recovery_score' => true,
            'fatigue_index' => true,
        ],

        'injury_prevention' => [
            'risk_assessment' => true,
            'load_management' => true,
            'recovery_monitoring' => true,
            'preventive_alerts' => true,
        ],

        'reporting' => [
            'auto_generation' => true,
            'templates' => ['medical', 'fitness', 'performance'],
            'export_formats' => ['pdf', 'html', 'json'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics et Business Intelligence
    |--------------------------------------------------------------------------
    */
    'analytics' => [
        'real_time' => env('FIT_ANALYTICS_REALTIME', true),
        'data_retention_days' => env('FIT_ANALYTICS_RETENTION', 365),
        
        'dashboards' => [
            'performance' => true,
            'medical' => true,
            'business' => true,
            'technical' => true,
            'custom' => true,
        ],

        'predictions' => [
            'market_trends' => true,
            'performance_evolution' => true,
            'injury_risks' => true,
            'transfer_opportunities' => true,
        ],

        'export' => [
            'formats' => ['csv', 'json', 'xml', 'pdf', 'excel'],
            'scheduling' => true,
            'automation' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance et Monitoring
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'cache' => [
            'driver' => env('FIT_CACHE_DRIVER', 'redis'),
            'ttl' => env('FIT_CACHE_TTL', 3600),
            'prefix' => 'fit_v3:',
        ],

        'monitoring' => [
            'enabled' => env('FIT_MONITORING_ENABLED', true),
            'metrics' => ['response_time', 'throughput', 'error_rate', 'cpu_usage'],
            'alerts' => ['high_latency', 'high_error_rate', 'low_uptime'],
        ],

        'optimization' => [
            'database_query_optimization' => true,
            'image_optimization' => true,
            'code_splitting' => true,
            'lazy_loading' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sécurité et Conformité
    |--------------------------------------------------------------------------
    */
    'security' => [
        'data_encryption' => env('FIT_DATA_ENCRYPTION', true),
        'api_rate_limiting' => env('FIT_API_RATE_LIMITING', true),
        'audit_logging' => env('FIT_AUDIT_LOGGING', true),
        
        'gdpr_compliance' => [
            'enabled' => env('FIT_GDPR_COMPLIANCE', true),
            'data_retention' => env('FIT_GDPR_RETENTION_DAYS', 2555),
            'right_to_forget' => true,
            'data_portability' => true,
        ],

        'authentication' => [
            'multi_factor' => env('FIT_MFA_ENABLED', true),
            'session_timeout' => env('FIT_SESSION_TIMEOUT', 480),
            'password_policy' => 'strong',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Développement et Tests
    |--------------------------------------------------------------------------
    */
    'development' => [
        'debug_mode' => env('FIT_DEBUG_MODE', false),
        'test_coverage_threshold' => env('FIT_TEST_COVERAGE', 90),
        
        'testing' => [
            'unit_tests' => true,
            'integration_tests' => true,
            'e2e_tests' => true,
            'performance_tests' => true,
            'security_tests' => true,
        ],

        'documentation' => [
            'api_docs' => true,
            'code_docs' => true,
            'user_manual' => true,
            'deployment_guide' => true,
        ],
    ],
];
