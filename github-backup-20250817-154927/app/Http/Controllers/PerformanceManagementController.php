<?php

namespace App\Http\Controllers;

use App\Models\PerformanceMetric;
use App\Models\PerformanceDashboard;
use App\Models\PerformanceAlert;
use App\Models\PerformanceTrend;
use App\Models\DashboardWidget;
use App\Models\Player;
use App\Models\Club;
use App\Models\Team;
use App\Models\User;
use App\Services\PerformanceAnalysisService;
use App\Services\AIAnalysisService;
use App\Services\FifaConnectService;
use App\Services\Hl7FhirService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PerformanceManagementController extends Controller
{
    protected PerformanceAnalysisService $analysisService;
    protected AIAnalysisService $aiService;
    protected FifaConnectService $fifaService;
    protected Hl7FhirService $hl7Service;

    public function __construct(
        PerformanceAnalysisService $analysisService,
        AIAnalysisService $aiService,
        FifaConnectService $fifaService,
        Hl7FhirService $hl7Service
    ) {
        $this->analysisService = $analysisService;
        $this->aiService = $aiService;
        $this->fifaService = $fifaService;
        $this->hl7Service = $hl7Service;
    }

    /**
     * Display the main performance management dashboard
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();
        $dashboardType = $request->get('type', $this->getUserDashboardType($user));
        
        $dashboard = $this->getOrCreateDashboard($user, $dashboardType);
        $metrics = $this->getPerformanceMetrics($user, $dashboardType);
        $alerts = $this->getActiveAlerts($user, $dashboardType);
        $trends = $this->getRecentTrends($user, $dashboardType);

        return view('performance-management.dashboard', compact(
            'dashboard',
            'metrics',
            'alerts',
            'trends',
            'dashboardType'
        ));
    }

    /**
     * Get performance metrics for the current user/context
     */
    public function getMetrics(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'player_id' => 'nullable|exists:players,id',
            'club_id' => 'nullable|exists:clubs,id',
            'team_id' => 'nullable|exists:teams,id',
            'metric_type' => 'nullable|in:' . implode(',', PerformanceMetric::getMetricTypes()),
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'data_source' => 'nullable|in:' . implode(',', PerformanceMetric::getDataSources()),
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $query = PerformanceMetric::with(['player', 'createdBy', 'verifiedBy']);

        // Apply filters based on user role and permissions
        $this->applyMetricFilters($query, $user, $request->all());

        $metrics = $query->orderBy('measurement_date', 'desc')
                        ->paginate($request->get('limit', 20));

        return response()->json([
            'success' => true,
            'data' => $metrics,
            'summary' => $this->getMetricsSummary($metrics->items()),
        ]);
    }

    /**
     * Store a new performance metric
     */
    public function storeMetric(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'player_id' => 'required|exists:players,id',
            'metric_type' => ['required', Rule::in(array_keys(PerformanceMetric::getMetricTypes()))],
            'metric_name' => 'required|string|max:255',
            'metric_value' => 'required|numeric',
            'metric_unit' => 'required|string|max:50',
            'measurement_date' => 'required|date',
            'data_source' => ['required', Rule::in(array_keys(PerformanceMetric::getDataSources()))],
            'confidence_score' => 'nullable|numeric|min:0|max:1',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $metric = PerformanceMetric::create([
                'player_id' => $request->player_id,
                'metric_type' => $request->metric_type,
                'metric_name' => $request->metric_name,
                'metric_value' => $request->metric_value,
                'metric_unit' => $request->metric_unit,
                'measurement_date' => $request->measurement_date,
                'data_source' => $request->data_source,
                'confidence_score' => $request->confidence_score ?? 1.0,
                'notes' => $request->notes,
                'metadata' => $request->metadata,
                'created_by' => Auth::id(),
            ]);

            // Analyze the metric for trends and alerts
            $this->analyzeMetric($metric);

            // Sync with external systems if applicable
            $this->syncMetricToExternalSystems($metric);

            DB::commit();

            Log::info('Performance metric created', [
                'metric_id' => $metric->id,
                'player_id' => $metric->player_id,
                'metric_type' => $metric->metric_type,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Performance metric created successfully',
                'data' => $metric->load(['player', 'createdBy']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create performance metric', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create performance metric',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing performance metric
     */
    public function updateMetric(Request $request, PerformanceMetric $metric): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'metric_value' => 'nullable|numeric',
            'metric_unit' => 'nullable|string|max:50',
            'measurement_date' => 'nullable|date',
            'confidence_score' => 'nullable|numeric|min:0|max:1',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
            'is_verified' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $updateData = $request->only([
                'metric_value', 'metric_unit', 'measurement_date',
                'confidence_score', 'notes', 'metadata'
            ]);

            if ($request->has('is_verified') && $request->is_verified) {
                $updateData['is_verified'] = true;
                $updateData['verified_by'] = Auth::id();
                $updateData['verified_at'] = now();
            }

            $updateData['updated_by'] = Auth::id();

            $metric->update($updateData);

            // Re-analyze the metric if value changed
            if ($request->has('metric_value')) {
                $this->analyzeMetric($metric);
            }

            DB::commit();

            Log::info('Performance metric updated', [
                'metric_id' => $metric->id,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Performance metric updated successfully',
                'data' => $metric->load(['player', 'createdBy', 'verifiedBy']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update performance metric', [
                'error' => $e->getMessage(),
                'metric_id' => $metric->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update performance metric',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get performance alerts
     */
    public function getAlerts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'alert_type' => 'nullable|in:' . implode(',', array_keys(PerformanceAlert::getAlertTypes())),
            'alert_level' => 'nullable|in:' . implode(',', array_keys(PerformanceAlert::getAlertLevels())),
            'is_acknowledged' => 'nullable|boolean',
            'is_resolved' => 'nullable|boolean',
            'player_id' => 'nullable|exists:players,id',
            'club_id' => 'nullable|exists:clubs,id',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $query = PerformanceAlert::with(['player', 'club', 'team', 'metric', 'createdBy']);

        // Apply filters based on user role and permissions
        $this->applyAlertFilters($query, $user, $request->all());

        $alerts = $query->orderBy('created_at', 'desc')
                       ->paginate($request->get('limit', 20));

        return response()->json([
            'success' => true,
            'data' => $alerts,
            'summary' => $this->getAlertsSummary($alerts->items()),
        ]);
    }

    /**
     * Acknowledge an alert
     */
    public function acknowledgeAlert(Request $request, PerformanceAlert $alert): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $alert->acknowledge(Auth::user(), $request->notes);

            Log::info('Performance alert acknowledged', [
                'alert_id' => $alert->id,
                'acknowledged_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alert acknowledged successfully',
                'data' => $alert->load(['player', 'club', 'team', 'metric', 'acknowledgedBy']),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to acknowledge alert', [
                'error' => $e->getMessage(),
                'alert_id' => $alert->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to acknowledge alert',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resolve an alert
     */
    public function resolveAlert(Request $request, PerformanceAlert $alert): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $alert->resolve(Auth::user(), $request->notes);

            Log::info('Performance alert resolved', [
                'alert_id' => $alert->id,
                'resolved_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alert resolved successfully',
                'data' => $alert->load(['player', 'club', 'team', 'metric', 'resolvedBy']),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to resolve alert', [
                'error' => $e->getMessage(),
                'alert_id' => $alert->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve alert',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get performance trends
     */
    public function getTrends(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'player_id' => 'nullable|exists:players,id',
            'trend_period' => 'nullable|in:' . implode(',', array_keys(PerformanceTrend::getTrendPeriods())),
            'trend_direction' => 'nullable|in:' . implode(',', array_keys(PerformanceTrend::getTrendDirections())),
            'trend_strength' => 'nullable|in:' . implode(',', array_keys(PerformanceTrend::getTrendStrengths())),
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $query = PerformanceTrend::with(['player', 'metric', 'createdBy']);

        // Apply filters based on user role and permissions
        $this->applyTrendFilters($query, $user, $request->all());

        $trends = $query->orderBy('end_date', 'desc')
                       ->paginate($request->get('limit', 20));

        return response()->json([
            'success' => true,
            'data' => $trends,
            'summary' => $this->getTrendsSummary($trends->items()),
        ]);
    }

    /**
     * Get dashboard configuration
     */
    public function getDashboard(Request $request): JsonResponse
    {
        $user = Auth::user();
        $dashboardType = $request->get('type', $this->getUserDashboardType($user));
        
        $dashboard = $this->getOrCreateDashboard($user, $dashboardType);
        $widgets = $dashboard->widgets()->with('dashboard')->get();
        $filters = $dashboard->filters()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'dashboard' => $dashboard,
                'widgets' => $widgets,
                'filters' => $filters,
            ],
        ]);
    }

    /**
     * Update dashboard configuration
     */
    public function updateDashboard(Request $request, PerformanceDashboard $dashboard): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'layout_config' => 'nullable|array',
            'widgets_config' => 'nullable|array',
            'filters_config' => 'nullable|array',
            'refresh_interval' => 'nullable|integer|min:30|max:3600',
            'is_default' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $updateData = $request->only([
                'name', 'description', 'layout_config', 'widgets_config',
                'filters_config', 'refresh_interval', 'is_default', 'is_public'
            ]);

            $updateData['updated_by'] = Auth::id();

            $dashboard->update($updateData);

            Log::info('Performance dashboard updated', [
                'dashboard_id' => $dashboard->id,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dashboard updated successfully',
                'data' => $dashboard->load(['widgets', 'filters']),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update dashboard', [
                'error' => $e->getMessage(),
                'dashboard_id' => $dashboard->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update dashboard',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get AI insights for performance data
     */
    public function getAIInsights(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'player_id' => 'nullable|exists:players,id',
            'club_id' => 'nullable|exists:clubs,id',
            'insight_type' => 'nullable|in:performance,injury_risk,development,recommendations',
            'date_range' => 'nullable|in:week,month,quarter,year',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $insights = $this->aiService->generateInsights($request->all());

            return response()->json([
                'success' => true,
                'data' => $insights,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate AI insights', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate AI insights',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export performance data
     */
    public function exportData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'export_type' => 'required|in:metrics,alerts,trends,dashboard',
            'format' => 'required|in:csv,excel,pdf,json',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'filters' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $exportData = $this->generateExportData($request->all());

            return response()->json([
                'success' => true,
                'data' => $exportData,
                'download_url' => $this->generateDownloadUrl($exportData, $request->format),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to export performance data', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Helper methods

    /**
     * Get user dashboard type based on role
     */
    protected function getUserDashboardType(User $user): string
    {
        if ($user->isSystemAdmin()) {
            return PerformanceDashboard::TYPE_FEDERATION;
        } elseif ($user->isAssociationAdmin()) {
            return PerformanceDashboard::TYPE_FEDERATION;
        } elseif ($user->isClubAdmin()) {
            return PerformanceDashboard::TYPE_CLUB;
        } elseif ($user->isCoach()) {
            return PerformanceDashboard::TYPE_COACH;
        } elseif ($user->isMedicalStaff()) {
            return PerformanceDashboard::TYPE_MEDICAL;
        } elseif ($user->isReferee()) {
            return PerformanceDashboard::TYPE_REFEREE;
        } else {
            return PerformanceDashboard::TYPE_PLAYER;
        }
    }

    /**
     * Get or create dashboard for user
     */
    protected function getOrCreateDashboard(User $user, string $type): PerformanceDashboard
    {
        $dashboard = PerformanceDashboard::where('user_id', $user->id)
            ->where('dashboard_type', $type)
            ->where('is_default', true)
            ->first();

        if (!$dashboard) {
            $dashboard = PerformanceDashboard::create([
                'name' => ucfirst($type) . ' Dashboard',
                'dashboard_type' => $type,
                'user_id' => $user->id,
                'is_default' => true,
                'created_by' => $user->id,
            ]);

            $dashboard->initializeWithDefaults();
        }

        return $dashboard;
    }

    /**
     * Apply metric filters based on user permissions
     */
    protected function applyMetricFilters($query, User $user, array $filters): void
    {
        // Apply role-based filtering
        if ($user->isClubAdmin()) {
            $query->whereHas('player', function ($q) use ($user) {
                $q->where('club_id', $user->club_id);
            });
        } elseif ($user->isCoach()) {
            $query->whereHas('player', function ($q) use ($user) {
                $q->whereHas('teams', function ($teamQ) use ($user) {
                    $teamQ->where('coach_id', $user->id);
                });
            });
        } elseif (!$user->isSystemAdmin() && !$user->isAssociationAdmin()) {
            // Regular users can only see their own metrics
            $query->whereHas('player', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // Apply additional filters
        if (isset($filters['player_id'])) {
            $query->where('player_id', $filters['player_id']);
        }

        if (isset($filters['metric_type'])) {
            $query->where('metric_type', $filters['metric_type']);
        }

        if (isset($filters['date_from'])) {
            $query->where('measurement_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('measurement_date', '<=', $filters['date_to']);
        }

        if (isset($filters['data_source'])) {
            $query->where('data_source', $filters['data_source']);
        }
    }

    /**
     * Apply alert filters based on user permissions
     */
    protected function applyAlertFilters($query, User $user, array $filters): void
    {
        // Apply role-based filtering
        if ($user->isClubAdmin()) {
            $query->where('club_id', $user->club_id);
        } elseif ($user->isCoach()) {
            $query->whereHas('player', function ($q) use ($user) {
                $q->whereHas('teams', function ($teamQ) use ($user) {
                    $teamQ->where('coach_id', $user->id);
                });
            });
        } elseif (!$user->isSystemAdmin() && !$user->isAssociationAdmin()) {
            // Regular users can only see their own alerts
            $query->where('player_id', $user->player_id);
        }

        // Apply additional filters
        if (isset($filters['alert_type'])) {
            $query->where('alert_type', $filters['alert_type']);
        }

        if (isset($filters['alert_level'])) {
            $query->where('alert_level', $filters['alert_level']);
        }

        if (isset($filters['is_acknowledged'])) {
            $query->where('is_acknowledged', $filters['is_acknowledged']);
        }

        if (isset($filters['is_resolved'])) {
            $query->where('is_resolved', $filters['is_resolved']);
        }

        if (isset($filters['player_id'])) {
            $query->where('player_id', $filters['player_id']);
        }

        if (isset($filters['club_id'])) {
            $query->where('club_id', $filters['club_id']);
        }
    }

    /**
     * Apply trend filters based on user permissions
     */
    protected function applyTrendFilters($query, User $user, array $filters): void
    {
        // Apply role-based filtering
        if ($user->isClubAdmin()) {
            $query->whereHas('player', function ($q) use ($user) {
                $q->where('club_id', $user->club_id);
            });
        } elseif ($user->isCoach()) {
            $query->whereHas('player', function ($q) use ($user) {
                $q->whereHas('teams', function ($teamQ) use ($user) {
                    $teamQ->where('coach_id', $user->id);
                });
            });
        } elseif (!$user->isSystemAdmin() && !$user->isAssociationAdmin()) {
            // Regular users can only see their own trends
            $query->where('player_id', $user->player_id);
        }

        // Apply additional filters
        if (isset($filters['player_id'])) {
            $query->where('player_id', $filters['player_id']);
        }

        if (isset($filters['trend_period'])) {
            $query->where('trend_period', $filters['trend_period']);
        }

        if (isset($filters['trend_direction'])) {
            $query->where('trend_direction', $filters['trend_direction']);
        }

        if (isset($filters['trend_strength'])) {
            $query->where('trend_strength', $filters['trend_strength']);
        }

        if (isset($filters['date_from'])) {
            $query->where('end_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('end_date', '<=', $filters['date_to']);
        }
    }

    /**
     * Get performance metrics for dashboard
     */
    protected function getPerformanceMetrics(User $user, string $dashboardType): array
    {
        $query = PerformanceMetric::with(['player', 'createdBy']);

        switch ($dashboardType) {
            case PerformanceDashboard::TYPE_FEDERATION:
                // Federation can see all metrics
                break;
            case PerformanceDashboard::TYPE_CLUB:
                $query->whereHas('player', function ($q) use ($user) {
                    $q->where('club_id', $user->club_id);
                });
                break;
            case PerformanceDashboard::TYPE_PLAYER:
                $query->whereHas('player', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
                break;
            default:
                $query->whereHas('player', function ($q) use ($user) {
                    $q->where('club_id', $user->club_id);
                });
        }

        return [
            'recent' => $query->recent()->limit(10)->get(),
            'by_type' => $query->selectRaw('metric_type, COUNT(*) as count')
                              ->groupBy('metric_type')
                              ->get(),
            'by_source' => $query->selectRaw('data_source, COUNT(*) as count')
                                ->groupBy('data_source')
                                ->get(),
        ];
    }

    /**
     * Get active alerts for dashboard
     */
    protected function getActiveAlerts(User $user, string $dashboardType): array
    {
        $query = PerformanceAlert::with(['player', 'club', 'team'])->active();

        switch ($dashboardType) {
            case PerformanceDashboard::TYPE_FEDERATION:
                // Federation can see all alerts
                break;
            case PerformanceDashboard::TYPE_CLUB:
                $query->where('club_id', $user->club_id);
                break;
            case PerformanceDashboard::TYPE_PLAYER:
                $query->where('player_id', $user->player_id);
                break;
            default:
                $query->where('club_id', $user->club_id);
        }

        return [
            'critical' => $query->critical()->limit(5)->get(),
            'high_priority' => $query->highPriority()->limit(10)->get(),
            'by_type' => $query->selectRaw('alert_type, COUNT(*) as count')
                              ->groupBy('alert_type')
                              ->get(),
        ];
    }

    /**
     * Get recent trends for dashboard
     */
    protected function getRecentTrends(User $user, string $dashboardType): array
    {
        $query = PerformanceTrend::with(['player', 'metric'])->recent();

        switch ($dashboardType) {
            case PerformanceDashboard::TYPE_FEDERATION:
                // Federation can see all trends
                break;
            case PerformanceDashboard::TYPE_CLUB:
                $query->whereHas('player', function ($q) use ($user) {
                    $q->where('club_id', $user->club_id);
                });
                break;
            case PerformanceDashboard::TYPE_PLAYER:
                $query->where('player_id', $user->player_id);
                break;
            default:
                $query->whereHas('player', function ($q) use ($user) {
                    $q->where('club_id', $user->club_id);
                });
        }

        return [
            'significant' => $query->strong()->limit(10)->get(),
            'by_direction' => $query->selectRaw('trend_direction, COUNT(*) as count')
                                   ->groupBy('trend_direction')
                                   ->get(),
        ];
    }

    /**
     * Analyze metric for trends and alerts
     */
    protected function analyzeMetric(PerformanceMetric $metric): void
    {
        // Generate trends
        $this->analysisService->generateTrends($metric);

        // Check for alerts
        $this->analysisService->checkForAlerts($metric);

        // Generate AI insights
        $this->aiService->analyzeMetric($metric);
    }

    /**
     * Sync metric to external systems
     */
    protected function syncMetricToExternalSystems(PerformanceMetric $metric): void
    {
        // Sync to FIFA Connect if applicable
        if ($metric->data_source === PerformanceMetric::SOURCE_FIFA_CONNECT) {
            $this->fifaService->syncMetric($metric);
        }

        // Sync to HL7 FHIR if applicable
        if ($metric->data_source === PerformanceMetric::SOURCE_HL7_FHIR) {
            $this->hl7Service->syncMetric($metric);
        }
    }

    /**
     * Get metrics summary
     */
    protected function getMetricsSummary(array $metrics): array
    {
        return [
            'total_count' => count($metrics),
            'by_type' => collect($metrics)->groupBy('metric_type')->map->count(),
            'by_source' => collect($metrics)->groupBy('data_source')->map->count(),
            'verified_count' => collect($metrics)->where('is_verified', true)->count(),
        ];
    }

    /**
     * Get alerts summary
     */
    protected function getAlertsSummary(array $alerts): array
    {
        return [
            'total_count' => count($alerts),
            'by_type' => collect($alerts)->groupBy('alert_type')->map->count(),
            'by_level' => collect($alerts)->groupBy('alert_level')->map->count(),
            'unacknowledged_count' => collect($alerts)->where('is_acknowledged', false)->count(),
            'unresolved_count' => collect($alerts)->where('is_resolved', false)->count(),
        ];
    }

    /**
     * Get trends summary
     */
    protected function getTrendsSummary(array $trends): array
    {
        return [
            'total_count' => count($trends),
            'by_direction' => collect($trends)->groupBy('trend_direction')->map->count(),
            'by_strength' => collect($trends)->groupBy('trend_strength')->map->count(),
            'significant_count' => collect($trends)->where('trend_strength', 'strong')->count(),
        ];
    }

    /**
     * Generate export data
     */
    protected function generateExportData(array $params): array
    {
        // Implementation for data export
        return [];
    }

    /**
     * Generate download URL
     */
    protected function generateDownloadUrl(array $data, string $format): string
    {
        // Implementation for download URL generation
        return '';
    }
} 