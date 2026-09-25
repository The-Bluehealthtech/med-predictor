<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceDashboard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'dashboard_type', // federation, club, player, coach, medical
        'user_id',
        'club_id',
        'association_id',
        'federation_id',
        'is_default',
        'is_public',
        'layout_config', // JSON field for dashboard layout
        'widgets_config', // JSON field for widget configurations
        'filters_config', // JSON field for filter configurations
        'refresh_interval', // in seconds
        'last_refresh',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_public' => 'boolean',
        'layout_config' => 'array',
        'widgets_config' => 'array',
        'filters_config' => 'array',
        'last_refresh' => 'datetime',
    ];

    // Dashboard types constants
    const TYPE_FEDERATION = 'federation';
    const TYPE_CLUB = 'club';
    const TYPE_PLAYER = 'player';
    const TYPE_COACH = 'coach';
    const TYPE_MEDICAL = 'medical';
    const TYPE_REFEREE = 'referee';

    /**
     * Get the user who owns the dashboard
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the club associated with the dashboard
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the association associated with the dashboard
     */
    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }

    /**
     * Get the federation associated with the dashboard
     */
    public function federation(): BelongsTo
    {
        return $this->belongsTo(Federation::class);
    }

    /**
     * Get the user who created the dashboard
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get dashboard widgets
     */
    public function widgets(): HasMany
    {
        return $this->hasMany(DashboardWidget::class);
    }

    /**
     * Get dashboard filters
     */
    public function filters(): HasMany
    {
        return $this->hasMany(DashboardFilter::class);
    }

    /**
     * Get dashboard alerts
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(DashboardAlert::class);
    }

    /**
     * Scope for federation dashboards
     */
    public function scopeFederation($query)
    {
        return $query->where('dashboard_type', self::TYPE_FEDERATION);
    }

    /**
     * Scope for club dashboards
     */
    public function scopeClub($query)
    {
        return $query->where('dashboard_type', self::TYPE_CLUB);
    }

    /**
     * Scope for player dashboards
     */
    public function scopePlayer($query)
    {
        return $query->where('dashboard_type', self::TYPE_PLAYER);
    }

    /**
     * Scope for coach dashboards
     */
    public function scopeCoach($query)
    {
        return $query->where('dashboard_type', self::TYPE_COACH);
    }

    /**
     * Scope for medical dashboards
     */
    public function scopeMedical($query)
    {
        return $query->where('dashboard_type', self::TYPE_MEDICAL);
    }

    /**
     * Scope for default dashboards
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for public dashboards
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get dashboard type options
     */
    public static function getDashboardTypes(): array
    {
        return [
            self::TYPE_FEDERATION => 'Federation',
            self::TYPE_CLUB => 'Club',
            self::TYPE_PLAYER => 'Player',
            self::TYPE_COACH => 'Coach',
            self::TYPE_MEDICAL => 'Medical',
            self::TYPE_REFEREE => 'Referee',
        ];
    }

    /**
     * Get default widgets for dashboard type
     */
    public function getDefaultWidgets(): array
    {
        $defaultWidgets = [
            self::TYPE_FEDERATION => [
                'performance_overview',
                'club_performance_ranking',
                'player_registration_stats',
                'transfer_activity',
                'medical_alerts',
                'compliance_status',
            ],
            self::TYPE_CLUB => [
                'team_performance',
                'player_performance_ranking',
                'injury_risk_alerts',
                'training_load',
                'match_results',
                'player_availability',
            ],
            self::TYPE_PLAYER => [
                'personal_performance',
                'fitness_metrics',
                'match_statistics',
                'training_progress',
                'medical_status',
                'career_milestones',
            ],
            self::TYPE_COACH => [
                'team_performance',
                'player_performance',
                'tactical_analysis',
                'training_effectiveness',
                'opponent_analysis',
                'player_development',
            ],
            self::TYPE_MEDICAL => [
                'injury_risk_assessment',
                'medical_alerts',
                'player_health_status',
                'recovery_progress',
                'medical_compliance',
                'health_trends',
            ],
            self::TYPE_REFEREE => [
                'match_assignments',
                'performance_metrics',
                'decision_accuracy',
                'fitness_status',
                'training_schedule',
                'career_progression',
            ],
        ];

        return $defaultWidgets[$this->dashboard_type] ?? [];
    }

    /**
     * Get default layout for dashboard type
     */
    public function getDefaultLayout(): array
    {
        $defaultLayouts = [
            self::TYPE_FEDERATION => [
                'columns' => 3,
                'rows' => 4,
                'widgets' => [
                    ['id' => 'performance_overview', 'x' => 0, 'y' => 0, 'w' => 3, 'h' => 1],
                    ['id' => 'club_performance_ranking', 'x' => 0, 'y' => 1, 'w' => 2, 'h' => 2],
                    ['id' => 'player_registration_stats', 'x' => 2, 'y' => 1, 'w' => 1, 'h' => 1],
                    ['id' => 'transfer_activity', 'x' => 2, 'y' => 2, 'w' => 1, 'h' => 1],
                    ['id' => 'medical_alerts', 'x' => 0, 'y' => 3, 'w' => 2, 'h' => 1],
                    ['id' => 'compliance_status', 'x' => 2, 'y' => 3, 'w' => 1, 'h' => 1],
                ],
            ],
            self::TYPE_CLUB => [
                'columns' => 3,
                'rows' => 4,
                'widgets' => [
                    ['id' => 'team_performance', 'x' => 0, 'y' => 0, 'w' => 3, 'h' => 1],
                    ['id' => 'player_performance_ranking', 'x' => 0, 'y' => 1, 'w' => 2, 'h' => 2],
                    ['id' => 'injury_risk_alerts', 'x' => 2, 'y' => 1, 'w' => 1, 'h' => 1],
                    ['id' => 'training_load', 'x' => 2, 'y' => 2, 'w' => 1, 'h' => 1],
                    ['id' => 'match_results', 'x' => 0, 'y' => 3, 'w' => 2, 'h' => 1],
                    ['id' => 'player_availability', 'x' => 2, 'y' => 3, 'w' => 1, 'h' => 1],
                ],
            ],
            self::TYPE_PLAYER => [
                'columns' => 2,
                'rows' => 4,
                'widgets' => [
                    ['id' => 'personal_performance', 'x' => 0, 'y' => 0, 'w' => 2, 'h' => 1],
                    ['id' => 'fitness_metrics', 'x' => 0, 'y' => 1, 'w' => 1, 'h' => 1],
                    ['id' => 'match_statistics', 'x' => 1, 'y' => 1, 'w' => 1, 'h' => 1],
                    ['id' => 'training_progress', 'x' => 0, 'y' => 2, 'w' => 1, 'h' => 1],
                    ['id' => 'medical_status', 'x' => 1, 'y' => 2, 'w' => 1, 'h' => 1],
                    ['id' => 'career_milestones', 'x' => 0, 'y' => 3, 'w' => 2, 'h' => 1],
                ],
            ],
        ];

        return $defaultLayouts[$this->dashboard_type] ?? [
            'columns' => 3,
            'rows' => 4,
            'widgets' => [],
        ];
    }

    /**
     * Initialize dashboard with default configuration
     */
    public function initializeWithDefaults(): void
    {
        if (empty($this->layout_config)) {
            $this->layout_config = $this->getDefaultLayout();
        }

        if (empty($this->widgets_config)) {
            $this->widgets_config = $this->getDefaultWidgets();
        }

        if (empty($this->filters_config)) {
            $this->filters_config = $this->getDefaultFilters();
        }

        $this->save();
    }

    /**
     * Get default filters for dashboard type
     */
    public function getDefaultFilters(): array
    {
        $defaultFilters = [
            self::TYPE_FEDERATION => [
                'date_range' => 'last_30_days',
                'clubs' => 'all',
                'competitions' => 'all',
                'player_age_groups' => 'all',
            ],
            self::TYPE_CLUB => [
                'date_range' => 'last_30_days',
                'teams' => 'all',
                'players' => 'all',
                'competitions' => 'all',
            ],
            self::TYPE_PLAYER => [
                'date_range' => 'last_30_days',
                'metric_types' => 'all',
                'data_sources' => 'all',
            ],
        ];

        return $defaultFilters[$this->dashboard_type] ?? [];
    }

    /**
     * Check if dashboard needs refresh
     */
    public function needsRefresh(): bool
    {
        if (!$this->refresh_interval) {
            return false;
        }

        return !$this->last_refresh || 
               $this->last_refresh->addSeconds($this->refresh_interval)->isPast();
    }

    /**
     * Update last refresh timestamp
     */
    public function markAsRefreshed(): void
    {
        $this->update(['last_refresh' => now()]);
    }

    /**
     * Get dashboard display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: ucfirst($this->dashboard_type) . ' Dashboard';
    }
} 