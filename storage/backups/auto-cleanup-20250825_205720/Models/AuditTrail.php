<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AuditTrail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'table_name',
        'event_type',
        'severity',
        'description',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
        'session_id',
        'request_method',
        'request_url',
        'request_id',
        'occurred_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes for filtering
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        return $query;
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('occurred_at', [$startDate, $endDate]);
    }

    public function scopeByIpAddress($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    public function scopeSecurityEvents($query)
    {
        return $query->where('event_type', 'security_event');
    }

    public function scopeDataAccess($query)
    {
        return $query->where('event_type', 'data_access');
    }

    public function scopeUserActions($query)
    {
        return $query->where('event_type', 'user_action');
    }

    public function scopeSystemEvents($query)
    {
        return $query->where('event_type', 'system_event');
    }

    // Helper methods
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'red',
            'error' => 'orange',
            'warning' => 'yellow',
            'info' => 'blue',
            default => 'gray'
        };
    }

    public function getEventTypeLabelAttribute(): string
    {
        return match($this->event_type) {
            'user_action' => 'User Action',
            'system_event' => 'System Event',
            'security_event' => 'Security Event',
            'data_access' => 'Data Access',
            default => ucfirst(str_replace('_', ' ', $this->event_type))
        };
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'view' => 'Viewed',
            'export' => 'Exported',
            'import' => 'Imported',
            'download' => 'Downloaded',
            'upload' => 'Uploaded',
            'approve' => 'Approved',
            'reject' => 'Rejected',
            'suspend' => 'Suspended',
            'activate' => 'Activated',
            'deactivate' => 'Deactivated',
            default => ucfirst($this->action)
        };
    }

    public function getModelNameAttribute(): string
    {
        if (!$this->model_type) {
            return 'System';
        }

        return class_basename($this->model_type);
    }

    public function getChangesSummaryAttribute(): string
    {
        if (!$this->old_values && !$this->new_values) {
            return 'No changes recorded';
        }

        $changes = [];
        
        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $field => $newValue) {
                $oldValue = $this->old_values[$field] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[] = ucfirst(str_replace('_', ' ', $field));
                }
            }
        }

        return empty($changes) ? 'No changes recorded' : implode(', ', $changes);
    }

    // Static methods for creating audit entries
    public static function logUserAction(string $action, string $description, array $metadata = []): self
    {
        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'event_type' => 'user_action',
            'severity' => 'info',
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => request()->hasSession() ? request()->session()->getId() : null,
            'request_method' => request()->method(),
            'request_url' => request()->fullUrl(),
            'request_id' => uniqid(),
            'occurred_at' => now(),
        ]);
    }

    public static function logSecurityEvent(string $action, string $description, string $severity = 'warning', array $metadata = []): self
    {
        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'event_type' => 'security_event',
            'severity' => $severity,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => request()->hasSession() ? request()->session()->getId() : null,
            'request_method' => request()->method(),
            'request_url' => request()->fullUrl(),
            'request_id' => uniqid(),
            'occurred_at' => now(),
        ]);
    }

    public static function logDataAccess(string $action, string $description, ?string $modelType = null, ?int $modelId = null, array $metadata = []): self
    {
        $tableName = null;
        if ($modelType) {
            try {
                $fullClassName = "App\\Models\\{$modelType}";
                if (class_exists($fullClassName)) {
                    $tableName = (new $fullClassName)->getTable();
                }
            } catch (\Exception $e) {
                // If we can't get the table name, just continue without it
                $tableName = null;
            }
        }

        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'table_name' => $tableName,
            'event_type' => 'data_access',
            'severity' => 'info',
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => request()->hasSession() ? request()->session()->getId() : null,
            'request_method' => request()->method(),
            'request_url' => request()->fullUrl(),
            'request_id' => uniqid(),
            'occurred_at' => now(),
        ]);
    }

    public static function logModelChange(string $action, Model $model, ?array $oldValues = null, ?array $newValues = null, ?string $description = null): self
    {
        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'table_name' => $model->getTable(),
            'event_type' => 'user_action',
            'severity' => 'info',
            'description' => $description ?? ucfirst($action) . ' ' . class_basename($model),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => [
                'model_class' => get_class($model),
                'model_table' => $model->getTable(),
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => request()->hasSession() ? request()->session()->getId() : null,
            'request_method' => request()->method(),
            'request_url' => request()->fullUrl(),
            'request_id' => uniqid(),
            'occurred_at' => now(),
        ]);
    }

    public static function logSystemEvent(string $action, string $description, string $severity = 'info', array $metadata = []): self
    {
        return self::create([
            'user_id' => null, // System events don't have a user
            'action' => $action,
            'event_type' => 'system_event',
            'severity' => $severity,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => request()->hasSession() ? request()->session()->getId() : null,
            'request_method' => request()->method(),
            'request_url' => request()->fullUrl(),
            'request_id' => uniqid(),
            'occurred_at' => now(),
        ]);
    }
}
