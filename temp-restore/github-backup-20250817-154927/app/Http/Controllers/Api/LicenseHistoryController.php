<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\LicenseHistoryAggregator;
use App\Services\TrainingCompensationCalculator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LicenseHistoryController extends Controller
{
    private LicenseHistoryAggregator $licenseAggregator;
    private TrainingCompensationCalculator $compensationCalculator;

    public function __construct(
        LicenseHistoryAggregator $licenseAggregator,
        TrainingCompensationCalculator $compensationCalculator
    ) {
        $this->licenseAggregator = $licenseAggregator;
        $this->compensationCalculator = $compensationCalculator;
    }

    /**
     * Obtient l'historique complet des licences d'un joueur
     */
    public function getPlayerLicenseHistory(Request $request, int $playerId): JsonResponse
    {
        try {
            // Validation des paramètres
            $validator = Validator::make(['player_id' => $playerId], [
                'player_id' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paramètres invalides',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Vérifier que le joueur existe
            $player = \App\Models\Player::find($playerId);
            if (!$player) {
                return response()->json([
                    'success' => false,
                    'message' => 'Joueur non trouvé',
                    'player_id' => $playerId
                ], 404);
            }

            // Récupérer l'historique des licences
            $licenses = $this->licenseAggregator->getPlayerLicenseHistory($playerId);

            // Calculer les primes de formation
            $primes = $this->compensationCalculator->calculateTrainingCompensation($playerId, $licenses);

            // Préparer la réponse
            $response = [
                'success' => true,
                'data' => [
                    'joueur_id' => $playerId,
                    'joueur_nom' => $player->first_name . ' ' . $player->last_name,
                    'licences' => $licenses,
                    'primes_formation' => $primes,
                    'generated_at' => now()->toISOString(),
                    'total_licences' => count($licenses),
                    'total_clubs_formateurs' => count($primes),
                    'montant_total_formation' => array_sum(array_column($primes, 'montant_eur'))
                ]
            ];

            Log::info("Historique des licences récupéré avec succès", [
                'player_id' => $playerId,
                'total_licenses' => count($licenses),
                'total_primes' => count($primes)
            ]);

            return response()->json($response, 200);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération de l'historique des licences", [
                'player_id' => $playerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur',
                'error' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue'
            ], 500);
        }
    }

    /**
     * Obtient les statistiques des licences
     */
    public function getLicenseStats(Request $request, int $playerId): JsonResponse
    {
        try {
            $licenses = $this->licenseAggregator->getPlayerLicenseHistory($playerId);
            
            // Statistiques par type de licence
            $statsByType = [];
            foreach ($licenses as $license) {
                $type = $license['type_licence'] ?? 'Inconnu';
                if (!isset($statsByType[$type])) {
                    $statsByType[$type] = 0;
                }
                $statsByType[$type]++;
            }

            // Statistiques par source
            $statsBySource = [];
            foreach ($licenses as $license) {
                $source = $license['source_donnee'] ?? 'Inconnue';
                if (!isset($statsBySource[$source])) {
                    $statsBySource[$source] = 0;
                }
                $statsBySource[$source]++;
            }

            // Licence actuelle
            $currentLicense = null;
            foreach ($licenses as $license) {
                if (empty($license['date_fin']) || 
                    \Carbon\Carbon::parse($license['date_fin'])->isFuture()) {
                    $currentLicense = $license;
                    break;
                }
            }

            $response = [
                'success' => true,
                'data' => [
                    'joueur_id' => $playerId,
                    'total_licences' => count($licenses),
                    'licence_actuelle' => $currentLicense,
                    'stats_par_type' => $statsByType,
                    'stats_par_source' => $statsBySource,
                    'premiere_licence' => $licenses[0] ?? null,
                    'derniere_licence' => end($licenses) ?: null
                ]
            ];

            return response()->json($response, 200);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des statistiques des licences", [
                'player_id' => $playerId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }

    /**
     * Obtient les barèmes de formation actuels
     */
    public function getTrainingBarèmes(): JsonResponse
    {
        try {
            $barèmes = TrainingCompensationCalculator::getBarèmes();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'barèmes_eur' => $barèmes,
                    'tranche_age_formation' => [
                        'age_min' => 12,
                        'age_max' => 21
                    ],
                    'description' => 'Barèmes FIFA pour les primes de formation par catégorie de club'
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des barèmes de formation", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des barèmes'
            ], 500);
        }
    }
}
