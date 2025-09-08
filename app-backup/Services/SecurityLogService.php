<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class SecurityLogService
{
    /**
     * Log une action de sécurité
     */
    public static function logSecurityAction(string $action, array $data = [], ?Request $request = null): void
    {
        $logData = [
            'action' => $action,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()?->role,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'data' => $data
        ];

        Log::channel('security')->info('Security action', $logData);
    }

    /**
     * Log une tentative d'accès non autorisé
     */
    public static function logUnauthorizedAccess(string $resource, array $context = [], ?Request $request = null): void
    {
        $logData = [
            'resource' => $resource,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()?->role,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'context' => $context
        ];

        Log::channel('security')->warning('Unauthorized access attempt', $logData);
    }

    /**
     * Log une action de licence
     */
    public static function logLicenseAction(string $action, int $licenseId, array $data = [], ?Request $request = null): void
    {
        $logData = [
            'action' => $action,
            'license_id' => $licenseId,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()?->role,
            'ip_address' => $request?->ip(),
            'data' => $data
        ];

        Log::channel('licenses')->info('License action', $logData);
    }

    /**
     * Log une erreur de sécurité
     */
    public static function logSecurityError(string $error, array $context = [], ?Request $request = null): void
    {
        $logData = [
            'error' => $error,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()?->role,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'context' => $context
        ];

        Log::channel('security')->error('Security error', $logData);
    }

    /**
     * Log une tentative de rate limiting
     */
    public static function logRateLimitExceeded(string $endpoint, array $context = [], ?Request $request = null): void
    {
        $logData = [
            'endpoint' => $endpoint,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()?->role,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'context' => $context
        ];

        Log::channel('security')->warning('Rate limit exceeded', $logData);
    }

    /**
     * Log une action de fichier
     */
    public static function logFileAction(string $action, string $filePath, array $context = [], ?Request $request = null): void
    {
        $logData = [
            'action' => $action,
            'file_path' => $filePath,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()?->role,
            'ip_address' => $request?->ip(),
            'context' => $context
        ];

        Log::channel('files')->info('File action', $logData);
    }
} 