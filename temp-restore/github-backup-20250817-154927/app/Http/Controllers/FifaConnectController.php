<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use App\Services\FifaConnectService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\FifaSyncLog; // Added this import for FifaSyncLog
use Carbon\Carbon; // Added this import for Carbon

class FifaConnectController extends Controller
{
    protected $fifaConnectService;

    public function __construct(FifaConnectService $fifaConnectService)
    {
        $this->fifaConnectService = $fifaConnectService;
        $this->middleware('auth');
        $this->middleware('role:system_admin,association_admin,admin');
    }

    /**
     * Get players from FIFA Connect API
     */
    public function getPlayers(Request $request): JsonResponse
    {
        $filters = $request->only([
            'position', 'nationality', 'min_rating', 'max_rating', 
            'age_min', 'age_max', 'preferred_foot', 'work_rate'
        ]);

        $page = $request->get('page', 1);
        $limit = min($request->get('limit', 50), 100);

        $result = $this->fifaConnectService->fetchPlayers($filters, $page, $limit);

        return response()->json($result);
    }

    /**
     * Get single player from FIFA Connect API
     */
    public function getPlayer($id): JsonResponse
    {
        $result = $this->fifaConnectService->fetchPlayer($id);

        if (!$result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }

    /**
     * Get player statistics from FIFA Connect API
     */
    public function getPlayerStats($id): JsonResponse
    {
        $result = $this->fifaConnectService->fetchPlayerStats($id);

        if (!$result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }

    /**
     * Sync players from FIFA Connect API to local database
     */
    public function syncPlayers(Request $request): JsonResponse
    {
        $filters = $request->only([
            'position', 'nationality', 'min_rating', 'max_rating'
        ]);

        $batchSize = min($request->get('batch_size', 50), 100);
        $forceSync = $request->get('force_sync', false);

        if ($forceSync) {
            $this->fifaConnectService->clearCaches();
        }

        $result = $this->fifaConnectService->syncPlayers($filters, $batchSize);

        // Store last sync timestamp
        Cache::put('fifa_last_sync', now(), 86400);

        return response()->json([
            'success' => true,
            'message' => 'Synchronisation terminée',
            'results' => $result
        ]);
    }

    /**
     * Search players with advanced filtering
     */
    public function searchPlayers(Request $request): JsonResponse
    {
        $filters = $request->only([
            'q', 'position', 'nationality', 'min_rating', 'max_rating',
            'age_min', 'age_max', 'preferred_foot', 'work_rate'
        ]);

        $page = $request->get('page', 1);
        $limit = min($request->get('limit', 20), 100);
        $sortBy = $request->get('sort_by', 'overall_rating');
        $sortOrder = $request->get('sort_order', 'desc');

        // Add sorting to filters
        $filters['sort_by'] = $sortBy;
        $filters['sort_order'] = $sortOrder;

        $result = $this->fifaConnectService->fetchPlayers($filters, $page, $limit);

        return response()->json([
            'success' => $result['success'],
            'data' => $result['data'],
            'total' => $result['total'],
            'source' => $result['source'],
            'filters' => $request->all(),
            'sorting' => [
                'by' => $sortBy,
                'order' => $sortOrder
            ]
        ]);
    }

    /**
     * Get FIFA Connect connectivity status
     */
    public function getConnectivityStatus(): JsonResponse
    {
        $status = $this->fifaConnectService->testConnectivity();

        return response()->json($status);
    }

    /**
     * Display FIFA connectivity status page
     */
    public function showConnectivityStatus()
    {
        // Minimal version for debugging
        return view('fifa.connectivity', [
            'status' => 'connected',
            'error' => null,
            'responseTime' => 100,
            'mockMode' => true,
            'statistics' => [
                'total_players' => 0,
                'total_clubs' => 0,
                'total_associations' => 0,
            ],
            'samplePlayers' => ['data' => []],
            'sampleClubs' => ['data' => []],
            'localClubs' => collect([]),
            'localPlayers' => collect([])
        ]);
    }

    /**
     * Get FIFA Connect statistics
     */
    public function getStatistics(): JsonResponse
    {
        $statistics = $this->fifaConnectService->getStatistics();
        
        // Get total counts from database
        $totalPlayers = Player::count();
        $totalClubs = Club::count();
        $totalAssociations = Association::count();
        
        // Get synced counts
        $syncedPlayers = Player::whereNotNull('fifa_connect_id')->count();
        $syncedClubs = Club::whereNotNull('fifa_connect_id')->count();
        $syncedAssociations = Association::whereNotNull('fifa_connect_id')->count();
        
        // Calculate sync rates
        $playerSyncRate = $totalPlayers > 0 ? round(($syncedPlayers / $totalPlayers) * 100, 1) : 0;
        $clubSyncRate = $totalClubs > 0 ? round(($syncedClubs / $totalClubs) * 100, 1) : 0;
        $associationSyncRate = $totalAssociations > 0 ? round(($syncedAssociations / $totalAssociations) * 100, 1) : 0;
        
        // Get recent activity from sync logs
        $recentActivity = FifaSyncLog::latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'type' => $log->status_code >= 200 && $log->status_code < 300 ? 'success' : 'error',
                    'description' => "Sync {$log->entity_type} #{$log->entity_id}",
                    'timestamp' => $log->created_at->diffForHumans(),
                    'status' => $log->status_code >= 200 && $log->status_code < 300 ? 'completed' : 'failed'
                ];
            });

        return response()->json([
            'connection_status' => $statistics['connectivity_status']['success'] ? 'Connected' : 'Disconnected',
            'last_sync' => $statistics['last_sync'] ? Carbon::parse($statistics['last_sync'])->diffForHumans() : 'Never',
            'pending_conflicts' => 0, // Placeholder
            'recent_errors' => FifaSyncLog::where('status_code', '>=', 400)->count(),
            'players' => [
                'total' => $totalPlayers,
                'synced' => $syncedPlayers,
                'sync_rate' => $playerSyncRate
            ],
            'clubs' => [
                'total' => $totalClubs,
                'synced' => $syncedClubs,
                'sync_rate' => $clubSyncRate
            ],
            'associations' => [
                'total' => $totalAssociations,
                'synced' => $syncedAssociations,
                'sync_rate' => $associationSyncRate
            ],
            'recent_activity' => $recentActivity
        ]);
    }

    /**
     * Clear FIFA Connect caches
     */
    public function clearCaches(): JsonResponse
    {
        try {
            $this->fifaConnectService->clearCaches();

            return response()->json([
                'success' => true,
                'message' => 'Caches FIFA Connect effacés avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to clear FIFA Connect caches: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'effacement des caches',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get clubs from FIFA Connect API
     */
    public function getClubs(Request $request): JsonResponse
    {
        $filters = $request->only(['country', 'league', 'status']);
        $page = $request->get('page', 1);
        $limit = min($request->get('limit', 50), 100);

        $result = $this->fifaConnectService->fetchClubs($filters, $page, $limit);

        return response()->json($result);
    }

    /**
     * Get associations from FIFA Connect API
     */
    public function getAssociations(Request $request): JsonResponse
    {
        $filters = $request->only(['country', 'region', 'status']);
        $page = $request->get('page', 1);
        $limit = min($request->get('limit', 50), 100);

        $result = $this->fifaConnectService->fetchAssociations($filters, $page, $limit);

        return response()->json($result);
    }

    /**
     * Sync specific player by FIFA ID
     */
    public function syncPlayer($fifaId): JsonResponse
    {
        try {
            $playerData = $this->fifaConnectService->fetchPlayer($fifaId);

            if (!$playerData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Joueur non trouvé dans FIFA Connect',
                    'error' => $playerData['error']
                ], 404);
            }

            // Find or create player in local database
            $player = Player::where('fifa_connect_id', $fifaId)->first();

            if ($player) {
                $player->update([
                    'name' => $playerData['data']['name'] ?? $player->name,
                    'first_name' => $playerData['data']['first_name'] ?? $player->first_name,
                    'last_name' => $playerData['data']['last_name'] ?? $player->last_name,
                    'overall_rating' => $playerData['data']['overall_rating'] ?? $player->overall_rating,
                    'potential_rating' => $playerData['data']['potential_rating'] ?? $player->potential_rating,
                    'last_updated' => now(),
                ]);

                $action = 'updated';
            } else {
                $player = Player::create([
                    'fifa_connect_id' => $fifaId,
                    'name' => $playerData['data']['name'] ?? '',
                    'first_name' => $playerData['data']['first_name'] ?? '',
                    'last_name' => $playerData['data']['last_name'] ?? '',
                    'date_of_birth' => $playerData['data']['date_of_birth'] ?? null,
                    'nationality' => $playerData['data']['nationality'] ?? '',
                    'position' => $playerData['data']['position'] ?? '',
                    'overall_rating' => $playerData['data']['overall_rating'] ?? null,
                    'potential_rating' => $playerData['data']['potential_rating'] ?? null,
                    'last_updated' => now(),
                ]);

                $action = 'created';
            }

            return response()->json([
                'success' => true,
                'message' => "Joueur {$action} avec succès",
                'action' => $action,
                'player_id' => $player->id,
                'fifa_id' => $fifaId
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to sync player {$fifaId}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la synchronisation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk sync multiple players
     */
    public function bulkSyncPlayers(Request $request): JsonResponse
    {
        $fifaIds = $request->input('fifa_ids', []);
        $filters = $request->only(['position', 'nationality', 'min_rating', 'max_rating']);

        if (empty($fifaIds) && empty($filters)) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez spécifier des IDs FIFA ou des filtres'
            ], 400);
        }

        $results = [
            'total_requested' => count($fifaIds),
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];

        if (!empty($fifaIds)) {
            // Sync specific players
            foreach ($fifaIds as $fifaId) {
                try {
                    $response = $this->syncPlayer($fifaId);
                    $responseData = json_decode($response->getContent(), true);

                    if ($responseData['success']) {
                        $results['successful']++;
                    } else {
                        $results['failed']++;
                        $results['errors'][] = "ID {$fifaId}: " . ($responseData['error'] ?? 'Unknown error');
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "ID {$fifaId}: " . $e->getMessage();
                }
            }
        } else {
            // Sync based on filters
            $syncResult = $this->fifaConnectService->syncPlayers($filters, 50);
            
            $results['total_requested'] = $syncResult['total_processed'];
            $results['successful'] = $syncResult['new_players'] + $syncResult['updated_players'];
            $results['failed'] = count($syncResult['errors']);
            $results['errors'] = $syncResult['errors'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Synchronisation en lot terminée',
            'results' => $results
        ]);
    }
}
