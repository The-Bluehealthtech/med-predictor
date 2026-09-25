<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'model_type',
        'model_id',
        'model_name',
        'old_values',
        'new_values',
        'changes',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'user_id',
        'user_name',
        'user_email',
        'tenant_id',
        'module',
        'severity',
        'metadata'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changes' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Static methods for logging
    public static function log($eventType, $modelType, $action, $description = null, $data = [])
    {
        $user = auth()->user();
        
        return static::create([
            'event_type' => $eventType,
            'model_type' => $modelType,
            'model_id' => $data['model_id'] ?? null,
            'model_name' => $data['model_name'] ?? null,
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'changes' => $data['changes'] ?? null,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_email' => $user?->email,
            'tenant_id' => $user?->tenant_id,
            'module' => $data['module'] ?? null,
            'severity' => $data['severity'] ?? 'info',
            'metadata' => $data['metadata'] ?? null
        ]);
    }

    public static function logUserAction($action, $description = null, $data = [])
    {
        return static::log('user_action', User::class, $action, $description, $data);
    }

    public static function logModelChange($modelType, $action, $modelId = null, $oldValues = null, $newValues = null, $description = null)
    {
        $changes = null;
        if ($oldValues && $newValues) {
            $changes = array_diff_assoc($newValues, $oldValues);
        }

        return static::log('model_change', $modelType, $action, $description, [
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changes' => $changes
        ]);
    }

    public static function logSecurityEvent($action, $description = null, $severity = 'warning')
    {
        return static::log('security', 'security', $action, $description, [
            'severity' => $severity,
            'module' => 'security'
        ]);
    }

    public static function logSystemEvent($action, $description = null, $severity = 'info')
    {
        return static::log('system', 'system', $action, $description, [
            'severity' => $severity,
            'module' => 'system'
        ]);
    }

    // Helper methods
    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'critical' => 'red',
            'error' => 'red',
            'warning' => 'yellow',
            'info' => 'blue',
            'success' => 'green',
            default => 'gray'
        };
    }

    public function getEventTypeIconAttribute()
    {
        return match($this->event_type) {
            'created' => '➕',
            'updated' => '✏️',
            'deleted' => '🗑️',
            'accessed' => '👁️',
            'user_action' => '👤',
            'security' => '🔒',
            'system' => '⚙️',
            default => '📝'
        };
    }

    public function getActionIconAttribute()
    {
        return match($this->action) {
            'login' => '🔑',
            'logout' => '🚪',
            'create' => '➕',
            'update' => '✏️',
            'delete' => '🗑️',
            'view' => '👁️',
            'export' => '📤',
            'import' => '📥',
            default => '📝'
        };
    }
}
