<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashboardWidget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dashboard_id',
        'widget_type', // chart, metric, table, alert, progress, etc.
        'widget_name',
        'widget_title',
        'widget_description',
        'position_x',
        'position_y',
        'width',
        'height',
        'is_visible',
        'is_refreshable',
        'refresh_interval',
        'data_source', // api, database, calculation, external
        'data_config', // JSON field for data configuration
        'display_config', // JSON field for display configuration
        'filters_config', // JSON field for filters
        'last_data_update',
        'metadata', // JSON field for additional data
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'position_x' => 'integer',
        'position_y' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'is_visible' => 'boolean',
        'is_refreshable' => 'boolean',
        'refresh_interval' => 'integer',
        'data_config' => 'array',
        'display_config' => 'array',
        'filters_config' => 'array',
        'last_data_update' => 'datetime',
        'metadata' => 'array',
    ];

    // Widget types constants
    const TYPE_CHART = 'chart';
    const TYPE_METRIC = 'metric';
    const TYPE_TABLE = 'table';
    const TYPE_ALERT = 'alert';
    const TYPE_PROGRESS = 'progress';
    const TYPE_GAUGE = 'gauge';
    const TYPE_HEATMAP = 'heatmap';
    const TYPE_CALENDAR = 'calendar';
    const TYPE_MAP = 'map';
    const TYPE_LIST = 'list';

    // Data sources constants
    const SOURCE_API = 'api';
    const SOURCE_DATABASE = 'database';
    const SOURCE_CALCULATION = 'calculation';
    const SOURCE_EXTERNAL = 'external';

    /**
     * Get the dashboard that owns the widget
     */
    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(PerformanceDashboard::class, 'dashboard_id');
    }

    /**
     * Get the user who created the widget
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for visible widgets
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope for refreshable widgets
     */
    public function scopeRefreshable($query)
    {
        return $query->where('is_refreshable', true);
    }

    /**
     * Scope for chart widgets
     */
    public function scopeCharts($query)
    {
        return $query->where('widget_type', self::TYPE_CHART);
    }

    /**
     * Scope for metric widgets
     */
    public function scopeMetrics($query)
    {
        return $query->where('widget_type', self::TYPE_METRIC);
    }

    /**
     * Scope for alert widgets
     */
    public function scopeAlerts($query)
    {
        return $query->where('widget_type', self::TYPE_ALERT);
    }

    /**
     * Get widget type options
     */
    public static function getWidgetTypes(): array
    {
        return [
            self::TYPE_CHART => 'Chart',
            self::TYPE_METRIC => 'Metric',
            self::TYPE_TABLE => 'Table',
            self::TYPE_ALERT => 'Alert',
            self::TYPE_PROGRESS => 'Progress',
            self::TYPE_GAUGE => 'Gauge',
            self::TYPE_HEATMAP => 'Heatmap',
            self::TYPE_CALENDAR => 'Calendar',
            self::TYPE_MAP => 'Map',
            self::TYPE_LIST => 'List',
        ];
    }

    /**
     * Get data source options
     */
    public static function getDataSources(): array
    {
        return [
            self::SOURCE_API => 'API',
            self::SOURCE_DATABASE => 'Database',
            self::SOURCE_CALCULATION => 'Calculation',
            self::SOURCE_EXTERNAL => 'External',
        ];
    }

    /**
     * Get default widget configurations
     */
    public static function getDefaultConfigs(): array
    {
        return [
            self::TYPE_CHART => [
                'chart_type' => 'line',
                'show_legend' => true,
                'show_grid' => true,
                'responsive' => true,
            ],
            self::TYPE_METRIC => [
                'show_trend' => true,
                'show_percentage' => true,
                'color_scheme' => 'auto',
            ],
            self::TYPE_TABLE => [
                'show_pagination' => true,
                'show_search' => true,
                'sortable' => true,
                'page_size' => 10,
            ],
            self::TYPE_ALERT => [
                'show_priority' => true,
                'show_timestamp' => true,
                'max_items' => 5,
            ],
            self::TYPE_PROGRESS => [
                'show_percentage' => true,
                'show_label' => true,
                'color_scheme' => 'auto',
            ],
        ];
    }

    /**
     * Get widget data
     */
    public function getData(): array
    {
        switch ($this->data_source) {
            case self::SOURCE_API:
                return $this->getApiData();
            case self::SOURCE_DATABASE:
                return $this->getDatabaseData();
            case self::SOURCE_CALCULATION:
                return $this->getCalculationData();
            case self::SOURCE_EXTERNAL:
                return $this->getExternalData();
            default:
                return [];
        }
    }

    /**
     * Get API data
     */
    protected function getApiData(): array
    {
        $config = $this->data_config ?? [];
        $endpoint = $config['endpoint'] ?? '';
        $method = $config['method'] ?? 'GET';
        $params = $config['params'] ?? [];

        // Implementation for API data fetching
        // This would use Laravel's HTTP client
        return [];
    }

    /**
     * Get database data
     */
    protected function getDatabaseData(): array
    {
        $config = $this->data_config ?? [];
        $model = $config['model'] ?? '';
        $query = $config['query'] ?? [];
        $filters = $this->filters_config ?? [];

        // Implementation for database data fetching
        return [];
    }

    /**
     * Get calculation data
     */
    protected function getCalculationData(): array
    {
        $config = $this->data_config ?? [];
        $formula = $config['formula'] ?? '';
        $variables = $config['variables'] ?? [];

        // Implementation for calculation data
        return [];
    }

    /**
     * Get external data
     */
    protected function getExternalData(): array
    {
        $config = $this->data_config ?? [];
        $url = $config['url'] ?? '';
        $format = $config['format'] ?? 'json';

        // Implementation for external data fetching
        return [];
    }

    /**
     * Check if widget needs refresh
     */
    public function needsRefresh(): bool
    {
        if (!$this->is_refreshable || !$this->refresh_interval) {
            return false;
        }

        return !$this->last_data_update || 
               $this->last_data_update->addSeconds($this->refresh_interval)->isPast();
    }

    /**
     * Update last data update timestamp
     */
    public function markAsUpdated(): void
    {
        $this->update(['last_data_update' => now()]);
    }

    /**
     * Get widget position as array
     */
    public function getPositionAttribute(): array
    {
        return [
            'x' => $this->position_x,
            'y' => $this->position_y,
            'w' => $this->width,
            'h' => $this->height,
        ];
    }

    /**
     * Get widget size as array
     */
    public function getSizeAttribute(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
        ];
    }

    /**
     * Get widget display configuration
     */
    public function getDisplayConfigAttribute(): array
    {
        $defaultConfig = self::getDefaultConfigs()[$this->widget_type] ?? [];
        $config = $this->display_config ?? [];

        return array_merge($defaultConfig, $config);
    }

    /**
     * Get widget CSS classes
     */
    public function getCssClassesAttribute(): string
    {
        $classes = ['widget', "widget-{$this->widget_type}"];
        
        if (!$this->is_visible) {
            $classes[] = 'hidden';
        }

        return implode(' ', $classes);
    }
} 