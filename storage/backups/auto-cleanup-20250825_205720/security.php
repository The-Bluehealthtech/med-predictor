<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configurations for the application
    | including headers, CORS, rate limiting, and other security measures.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    */
    'headers' => [
        'x-frame-options' => 'SAMEORIGIN',
        'x-content-type-options' => 'nosniff',
        'x-xss-protection' => '1; mode=block',
        'referrer-policy' => 'strict-origin-when-cross-origin',
        'permissions-policy' => 'geolocation=(), microphone=(), camera=()',
        'content-security-policy' => [
            'default-src' => ["'self'"],
            'script-src' => ["'self'", "'unsafe-inline'", "'unsafe-eval'", 'https://cdn.jsdelivr.net'],
            'style-src' => ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com'],
            'font-src' => ["'self'", 'https://fonts.gstatic.com'],
            'img-src' => ["'self'", 'data:', 'https:'],
            'connect-src' => ["'self'", 'https://api.fifa.com', 'https://fhir.example.com'],
            'frame-src' => ["'self'"],
            'object-src' => ["'none'"],
            'base-uri' => ["'self'"],
            'form-action' => ["'self'"],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Configuration
    |--------------------------------------------------------------------------
    */
    'cors' => [
        'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'https://your-domain.com')),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
        'exposed_headers' => [],
        'max_age' => 86400,
        'supports_credentials' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'enabled' => env('RATE_LIMITING_ENABLED', true),
        'default_limits' => [
            'web' => [
                'requests' => env('RATE_LIMIT_WEB_REQUESTS', 100),
                'window' => env('RATE_LIMIT_WEB_WINDOW', 60),
            ],
            'api' => [
                'requests' => env('RATE_LIMIT_API_REQUESTS', 1000),
                'window' => env('RATE_LIMIT_API_WINDOW', 3600),
            ],
            'auth' => [
                'requests' => env('RATE_LIMIT_AUTH_REQUESTS', 5),
                'window' => env('RATE_LIMIT_AUTH_WINDOW', 300),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    */
    'session' => [
        'secure' => env('SESSION_SECURE_COOKIE', true),
        'http_only' => true,
        'same_site' => env('SESSION_SAME_SITE', 'strict'),
        'lifetime' => env('SESSION_LIFETIME', 120),
        'expire_on_close' => false,
        'encrypt' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Security
    |--------------------------------------------------------------------------
    */
    'passwords' => [
        'min_length' => env('PASSWORD_MIN_LENGTH', 8),
        'require_uppercase' => env('PASSWORD_REQUIRE_UPPERCASE', true),
        'require_lowercase' => env('PASSWORD_REQUIRE_LOWERCASE', true),
        'require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),
        'require_symbols' => env('PASSWORD_REQUIRE_SYMBOLS', true),
        'history_count' => env('PASSWORD_HISTORY_COUNT', 5),
        'expire_days' => env('PASSWORD_EXPIRE_DAYS', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication
    |--------------------------------------------------------------------------
    */
    '2fa' => [
        'enabled' => env('2FA_ENABLED', true),
        'required_for_admin' => env('2FA_REQUIRED_FOR_ADMIN', true),
        'issuer' => env('2FA_ISSUER', 'Med Predictor'),
        'algorithm' => 'sha1',
        'digits' => 6,
        'period' => 30,
        'window' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | API Security
    |--------------------------------------------------------------------------
    */
    'api' => [
        'throttle_enabled' => env('API_THROTTLE_ENABLED', true),
        'version_header' => 'X-API-Version',
        'rate_limit_by_ip' => true,
        'require_https' => env('API_REQUIRE_HTTPS', true),
        'allowed_ips' => explode(',', env('API_ALLOWED_IPS', '')),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    */
    'uploads' => [
        'max_size' => env('UPLOAD_MAX_SIZE', 10240), // 10MB
        'allowed_types' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
            'medical' => ['pdf', 'dicom', 'xml'],
        ],
        'scan_virus' => env('UPLOAD_SCAN_VIRUS', true),
        'store_encrypted' => env('UPLOAD_STORE_ENCRYPTED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'enabled' => env('AUDIT_LOGGING_ENABLED', true),
        'log_sensitive_actions' => [
            'user.login',
            'user.logout',
            'user.password_change',
            'user.role_change',
            'data.export',
            'data.import',
            'api.access',
        ],
        'retention_days' => env('AUDIT_RETENTION_DAYS', 365),
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption
    |--------------------------------------------------------------------------
    */
    'encryption' => [
        'algorithm' => 'AES-256-CBC',
        'key_rotation_days' => env('ENCRYPTION_KEY_ROTATION_DAYS', 90),
        'encrypt_sensitive_data' => [
            'player_medical_records',
            'user_passwords',
            'api_keys',
            'personal_identifiers',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring and Alerts
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'failed_login_threshold' => env('FAILED_LOGIN_THRESHOLD', 5),
        'suspicious_activity_threshold' => env('SUSPICIOUS_ACTIVITY_THRESHOLD', 10),
        'alert_channels' => [
            'email' => env('SECURITY_ALERT_EMAIL'),
            'slack' => env('SECURITY_ALERT_SLACK_WEBHOOK'),
        ],
    ],

]; 