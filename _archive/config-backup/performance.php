<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains performance-related configurations for the application
    | including caching, queue settings, and optimization parameters.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    */
    'caching' => [
        'default_ttl' => env('CACHE_DEFAULT_TTL', 3600),
        'long_ttl' => env('CACHE_LONG_TTL', 86400),
        'short_ttl' => env('CACHE_SHORT_TTL', 300),
        
        'strategies' => [
            'database_queries' => [
                'enabled' => env('CACHE_DB_QUERIES', true),
                'ttl' => env('CACHE_DB_TTL', 1800),
            ],
            'api_responses' => [
                'enabled' => env('CACHE_API_RESPONSES', true),
                'ttl' => env('CACHE_API_TTL', 3600),
            ],
            'views' => [
                'enabled' => env('CACHE_VIEWS', true),
                'ttl' => env('CACHE_VIEWS_TTL', 7200),
            ],
            'routes' => [
                'enabled' => env('CACHE_ROUTES', true),
            ],
            'config' => [
                'enabled' => env('CACHE_CONFIG', true),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    */
    'queues' => [
        'default' => [
            'connection' => env('QUEUE_CONNECTION', 'redis'),
            'workers' => env('QUEUE_WORKERS', 4),
            'timeout' => env('QUEUE_TIMEOUT', 60),
            'retry_after' => env('QUEUE_RETRY_AFTER', 90),
            'max_tries' => env('QUEUE_MAX_TRIES', 3),
        ],
        
        'high' => [
            'connection' => env('QUEUE_HIGH_CONNECTION', 'redis'),
            'workers' => env('QUEUE_HIGH_WORKERS', 2),
            'timeout' => env('QUEUE_HIGH_TIMEOUT', 30),
        ],
        
        'low' => [
            'connection' => env('QUEUE_LOW_CONNECTION', 'redis'),
            'workers' => env('QUEUE_LOW_WORKERS', 1),
            'timeout' => env('QUEUE_LOW_TIMEOUT', 300),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Optimization
    |--------------------------------------------------------------------------
    */
    'database' => [
        'connection_pooling' => env('DB_CONNECTION_POOLING', true),
        'max_connections' => env('DB_MAX_CONNECTIONS', 100),
        'min_connections' => env('DB_MIN_CONNECTIONS', 5),
        'query_timeout' => env('DB_QUERY_TIMEOUT', 30),
        
        'optimizations' => [
            'eager_loading' => true,
            'query_logging' => env('DB_QUERY_LOGGING', false),
            'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 1000),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Performance
    |--------------------------------------------------------------------------
    */
    'api' => [
        'pagination' => [
            'default_per_page' => env('API_DEFAULT_PER_PAGE', 20),
            'max_per_page' => env('API_MAX_PER_PAGE', 100),
        ],
        
        'response_compression' => env('API_RESPONSE_COMPRESSION', true),
        'response_caching' => env('API_RESPONSE_CACHING', true),
        
        'external_apis' => [
            'fifa_connect' => [
                'timeout' => env('FIFA_API_TIMEOUT', 30),
                'retry_attempts' => env('FIFA_API_RETRY_ATTEMPTS', 3),
                'cache_ttl' => env('FIFA_API_CACHE_TTL', 3600),
            ],
            'hl7_fhir' => [
                'timeout' => env('FHIR_API_TIMEOUT', 30),
                'retry_attempts' => env('FHIR_API_RETRY_ATTEMPTS', 3),
                'cache_ttl' => env('FHIR_API_CACHE_TTL', 1800),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Optimization
    |--------------------------------------------------------------------------
    */
    'assets' => [
        'minification' => env('ASSET_MINIFICATION', true),
        'compression' => env('ASSET_COMPRESSION', true),
        'versioning' => env('ASSET_VERSIONING', true),
        'cdn_enabled' => env('ASSET_CDN_ENABLED', false),
        'cdn_url' => env('ASSET_CDN_URL', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Memory Management
    |--------------------------------------------------------------------------
    */
    'memory' => [
        'max_execution_time' => env('MAX_EXECUTION_TIME', 300),
        'memory_limit' => env('MEMORY_LIMIT', '512M'),
        'upload_max_filesize' => env('UPLOAD_MAX_FILESIZE', '10M'),
        'post_max_size' => env('POST_MAX_SIZE', '10M'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Background Jobs
    |--------------------------------------------------------------------------
    */
    'jobs' => [
        'cleanup' => [
            'enabled' => env('CLEANUP_JOBS_ENABLED', true),
            'schedule' => env('CLEANUP_SCHEDULE', 'daily'),
            'retention_days' => [
                'logs' => env('LOG_RETENTION_DAYS', 30),
                'temp_files' => env('TEMP_FILE_RETENTION_DAYS', 7),
                'cache' => env('CACHE_RETENTION_DAYS', 1),
            ],
        ],
        
        'sync' => [
            'fifa_data' => [
                'enabled' => env('FIFA_SYNC_ENABLED', true),
                'schedule' => env('FIFA_SYNC_SCHEDULE', 'hourly'),
                'batch_size' => env('FIFA_SYNC_BATCH_SIZE', 100),
            ],
            'fhir_data' => [
                'enabled' => env('FHIR_SYNC_ENABLED', true),
                'schedule' => env('FHIR_SYNC_SCHEDULE', 'daily'),
                'batch_size' => env('FHIR_SYNC_BATCH_SIZE', 50),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring and Metrics
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'enabled' => env('PERFORMANCE_MONITORING', true),
        'metrics' => [
            'response_time' => true,
            'memory_usage' => true,
            'database_queries' => true,
            'cache_hit_rate' => true,
            'queue_length' => true,
        ],
        
        'alerts' => [
            'response_time_threshold' => env('RESPONSE_TIME_THRESHOLD', 2000),
            'memory_usage_threshold' => env('MEMORY_USAGE_THRESHOLD', 80),
            'error_rate_threshold' => env('ERROR_RATE_THRESHOLD', 5),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Optimization Flags
    |--------------------------------------------------------------------------
    */
    'optimizations' => [
        'opcache' => env('OPCACHE_ENABLED', true),
        'route_caching' => env('ROUTE_CACHING', true),
        'view_caching' => env('VIEW_CACHING', true),
        'config_caching' => env('CONFIG_CACHING', true),
        'query_caching' => env('QUERY_CACHING', true),
        'session_caching' => env('SESSION_CACHING', true),
    ],

]; 