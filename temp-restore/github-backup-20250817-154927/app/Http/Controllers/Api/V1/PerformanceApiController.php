<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\PlayerPerformance;
use App\Models\PerformanceRecommendation;
use App\Services\PerformanceAnalysisService;
use App\Services\AIRecommendationService;
use App\Services\FifaConnectService;
use App\Services\Hl7FhirService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * @OA\Tag(
 *     name="Performance Management",
 *     description="API endpoints for sports performance management"
 * )
 */
class PerformanceApiController extends Controller
{
    protected $performanceService;
    protected $aiService;
    protected $fifaService;
    protected $fhirService;

    public function __construct(
        PerformanceAnalysisService $performanceService,
        AIRecommendationService $aiService,
        FifaConnectService $fifaService,
        Hl7FhirService $fhirService
    ) {
        $this->performanceService = $performanceService;
        $this->aiService = $aiService;
        $this->fifaService = $fifaService;
        $this->fhirService = $fhirService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/performances",
     *     summary="Get all performances",
     *     tags={"Performance Management"},
     *     @OA\Parameter(
     *         name="player_id",
     *         in="query",
     *         description="Filter by player ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="data_source",
     *         in="query",
     *         description="Filter by data source",
     *         required=false,
     *         @OA\Schema(type="string", enum={"match", "training", "assessment", "fifa_connect", "medical_device"})
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Filter from date (Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter to date (Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PlayerPerformance")),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PlayerPerformance::with(['player', 'competition', 'team']);

            // Filtres
            if ($request->filled('player_id')) {
                $query->where('player_id', $request->player_id);
            }

            if ($request->filled('data_source')) {
                $query->where('data_source', $request->data_source);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('performance_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('performance_date', '<=', $request->date_to);
            }

            // Tri
            $query->orderBy('performance_date', 'desc');

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $performances = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $performances->items(),
                'meta' => [
                    'current_page' => $performances->currentPage(),
                    'last_page' => $performances->lastPage(),
                    'per_page' => $performances->perPage(),
                    'total' => $performances->total(),
                    'from' => $performances->firstItem(),
                    'to' => $performances->lastItem()
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des performances',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/performances",
     *     summary="Create a new performance record",
     *     tags={"Performance Management"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PlayerPerformanceRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Performance created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Performance créée avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/PlayerPerformance")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'player_id' => 'required|exists:players,id',
                'performance_date' => 'required|date',
                'data_source' => 'required|in:match,training,assessment,fifa_connect,medical_device',
                'overall_performance_score' => 'nullable|numeric|min:0|max:100',
                'physical_score' => 'nullable|numeric|min:0|max:100',
                'technical_score' => 'nullable|numeric|min:0|max:100',
                'tactical_score' => 'nullable|numeric|min:0|max:100',
                'mental_score' => 'nullable|numeric|min:0|max:100',
                'social_score' => 'nullable|numeric|min:0|max:100',
                'competition_id' => 'nullable|exists:competitions,id',
                'team_id' => 'nullable|exists:teams,id',
                'assessment_method' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'improvement_areas' => 'nullable|string',
                'strengths_highlighted' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 400);
            }

            DB::beginTransaction();

            // Créer la performance
            $performance = PlayerPerformance::create($request->all());

            // Synchroniser avec FIFA Connect si nécessaire
            if ($request->data_source === 'fifa_connect') {
                $player = Player::find($request->player_id);
                $this->fifaService->syncPlayerData($player);
            }

            // Créer l'observation FHIR
            $fhirResult = $this->fhirService->createPerformanceObservation($performance);

            // Générer des recommandations IA
            $recommendations = $this->aiService->generateRecommendations($performance);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Performance créée avec succès',
                'data' => $performance->load(['player', 'competition', 'team']),
                'fhir_sync' => $fhirResult,
                'ai_recommendations' => $recommendations
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la performance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/performances/{id}",
     *     summary="Get a specific performance record",
     *     tags={"Performance Management"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Performance ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/PlayerPerformance")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Performance not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $performance = PlayerPerformance::with(['player', 'competition', 'team'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $performance
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Performance non trouvée',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/performances/{id}",
     *     summary="Update a performance record",
     *     tags={"Performance Management"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Performance ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PlayerPerformanceRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Performance updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Performance mise à jour avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/PlayerPerformance")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Performance not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $performance = PlayerPerformance::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'performance_date' => 'sometimes|required|date',
                'data_source' => 'sometimes|required|in:match,training,assessment,fifa_connect,medical_device',
                'overall_performance_score' => 'nullable|numeric|min:0|max:100',
                'physical_score' => 'nullable|numeric|min:0|max:100',
                'technical_score' => 'nullable|numeric|min:0|max:100',
                'tactical_score' => 'nullable|numeric|min:0|max:100',
                'mental_score' => 'nullable|numeric|min:0|max:100',
                'social_score' => 'nullable|numeric|min:0|max:100',
                'competition_id' => 'nullable|exists:competitions,id',
                'team_id' => 'nullable|exists:teams,id',
                'assessment_method' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'improvement_areas' => 'nullable|string',
                'strengths_highlighted' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 400);
            }

            $performance->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Performance mise à jour avec succès',
                'data' => $performance->load(['player', 'competition', 'team'])
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la performance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/performances/{id}",
     *     summary="Delete a performance record",
     *     tags={"Performance Management"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Performance ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Performance deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Performance supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Performance not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $performance = PlayerPerformance::findOrFail($id);
            $performance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Performance supprimée avec succès'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la performance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/performances/player/{playerId}/analytics",
     *     summary="Get player performance analytics",
     *     tags={"Performance Management"},
     *     @OA\Parameter(
     *         name="playerId",
     *         in="path",
     *         description="Player ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="Analysis period",
     *         required=false,
     *         @OA\Schema(type="string", enum={"7d", "30d", "90d", "1y"}, default="30d")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/PlayerAnalytics")
     *         )
     *     )
     * )
     */
    public function playerAnalytics(Request $request, int $playerId): JsonResponse
    {
        try {
            $player = Player::findOrFail($playerId);
            $period = $request->get('period', '30d');

            $analytics = $this->performanceService->getPlayerAnalytics($player, $period);

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/performances/bulk-import",
     *     summary="Bulk import performance data",
     *     tags={"Performance Management"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="performances", type="array", @OA\Items(ref="#/components/schemas/PlayerPerformanceRequest"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bulk import completed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Import en lot terminé"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="success", type="integer", example=95),
     *                 @OA\Property(property="failed", type="integer", example=5),
     *                 @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function bulkImport(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'performances' => 'required|array|min:1',
                'performances.*.player_id' => 'required|exists:players,id',
                'performances.*.performance_date' => 'required|date',
                'performances.*.data_source' => 'required|in:match,training,assessment,fifa_connect,medical_device'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 400);
            }

            $results = [
                'total' => count($request->performances),
                'success' => 0,
                'failed' => 0,
                'errors' => []
            ];

            DB::beginTransaction();

            foreach ($request->performances as $performanceData) {
                try {
                    PlayerPerformance::create($performanceData);
                    $results['success']++;
                } catch (Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Erreur pour la performance: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Import en lot terminé',
                'data' => $results
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import en lot',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/performances/export",
     *     summary="Export performance data",
     *     tags={"Performance Management"},
     *     @OA\Parameter(
     *         name="format",
     *         in="query",
     *         description="Export format",
     *         required=false,
     *         @OA\Schema(type="string", enum={"json", "csv", "xlsx"}, default="json")
     *     ),
     *     @OA\Parameter(
     *         name="filters",
     *         in="query",
     *         description="Export filters (JSON)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Export successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="download_url", type="string"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $format = $request->get('format', 'json');
            $filters = json_decode($request->get('filters', '{}'), true);

            $exportData = $this->performanceService->exportData($filters, $format);

            return response()->json([
                'success' => true,
                'data' => $exportData
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'export',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/performances/dashboard",
     *     summary="Get performance dashboard data",
     *     tags={"Performance Management"},
     *     @OA\Parameter(
     *         name="level",
     *         in="query",
     *         description="Dashboard level",
     *         required=false,
     *         @OA\Schema(type="string", enum={"federation", "club", "player"}, default="federation")
     *     ),
     *     @OA\Parameter(
     *         name="entity_id",
     *         in="query",
     *         description="Entity ID for club/player level",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/DashboardData")
     *         )
     *     )
     * )
     */
    public function dashboard(Request $request): JsonResponse
    {
        try {
            $level = $request->get('level', 'federation');
            $entityId = $request->get('entity_id');

            $dashboardData = $this->performanceService->getDashboardData($level, $entityId);

            return response()->json([
                'success' => true,
                'data' => $dashboardData
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des données du dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 