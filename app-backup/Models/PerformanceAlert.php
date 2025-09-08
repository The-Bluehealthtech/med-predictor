<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceAlert extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'alert_type', // injury_risk, performance_decline, medical_alert, compliance_alert, transfer_alert
        'alert_level', // low, medium, high, critical
        'title',
        'description',
        'player_id',
        'club_id',
        'team_id',
        'metric_id',
        'dashboard_id',
        'trigger_value',
        'threshold_value',
        'alert_condition', // above, below, equals, trend
        'is_active',
        'is_acknowledged',
        'acknowledged_by',
        'acknowledged_at',
        'is_resolved',
        'resolved_by',
        'resolved_at',
        'resolution_notes',
        'notification_sent',
        'notification_channels', // email, sms, push, webhook
        'ai_recommendation',
        'metadata', // JSON field for additional data
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_acknowledged' => 'boolean',
        'acknowledged_at' => 'datetime',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'notification_sent' => 'boolean',
        'notification_channels' => 'array',
        'metadata' => 'array',
    ];

    // Alert types constants
    const TYPE_INJURY_RISK = 'injury_risk';
    const TYPE_PERFORMANCE_DECLINE = 'performance_decline';
    const TYPE_MEDICAL_ALERT = 'medical_alert';
    const TYPE_COMPLIANCE_ALERT = 'compliance_alert';
    const TYPE_TRANSFER_ALERT = 'transfer_alert';
    const TYPE_FITNESS_ALERT = 'fitness_alert';
    const TYPE_TACTICAL_ALERT = 'tactical_alert';
    const TYPE_SOCIAL_ALERT = 'social_alert';

    // Alert levels constants
    const LEVEL_LOW = 'low';
    const LEVEL_MEDIUM = 'medium';
    const LEVEL_HIGH = 'high';
    const LEVEL_CRITICAL = 'critical';

    // Alert conditions constants
    const CONDITION_ABOVE = 'above';
    const CONDITION_BELOW = 'below';
    const CONDITION_EQUALS = 'equals';
    const CONDITION_TREND = 'trend';
    const CONDITION_CHANGE = 'change';

    /**
     * Get the player associated with the alert
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the club associated with the alert
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the team associated with the alert
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the performance metric that triggered the alert
     */
    public function metric(): BelongsTo
    {
        return $this->belongsTo(PerformanceMetric::class, 'metric_id');
    }

    /**
     * Get the dashboard associated with the alert
     */
    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(PerformanceDashboard::class, 'dashboard_id');
    }

    /**
     * Get the user who acknowledged the alert
     */
    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Get the user who resolved the alert
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get the user who created the alert
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get alert notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(AlertNotification::class);
    }

    /**
     * Get alert actions taken
     */
    public function actions(): HasMany
    {
        return $this->hasMany(AlertAction::class);
    }

    /**
     * Scope for active alerts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for unacknowledged alerts
     */
    public function scopeUnacknowledged($query)
    {
        return $query->where('is_acknowledged', false);
    }

    /**
     * Scope for unresolved alerts
     */
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    /**
     * Scope for critical alerts
     */
    public function scopeCritical($query)
    {
        return $query->where('alert_level', self::LEVEL_CRITICAL);
    }

    /**
     * Scope for high priority alerts
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('alert_level', [self::LEVEL_HIGH, self::LEVEL_CRITICAL]);
    }

    /**
     * Scope for injury risk alerts
     */
    public function scopeInjuryRisk($query)
    {
        return $query->where('alert_type', self::TYPE_INJURY_RISK);
    }

    /**
     * Scope for medical alerts
     */
    public function scopeMedical($query)
    {
        return $query->where('alert_type', self::TYPE_MEDICAL_ALERT);
    }

    /**
     * Scope for recent alerts (last 7 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7));
    }

    /**
     * Get alert type options
     */
    public static function getAlertTypes(): array
    {
        return [
            self::TYPE_INJURY_RISK => 'Injury Risk',
            self::TYPE_PERFORMANCE_DECLINE => 'Performance Decline',
            self::TYPE_MEDICAL_ALERT => 'Medical Alert',
            self::TYPE_COMPLIANCE_ALERT => 'Compliance Alert',
            self::TYPE_TRANSFER_ALERT => 'Transfer Alert',
            self::TYPE_FITNESS_ALERT => 'Fitness Alert',
            self::TYPE_TACTICAL_ALERT => 'Tactical Alert',
            self::TYPE_SOCIAL_ALERT => 'Social Alert',
        ];
    }

    /**
     * Get alert level options
     */
    public static function getAlertLevels(): array
    {
        return [
            self::LEVEL_LOW => 'Low',
            self::LEVEL_MEDIUM => 'Medium',
            self::LEVEL_HIGH => 'High',
            self::LEVEL_CRITICAL => 'Critical',
        ];
    }

    /**
     * Get alert condition options
     */
    public static function getAlertConditions(): array
    {
        return [
            self::CONDITION_ABOVE => 'Above Threshold',
            self::CONDITION_BELOW => 'Below Threshold',
            self::CONDITION_EQUALS => 'Equals Value',
            self::CONDITION_TREND => 'Trend Analysis',
            self::CONDITION_CHANGE => 'Change Detection',
        ];
    }

    /**
     * Get notification channel options
     */
    public static function getNotificationChannels(): array
    {
        return [
            'email' => 'Email',
            'sms' => 'SMS',
            'push' => 'Push Notification',
            'webhook' => 'Webhook',
            'in_app' => 'In-App Notification',
        ];
    }

    /**
     * Acknowledge the alert
     */
    public function acknowledge(User $user, string $notes = null): void
    {
        $this->update([
            'is_acknowledged' => true,
            'acknowledged_by' => $user->id,
            'acknowledged_at' => now(),
            'resolution_notes' => $notes,
        ]);
    }

    /**
     * Resolve the alert
     */
    public function resolve(User $user, string $notes = null): void
    {
        $this->update([
            'is_resolved' => true,
            'resolved_by' => $user->id,
            'resolved_at' => now(),
            'resolution_notes' => $notes,
        ]);
    }

    /**
     * Send notification for this alert
     */
    public function sendNotification(): bool
    {
        if ($this->notification_sent) {
            return false;
        }

        $channels = $this->notification_channels ?? ['email'];
        
        foreach ($channels as $channel) {
            $this->sendNotificationToChannel($channel);
        }

        $this->update(['notification_sent' => true]);
        
        return true;
    }

    /**
     * Send notification to specific channel
     */
    protected function sendNotificationToChannel(string $channel): void
    {
        switch ($channel) {
            case 'email':
                $this->sendEmailNotification();
                break;
            case 'sms':
                $this->sendSmsNotification();
                break;
            case 'push':
                $this->sendPushNotification();
                break;
            case 'webhook':
                $this->sendWebhookNotification();
                break;
            case 'in_app':
                $this->sendInAppNotification();
                break;
        }
    }

    /**
     * Send email notification
     */
    protected function sendEmailNotification(): void
    {
        // Implementation for email notification
        // This would integrate with Laravel's notification system
    }

    /**
     * Send SMS notification
     */
    protected function sendSmsNotification(): void
    {
        // Implementation for SMS notification
    }

    /**
     * Send push notification
     */
    protected function sendPushNotification(): void
    {
        // Implementation for push notification
    }

    /**
     * Send webhook notification
     */
    protected function sendWebhookNotification(): void
    {
        // Implementation for webhook notification
    }

    /**
     * Send in-app notification
     */
    protected function sendInAppNotification(): void
    {
        // Implementation for in-app notification
    }

    /**
     * Get alert priority score
     */
    public function getPriorityScore(): int
    {
        $scores = [
            self::LEVEL_LOW => 1,
            self::LEVEL_MEDIUM => 2,
            self::LEVEL_HIGH => 3,
            self::LEVEL_CRITICAL => 4,
        ];

        return $scores[$this->alert_level] ?? 1;
    }

    /**
     * Check if alert is urgent
     */
    public function isUrgent(): bool
    {
        return in_array($this->alert_level, [self::LEVEL_HIGH, self::LEVEL_CRITICAL]);
    }

    /**
     * Get alert status
     */
    public function getStatusAttribute(): string
    {
        if ($this->is_resolved) {
            return 'resolved';
        } elseif ($this->is_acknowledged) {
            return 'acknowledged';
        } else {
            return 'active';
        }
    }

    /**
     * Get alert color based on level
     */
    public function getAlertColorAttribute(): string
    {
        $colors = [
            self::LEVEL_LOW => 'green',
            self::LEVEL_MEDIUM => 'yellow',
            self::LEVEL_HIGH => 'orange',
            self::LEVEL_CRITICAL => 'red',
        ];

        return $colors[$this->alert_level] ?? 'gray';
    }
} 