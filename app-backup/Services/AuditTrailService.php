<?php

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditTrailService
{
    /**
     * Log a user action
     */
    public static function logUserAction(string $action, string $description, array $metadata = []): AuditTrail
    {
        return AuditTrail::logUserAction($action, $description, $metadata);
    }

    /**
     * Log a security event
     */
    public static function logSecurityEvent(string $action, string $description, string $severity = 'warning', array $metadata = []): AuditTrail
    {
        return AuditTrail::logSecurityEvent($action, $description, $severity, $metadata);
    }

    /**
     * Log data access
     */
    public static function logDataAccess(string $action, string $description, ?string $modelType = null, ?int $modelId = null, array $metadata = []): AuditTrail
    {
        return AuditTrail::logDataAccess($action, $description, $modelType, $modelId, $metadata);
    }

    /**
     * Log model changes (create, update, delete)
     */
    public static function logModelChange(string $action, Model $model, ?array $oldValues = null, ?array $newValues = null, ?string $description = null): AuditTrail
    {
        return AuditTrail::logModelChange($action, $model, $oldValues, $newValues, $description);
    }

    /**
     * Log a system event
     */
    public static function logSystemEvent(string $action, string $description, string $severity = 'info', array $metadata = []): AuditTrail
    {
        return AuditTrail::logSystemEvent($action, $description, $severity, $metadata);
    }

    /**
     * Log user login
     */
    public static function logLogin(User $user, bool $success = true, ?string $reason = null): AuditTrail
    {
        $description = $success 
            ? "User {$user->name} logged in successfully"
            : "Failed login attempt for user {$user->name}";

        $metadata = [
            'user_email' => $user->email,
            'user_role' => $user->role,
            'success' => $success,
        ];

        if ($reason) {
            $metadata['reason'] = $reason;
        }

        return self::logSecurityEvent(
            'login',
            $description,
            $success ? 'info' : 'warning',
            $metadata
        );
    }

    /**
     * Log user logout
     */
    public static function logLogout(User $user): AuditTrail
    {
        return self::logUserAction(
            'logout',
            "User {$user->name} logged out",
            [
                'user_email' => $user->email,
                'user_role' => $user->role,
            ]
        );
    }

    /**
     * Log failed login attempt
     */
    public static function logFailedLogin(string $email, ?string $reason = null): AuditTrail
    {
        $description = "Failed login attempt for email: {$email}";
        
        $metadata = [
            'attempted_email' => $email,
        ];

        if ($reason) {
            $metadata['reason'] = $reason;
        }

        return self::logSecurityEvent(
            'login_failed',
            $description,
            'warning',
            $metadata
        );
    }

    /**
     * Log password change
     */
    public static function logPasswordChange(User $user): AuditTrail
    {
        return self::logSecurityEvent(
            'password_change',
            "User {$user->name} changed their password",
            'info',
            [
                'user_email' => $user->email,
                'user_role' => $user->role,
            ]
        );
    }

    /**
     * Log role assignment
     */
    public static function logRoleAssignment(User $user, string $oldRole, string $newRole): AuditTrail
    {
        return self::logSecurityEvent(
            'role_assignment',
            "User {$user->name} role changed from {$oldRole} to {$newRole}",
            'warning',
            [
                'user_email' => $user->email,
                'old_role' => $oldRole,
                'new_role' => $newRole,
            ]
        );
    }

    /**
     * Log data export
     */
    public static function logDataExport(User $user, string $dataType, ?int $recordCount = null): AuditTrail
    {
        $description = "User {$user->name} exported {$dataType} data";
        
        $metadata = [
            'user_email' => $user->email,
            'user_role' => $user->role,
            'data_type' => $dataType,
        ];

        if ($recordCount) {
            $metadata['record_count'] = $recordCount;
        }

        return self::logDataAccess(
            'export',
            $description,
            null,
            null,
            $metadata
        );
    }

    /**
     * Log data import
     */
    public static function logDataImport(User $user, string $dataType, int $recordCount, bool $success = true): AuditTrail
    {
        $description = $success 
            ? "User {$user->name} successfully imported {$recordCount} {$dataType} records"
            : "User {$user->name} failed to import {$dataType} data";

        $metadata = [
            'user_email' => $user->email,
            'user_role' => $user->role,
            'data_type' => $dataType,
            'record_count' => $recordCount,
            'success' => $success,
        ];

        return self::logDataAccess(
            'import',
            $description,
            null,
            null,
            $metadata
        );
    }

    /**
     * Log sensitive data access
     */
    public static function logSensitiveDataAccess(User $user, string $dataType, ?int $recordId = null): AuditTrail
    {
        $description = "User {$user->name} accessed sensitive {$dataType} data";
        
        $metadata = [
            'user_email' => $user->email,
            'user_role' => $user->role,
            'data_type' => $dataType,
        ];

        if ($recordId) {
            $metadata['record_id'] = $recordId;
        }

        return self::logSecurityEvent(
            'sensitive_data_access',
            $description,
            'warning',
            $metadata
        );
    }

    /**
     * Log configuration change
     */
    public static function logConfigurationChange(User $user, string $configKey, $oldValue, $newValue): AuditTrail
    {
        return self::logSystemEvent(
            'config_change',
            "User {$user->name} changed configuration {$configKey}",
            'info',
            [
                'user_email' => $user->email,
                'user_role' => $user->role,
                'config_key' => $configKey,
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ]
        );
    }

    /**
     * Log system maintenance
     */
    public static function logSystemMaintenance(string $action, string $description, bool $success = true): AuditTrail
    {
        $metadata = [
            'success' => $success,
        ];

        return self::logSystemEvent(
            $action,
            $description,
            $success ? 'info' : 'error',
            $metadata
        );
    }

    /**
     * Log API access
     */
    public static function logApiAccess(User $user, string $endpoint, string $method, bool $success = true): AuditTrail
    {
        $description = "User {$user->name} accessed API endpoint {$method} {$endpoint}";
        
        $metadata = [
            'user_email' => $user->email,
            'user_role' => $user->role,
            'endpoint' => $endpoint,
            'method' => $method,
            'success' => $success,
        ];

        return self::logDataAccess(
            'api_access',
            $description,
            null,
            null,
            $metadata
        );
    }

    /**
     * Log bulk operation
     */
    public static function logBulkOperation(User $user, string $operation, string $dataType, int $recordCount, bool $success = true): AuditTrail
    {
        $description = $success 
            ? "User {$user->name} successfully performed bulk {$operation} on {$recordCount} {$dataType} records"
            : "User {$user->name} failed to perform bulk {$operation} on {$dataType} records";

        $metadata = [
            'user_email' => $user->email,
            'user_role' => $user->role,
            'operation' => $operation,
            'data_type' => $dataType,
            'record_count' => $recordCount,
            'success' => $success,
        ];

        return self::logDataAccess(
            'bulk_operation',
            $description,
            null,
            null,
            $metadata
        );
    }

    public function log($action, $description, $subject = null, $user = null, $oldData = null)
    {
        // You can implement actual logging logic here
        // For now, just return true for test purposes
        return true;
    }

    public function logPlayerUpdated($user, $player, $oldData)
    {
        // Log player update event
        // You can customize the details as needed
        // Example:
        // $this->log('player_updated', $user, ['player_id' => $player->id, 'old' => $oldData, 'new' => $player->toArray()]);
    }

    public function logPlayerDeleted($user, $playerData)
    {
        // Log player delete event
        // Example:
        // $this->log('player_deleted', $user, ['player' => $playerData]);
    }

    public function logPlayerCreated($user, $player)
    {
        // Log player create event
        // Example:
        // $this->log('player_created', $user, ['player_id' => $player->id, 'data' => $player->toArray()]);
    }

    public function logClubCreated($user, $club)
    {
        return self::logModelChange('created', $club, null, $club->toArray(), "Club created by {$user->name}");
    }

    public function logClubUpdated($user, $club, $oldData)
    {
        return self::logModelChange('updated', $club, $oldData, $club->toArray(), "Club updated by {$user->name}");
    }

    public function logClubDeleted($user, $clubData)
    {
        return self::logSystemEvent('deleted', "Club deleted by {$user->name}", 'warning', $clubData);
    }

    public function logMatchCreated($user, $match)
    {
        return self::logModelChange('created', $match, null, $match->toArray(), "Match created by {$user->name}");
    }

    public function logMatchUpdated($user, $match, $oldData)
    {
        return self::logModelChange('updated', $match, $oldData, $match->toArray(), "Match updated by {$user->name}");
    }

    public function logMatchDeleted($user, $matchData)
    {
        return self::logSystemEvent('deleted', "Match deleted by {$user->name}", 'warning', $matchData);
    }

    public function logMatchEventCreated($user, $event)
    {
        return self::logModelChange('created', $event, null, $event->toArray(), "Match event created by {$user->name}");
    }

    public function logMatchStatusUpdated($user, $match, $oldStatus)
    {
        return self::logModelChange('updated', $match, ['status' => $oldStatus], ['status' => $match->status], "Match status updated from {$oldStatus} to {$match->status} by {$user->name}");
    }
} 