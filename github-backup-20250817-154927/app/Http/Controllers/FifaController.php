<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Services\FifaConnectService;
use Illuminate\Http\Request;

class FifaController extends Controller
{
    protected $fifaService;

    public function __construct(FifaConnectService $fifaService)
    {
        $this->fifaService = $fifaService;
    }

    /**
     * Display FIFA dashboard.
     */
    public function dashboard()
    {
        return view('fifa.dashboard');
    }

    /**
     * Sync player data from FIFA Connect.
     */
    public function syncPlayer($fifaId)
    {
        $result = $this->fifaService->syncPlayerData($fifaId);

        return response()->json($result);
    }

    /**
     * Check FIFA compliance for a player.
     */
    public function checkCompliance($fifaId)
    {
        $result = $this->fifaService->checkCompliance($fifaId);

        return response()->json($result);
    }

    /**
     * Display FIFA sync dashboard.
     */
    public function syncDashboard()
    {
        return view('fifa.sync-dashboard');
    }

    /**
     * Display FIFA statistics.
     */
    public function statistics()
    {
        return view('fifa.statistics');
    }

    /**
     * Display FIFA connectivity status page.
     */
    public function connectivity()
    {
        $result = $this->fifaService->checkConnectivity();
        
        // Add additional context for the enhanced UI
        $result['environment'] = config('app.env');
        $result['mock_mode_enabled'] = config('services.fifa_connect.mock_mode', false);
        $result['api_key_configured'] = !empty(config('services.fifa_connect.api_key'));
        $result['base_url'] = config('services.fifa_connect.base_url');
        $result['timeout'] = config('services.fifa_connect.timeout');
        $result['cache_ttl'] = config('services.fifa_connect.cache_ttl');

        return view('fifa.connectivity', [
            'status' => $result['connected'] ? 'connected' : 'disconnected',
            'error' => $result['error'] ?? null,
            'responseTime' => $result['response_time'] ?? null,
            'mockMode' => $result['status'] === 'mock',
            'fallbackReason' => $result['fallback_reason'] ?? null,
            'environment' => $result['environment'],
            'mockModeEnabled' => $result['mock_mode_enabled'],
            'apiKeyConfigured' => $result['api_key_configured'],
            'baseUrl' => $result['base_url'],
            'timeout' => $result['timeout'],
            'cacheTtl' => $result['cache_ttl'],
        ]);
    }

    /**
     * Get FIFA connectivity status as JSON (for AJAX requests).
     */
    public function connectivityStatus()
    {
        $result = $this->fifaService->checkConnectivity();
        
        // Add additional context for the enhanced UI
        $result['environment'] = config('app.env');
        $result['mock_mode_enabled'] = config('services.fifa_connect.mock_mode', false);
        $result['api_key_configured'] = !empty(config('services.fifa_connect.api_key'));
        $result['base_url'] = config('services.fifa_connect.base_url');
        $result['timeout'] = config('services.fifa_connect.timeout');
        $result['cache_ttl'] = config('services.fifa_connect.cache_ttl');

        return response()->json($result);
    }

    /**
     * Get FIFA statistics as JSON for Vue dashboard.
     */
    public function statisticsApi()
    {
        $playersTotal = \App\Models\Player::count();
        $playersSynced = \App\Models\Player::whereNotNull('fifa_connect_id')->count();
        $clubsTotal = \App\Models\Club::count();
        $clubsSynced = \App\Models\Club::whereNotNull('fifa_connect_id')->count();
        $associationsTotal = \App\Models\Association::count();
        $associationsSynced = \App\Models\Association::whereNotNull('fifa_connect_id')->count();

        $data = [
            'connection_status' => 'Connected',
            'last_sync' => now()->subMinutes(5)->toDateTimeString(),
            'pending_conflicts' => 0,
            'recent_errors' => 0,
            'players' => [
                'total' => $playersTotal,
                'synced' => $playersSynced,
                'sync_rate' => $playersTotal > 0 ? round($playersSynced / $playersTotal * 100) : 0,
            ],
            'clubs' => [
                'total' => $clubsTotal,
                'synced' => $clubsSynced,
                'sync_rate' => $clubsTotal > 0 ? round($clubsSynced / $clubsTotal * 100) : 0,
            ],
            'associations' => [
                'total' => $associationsTotal,
                'synced' => $associationsSynced,
                'sync_rate' => $associationsTotal > 0 ? round($associationsSynced / $associationsTotal * 100) : 0,
            ],
            'recent_activity' => [],
        ];
        return response()->json($data);
    }

    /**
     * Synchronise les joueurs depuis le dashboard Vue.
     */
    public function syncPlayers(Request $request)
    {
        // Ici, tu pourrais lancer une vraie synchronisation avec FIFA Connect
        // Pour l'instant, on simule une réponse de succès
        return response()->json([
            'success' => true,
            'message' => 'Synchronisation lancée avec succès (simulation) !',
            'requested_type' => $request->input('type'),
            'batch_size' => $request->input('batch_size'),
            'filters' => $request->input('filters'),
            'dry_run' => $request->input('dry_run', false),
        ]);
    }
} 