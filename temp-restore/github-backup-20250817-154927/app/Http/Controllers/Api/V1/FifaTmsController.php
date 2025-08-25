<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\FifaTmsLicenseService;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FifaTmsController extends Controller
{
    private FifaTmsLicenseService $fifaTmsService;

    public function __construct(FifaTmsLicenseService $fifaTmsService)
    {
        $this->fifaTmsService = $fifaTmsService;
    }

    /**
     * Teste la connectivité à l'API FIFA TMS
     */
    public function testConnectivity(): JsonResponse
    {
        try {
            $result = $this->fifaTmsService->testConnectivity();
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => $result['connected'] ? 'Connecté à FIFA TMS' : 'Erreur de connexion'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors du test de connectivité FIFA TMS', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du test de connectivité',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
            ], 500);
        }
    }

    /**
     * Récupère les licences d'un joueur depuis FIFA TMS
     */
    public function getPlayerLicenses(Request $request, int $playerId): JsonResponse
    {
        try {
            // Vérifier que le joueur existe
            $player = Player::find($playerId);
            if (!$player) {
                return response()->json([
                    'success' => false,
                    'message' => 'Joueur non trouvé',
                    'player_id' => $playerId
                ], 404);
            }

            // Vérifier que le joueur a un FIFA ID
            if (!$player->fifa_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le joueur n\'a pas de FIFA ID',
                    'player_id' => $playerId,
                    'player_name' => $player->first_name . ' ' . $player->last_name
                ], 400);
            }

            // Récupérer les licences depuis FIFA TMS
            $licenses = $this->fifaTmsService->getPlayerLicenses($player->fifa_id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'player_id' => $playerId,
                    'fifa_id' => $player->fifa_id,
                    'player_name' => $player->first_name . ' ' . $player->last_name,
                    'licenses' => $licenses,
                    'total_licenses' => count($licenses),
                    'source' => 'FIFA TMS',
                    'retrieved_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des licences FIFA TMS', [
                'player_id' => $playerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des licences',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
            ], 500);
        }
    }

    /**
     * Récupère l'historique des transferts d'un joueur depuis FIFA TMS
     */
    public function getPlayerTransferHistory(Request $request, int $playerId): JsonResponse
    {
        try {
            // Vérifier que le joueur existe
            $player = Player::find($playerId);
            if (!$player) {
                return response()->json([
                    'success' => false,
                    'message' => 'Joueur non trouvé',
                    'player_id' => $playerId
                ], 404);
            }

            // Vérifier que le joueur a un FIFA ID
            if (!$player->fifa_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le joueur n\'a pas de FIFA ID',
                    'player_id' => $playerId,
                    'player_name' => $player->first_name . ' ' . $player->last_name
                ], 400);
            }

            // Récupérer l'historique des transferts depuis FIFA TMS
            $transfers = $this->fifaTmsService->getPlayerTransferHistory($player->fifa_id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'player_id' => $playerId,
                    'fifa_id' => $player->fifa_id,
                    'player_name' => $player->first_name . ' ' . $player->last_name,
                    'transfers' => $transfers,
                    'total_transfers' => count($transfers),
                    'source' => 'FIFA TMS',
                    'retrieved_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de l\'historique des transferts FIFA TMS', [
                'player_id' => $playerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'historique des transferts',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
            ], 500);
        }
    }

    /**
     * Synchronise toutes les données FIFA TMS pour un joueur
     */
    public function syncPlayerData(Request $request, int $playerId): JsonResponse
    {
        try {
            // Vérifier que le joueur existe
            $player = Player::find($playerId);
            if (!$player) {
                return response()->json([
                    'success' => false,
                    'message' => 'Joueur non trouvé',
                    'player_id' => $playerId
                ], 404);
            }

            // Vérifier que le joueur a un FIFA ID
            if (!$player->fifa_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le joueur n\'a pas de FIFA ID',
                    'player_id' => $playerId,
                    'player_name' => $player->first_name . ' ' . $player->last_name
                ], 400);
            }

            // Récupérer toutes les données FIFA TMS
            $licenses = $this->fifaTmsService->getPlayerLicenses($player->fifa_id);
            $transfers = $this->fifaTmsService->getPlayerTransferHistory($player->fifa_id);
            
            $syncResult = [
                'player_id' => $playerId,
                'fifa_id' => $player->fifa_id,
                'player_name' => $player->first_name . ' ' . $player->last_name,
                'licenses' => $licenses,
                'transfers' => $transfers,
                'total_licenses' => count($licenses),
                'total_transfers' => count($transfers),
                'source' => 'FIFA TMS',
                'synced_at' => now()->toISOString()
            ];

            Log::info('Données FIFA TMS synchronisées avec succès', [
                'player_id' => $playerId,
                'fifa_id' => $player->fifa_id,
                'licenses_count' => count($licenses),
                'transfers_count' => count($transfers)
            ]);

            return response()->json([
                'success' => true,
                'data' => $syncResult,
                'message' => 'Données FIFA TMS synchronisées avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la synchronisation des données FIFA TMS', [
                'player_id' => $playerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la synchronisation des données',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
            ], 500);
        }
    }
}
