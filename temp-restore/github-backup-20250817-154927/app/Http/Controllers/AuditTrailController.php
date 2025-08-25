<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

class AuditTrailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:system_admin');
    }

    /**
     * Display the audit trail index page
     */
    public function index(Request $request): View
    {
        $query = AuditTrail::with('user')
            ->orderBy('occurred_at', 'desc');

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Get paginated results
        $auditTrails = $query->paginate(50);

        // Get statistics
        $stats = $this->getStatistics($request);

        // Get filter options
        $filterOptions = $this->getFilterOptions();

        return view('back-office.audit-trail.index', compact(
            'auditTrails',
            'stats',
            'filterOptions'
        ));
    }

    /**
     * Show detailed view of an audit trail entry
     */
    public function show(AuditTrail $auditTrail): View
    {
        $auditTrail->load('user');
        
        return view('back-office.audit-trail.show', compact('auditTrail'));
    }

    /**
     * Export audit trail data
     */
    public function export(Request $request): Response
    {
        $query = AuditTrail::with('user')
            ->orderBy('occurred_at', 'desc');

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Get all results for export
        $auditTrails = $query->get();

        $filename = 'audit_trail_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($auditTrails) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'User',
                'Action',
                'Event Type',
                'Severity',
                'Description',
                'Model Type',
                'Model ID',
                'IP Address',
                'User Agent',
                'Request Method',
                'Request URL',
                'Occurred At',
                'Created At'
            ]);

            // CSV data
            foreach ($auditTrails as $trail) {
                fputcsv($file, [
                    $trail->id,
                    $trail->user ? $trail->user->name : 'System',
                    $trail->action,
                    $trail->event_type,
                    $trail->severity,
                    $trail->description,
                    $trail->model_type,
                    $trail->model_id,
                    $trail->ip_address,
                    $trail->user_agent,
                    $trail->request_method,
                    $trail->request_url,
                    $trail->occurred_at->format('Y-m-d H:i:s'),
                    $trail->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get audit trail statistics for dashboard
     */
    public function statistics(Request $request): JsonResponse
    {
        $stats = $this->getStatistics($request);
        
        return response()->json($stats);
    }

    /**
     * Get real-time audit trail updates
     */
    public function realtime(Request $request): JsonResponse
    {
        $lastId = $request->get('last_id', 0);
        
        $newEntries = AuditTrail::with('user')
            ->where('id', '>', $lastId)
            ->orderBy('occurred_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'entries' => $newEntries,
            'last_id' => $newEntries->max('id') ?? $lastId
        ]);
    }

    /**
     * Apply filters to the query
     */
    private function applyFilters($query, Request $request)
    {
        // User filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Action filter
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Event type filter
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Severity filter
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Model type filter
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('occurred_at', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('occurred_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        // IP address filter
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('action', 'like', '%' . $search . '%')
                  ->orWhere('model_type', 'like', '%' . $search . '%')
                  ->orWhere('ip_address', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                               ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        return $query;
    }

    /**
     * Get audit trail statistics
     */
    private function getStatistics(Request $request): array
    {
        $query = AuditTrail::query();
        
        // Apply date range if specified
        if ($request->filled('date_from')) {
            $query->where('occurred_at', '>=', Carbon::parse($request->date_from));
        }
        if ($request->filled('date_to')) {
            $query->where('occurred_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        // Total entries
        $total = $query->count();

        // By event type
        $byEventType = $query->clone()
            ->select('event_type', DB::raw('count(*) as count'))
            ->groupBy('event_type')
            ->pluck('count', 'event_type')
            ->toArray();

        // By severity
        $bySeverity = $query->clone()
            ->select('severity', DB::raw('count(*) as count'))
            ->groupBy('severity')
            ->pluck('count', 'severity')
            ->toArray();

        // By action
        $byAction = $query->clone()
            ->select('action', DB::raw('count(*) as count'))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'action')
            ->toArray();

        // By user
        $byUser = $query->clone()
            ->select('user_id', DB::raw('count(*) as count'))
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->mapWithKeys(function($item) {
                $user = User::find($item->user_id);
                return [$user ? $user->name : 'System' => $item->count];
            })
            ->toArray();

        // Recent activity (last 24 hours)
        $recentActivity = $query->clone()
            ->where('occurred_at', '>=', now()->subDay())
            ->count();

        // Security events
        $securityEvents = $query->clone()
            ->where('event_type', 'security_event')
            ->count();

        // Critical events
        $criticalEvents = $query->clone()
            ->where('severity', 'critical')
            ->count();

        return [
            'total' => $total,
            'by_event_type' => $byEventType,
            'by_severity' => $bySeverity,
            'by_action' => $byAction,
            'by_user' => $byUser,
            'recent_activity' => $recentActivity,
            'security_events' => $securityEvents,
            'critical_events' => $criticalEvents,
        ];
    }

    /**
     * Get filter options for the form
     */
    private function getFilterOptions(): array
    {
        return [
            'users' => User::orderBy('name')->pluck('name', 'id'),
            'actions' => AuditTrail::distinct()->pluck('action')->sort()->values(),
            'event_types' => AuditTrail::distinct()->pluck('event_type')->sort()->values(),
            'severities' => AuditTrail::distinct()->pluck('severity')->sort()->values(),
            'model_types' => AuditTrail::whereNotNull('model_type')->distinct()->pluck('model_type')->sort()->values(),
        ];
    }

    /**
     * Clear old audit trail entries
     */
    public function clearOldEntries(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $days = $request->days;
        $cutoffDate = now()->subDays($days);

        $deletedCount = AuditTrail::where('occurred_at', '<', $cutoffDate)->delete();

        // Log the cleanup action
        AuditTrail::logSystemEvent(
            'cleanup',
            "Cleared {$deletedCount} audit trail entries older than {$days} days",
            'info',
            ['deleted_count' => $deletedCount, 'cutoff_date' => $cutoffDate]
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully cleared {$deletedCount} old audit trail entries",
            'deleted_count' => $deletedCount
        ]);
    }
}
