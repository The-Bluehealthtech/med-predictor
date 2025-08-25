<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'ai-testing/*',
        'whisper/*',
        'gemini/*',
        'api/v1/licenses/fraud-detection/*',
        'api/v1/association/fraud-detection/*',
        'api/v1/clinical/*',
    ];
} 