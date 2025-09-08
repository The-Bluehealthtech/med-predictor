<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuditTrailController extends Controller
{
    /**
     * Afficher le tableau de bord audit trail
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        // Filtres
        $filters = [
            'event_type' => $request->get('event_type'),
            'action' => $request->get('action'),
            'severity' => $request->get('severity'),
            'module' => $request->get('module'),
            'user_id' => $request->get('user_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'search' => $request->get('search')
        ];

        // Construction de la requête
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Application des filtres
        if ($filters['event_type']) {
            $query->where('event_type', $filters['event_type']);
        }

        if ($filters['action']) {
            $query->where('action', $filters['action']);
        }

        if ($filters['severity']) {
            $query->where('severity', $filters['severity']);
        }

        if ($filters['module']) {
            $query->where('module', $filters['module']);
        }

        if ($filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        if ($filters['date_from']) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from']));
        }

        if ($filters['date_to']) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        if ($filters['search']) {
            $query->where(function($q) use ($filters) {
                $q->where('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('model_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('user_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('user_email', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Pagination
        $logs = $query->paginate(50);

        // Statistiques
        $stats = [
            'total_logs' => AuditLog::count(),
            'today_logs' => AuditLog::whereDate('created_at', today())->count(),
            'this_week_logs' => AuditLog::where('created_at', '>=', now()->startOfWeek())->count(),
            'critical_logs' => AuditLog::where('severity', 'critical')->count(),
            'error_logs' => AuditLog::where('severity', 'error')->count(),
            'warning_logs' => AuditLog::where('severity', 'warning')->count(),
            'security_logs' => AuditLog::where('event_type', 'security')->count(),
            'user_logs' => AuditLog::where('event_type', 'user_action')->count()
        ];

        // Données pour les filtres
        $filterData = [
            'event_types' => AuditLog::distinct()->pluck('event_type')->filter(),
            'actions' => AuditLog::distinct()->pluck('action')->filter(),
            'severities' => AuditLog::distinct()->pluck('severity')->filter(),
            'modules' => AuditLog::distinct()->pluck('module')->filter(),
            'users' => User::select('id', 'name', 'email')->get()
        ];

        return view('admin.audit-trail.index', compact('logs', 'stats', 'filters', 'filterData'));
    }

    /**
     * Afficher les détails d'un log
     */
    public function show($id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $log = AuditLog::with('user')->findOrFail($id);

        return view('admin.audit-trail.show', compact('log'));
    }

    /**
     * Exporter les logs
     */
    public function export(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $format = $request->get('format', 'csv');
        $filters = $request->only(['event_type', 'action', 'severity', 'module', 'user_id', 'date_from', 'date_to']);

        // Construction de la requête avec les mêmes filtres que l'index
        $query = AuditLog::with('user');

        foreach ($filters as $key => $value) {
            if ($value) {
                if (in_array($key, ['date_from', 'date_to'])) {
                    if ($key === 'date_from') {
                        $query->where('created_at', '>=', Carbon::parse($value));
                    } else {
                        $query->where('created_at', '<=', Carbon::parse($value)->endOfDay());
                    }
                } else {
                    $query->where($key, $value);
                }
            }
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'csv') {
            return $this->exportToCsv($logs);
        }

        return response()->json($logs);
    }

    /**
     * Exporter en CSV
     */
    private function exportToCsv($logs)
    {
        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID', 'Date', 'Type d\'événement', 'Action', 'Modèle', 'Nom du modèle',
                'Utilisateur', 'Email utilisateur', 'IP', 'URL', 'Méthode',
                'Module', 'Sévérité', 'Description'
            ]);

            // Données
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->event_type,
                    $log->action,
                    $log->model_type,
                    $log->model_name,
                    $log->user_name,
                    $log->user_email,
                    $log->ip_address,
                    $log->url,
                    $log->method,
                    $log->module,
                    $log->severity,
                    $log->description
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Supprimer les anciens logs
     */
    public function cleanup(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['super_admin', 'system_admin'])) {
            return redirect()->route('login')->withErrors(['email' => 'Accès administrateur requis.']);
        }

        $days = $request->get('days', 90);
        $deleted = AuditLog::where('created_at', '<', now()->subDays($days))->delete();

        return redirect()->route('admin.audit-trail.index')
            ->with('success', "{$deleted} logs supprimés (plus anciens que {$days} jours).");
    }
}
