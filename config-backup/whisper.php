<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Whisper Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour le service de reconnaissance vocale Whisper
    | Utilisé comme fallback de Google Assistant
    |
    */

    'enabled' => env('WHISPER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Mode d'utilisation
    |--------------------------------------------------------------------------
    */
    'use_openai' => env('WHISPER_USE_OPENAI', false), // true = OpenAI API, false = Whisper local

    /*
    |--------------------------------------------------------------------------
    | Configuration OpenAI
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('WHISPER_OPENAI_API_KEY'),
        'model' => env('WHISPER_OPENAI_MODEL', 'whisper-1'),
        'language' => env('WHISPER_LANGUAGE', 'fr'),
        'response_format' => env('WHISPER_RESPONSE_FORMAT', 'json'),
        'max_file_size' => env('WHISPER_MAX_FILE_SIZE', 25), // MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration Whisper Local
    |--------------------------------------------------------------------------
    */
    'local' => [
        'path' => env('WHISPER_LOCAL_PATH', '/usr/local/bin/whisper'),
        'model' => env('WHISPER_LOCAL_MODEL', 'base'),
        'language' => env('WHISPER_LOCAL_LANGUAGE', 'fr'),
        'output_format' => env('WHISPER_LOCAL_OUTPUT_FORMAT', 'txt'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Formats audio supportés
    |--------------------------------------------------------------------------
    */
    'supported_formats' => [
        'audio/wav',
        'audio/mp3', 
        'audio/m4a',
        'audio/ogg',
        'audio/flac'
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites et contraintes
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'max_file_size' => env('WHISPER_MAX_FILE_SIZE', 25), // MB
        'max_duration' => env('WHISPER_MAX_DURATION', 300), // secondes (5 minutes)
        'max_concurrent' => env('WHISPER_MAX_CONCURRENT', 5), // requêtes simultanées
    ],

    /*
    |--------------------------------------------------------------------------
    | Qualité et performance
    |--------------------------------------------------------------------------
    */
    'quality' => [
        'min_confidence' => env('WHISPER_MIN_CONFIDENCE', 0.7),
        'enable_vad' => env('WHISPER_ENABLE_VAD', true), // Voice Activity Detection
        'noise_reduction' => env('WHISPER_NOISE_REDUCTION', true),
        'speaker_diarization' => env('WHISPER_SPEAKER_DIARIZATION', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback et gestion d'erreurs
    |--------------------------------------------------------------------------
    */
    'fallback' => [
        'enabled' => env('WHISPER_FALLBACK_ENABLED', true),
        'max_retries' => env('WHISPER_MAX_RETRIES', 3),
        'retry_delay' => env('WHISPER_RETRY_DELAY', 1), // secondes
        'fallback_to_web' => env('WHISPER_FALLBACK_TO_WEB', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stockage et nettoyage
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'temp_directory' => env('WHISPER_TEMP_DIR', 'temp/whisper'),
        'cleanup_after' => env('WHISPER_CLEANUP_AFTER', 3600), // secondes (1 heure)
        'max_temp_files' => env('WHISPER_MAX_TEMP_FILES', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging et monitoring
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('WHISPER_LOGGING_ENABLED', true),
        'level' => env('WHISPER_LOG_LEVEL', 'info'),
        'log_transcriptions' => env('WHISPER_LOG_TRANSCRIPTIONS', false),
        'log_audio_metadata' => env('WHISPER_LOG_AUDIO_METADATA', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Intégration avec Google Assistant
    |--------------------------------------------------------------------------
    */
    'google_assistant_integration' => [
        'auto_fallback' => env('WHISPER_GA_AUTO_FALLBACK', true),
        'fallback_threshold' => env('WHISPER_GA_FALLBACK_THRESHOLD', 0.5), // Seuil de confiance
        'seamless_transition' => env('WHISPER_GA_SEAMLESS_TRANSITION', true),
        'maintain_context' => env('WHISPER_GA_MAINTAIN_CONTEXT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Langues supportées
    |--------------------------------------------------------------------------
    */
    'languages' => [
        'fr' => [
            'name' => 'Français',
            'code' => 'fr',
            'models' => ['whisper-1', 'base', 'small', 'medium', 'large'],
            'default_model' => 'whisper-1'
        ],
        'en' => [
            'name' => 'English',
            'code' => 'en',
            'models' => ['whisper-1', 'base', 'small', 'medium', 'large'],
            'default_model' => 'whisper-1'
        ],
        'ar' => [
            'name' => 'العربية',
            'code' => 'ar',
            'models' => ['whisper-1', 'large'],
            'default_model' => 'whisper-1'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Modèles disponibles
    |--------------------------------------------------------------------------
    */
    'models' => [
        'tiny' => [
            'name' => 'Tiny',
            'size' => '39 MB',
            'languages' => ['fr', 'en'],
            'speed' => 'fast',
            'accuracy' => 'low'
        ],
        'base' => [
            'name' => 'Base',
            'size' => '74 MB',
            'languages' => ['fr', 'en'],
            'speed' => 'fast',
            'accuracy' => 'medium'
        ],
        'small' => [
            'name' => 'Small',
            'size' => '244 MB',
            'languages' => ['fr', 'en'],
            'speed' => 'medium',
            'accuracy' => 'medium'
        ],
        'medium' => [
            'name' => 'Medium',
            'size' => '769 MB',
            'languages' => ['fr', 'en', 'ar'],
            'speed' => 'slow',
            'accuracy' => 'high'
        ],
        'large' => [
            'name' => 'Large',
            'size' => '1550 MB',
            'languages' => ['fr', 'en', 'ar'],
            'speed' => 'slow',
            'accuracy' => 'very_high'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Développement et tests
    |--------------------------------------------------------------------------
    */
    'development' => [
        'enabled' => env('WHISPER_DEV_MODE', false),
        'mock_responses' => env('WHISPER_MOCK_RESPONSES', false),
        'test_audio_path' => env('WHISPER_TEST_AUDIO_PATH', 'tests/audio'),
        'debug_transcription' => env('WHISPER_DEBUG_TRANSCRIPTION', false),
    ],
];
