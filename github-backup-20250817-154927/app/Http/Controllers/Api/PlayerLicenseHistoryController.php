<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\PlayerLicense;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlayerLicenseHistoryController extends Controller
{
    /**
     * Récupère l'historique complet des licences d'un joueur
     * avec calcul des primes FIFA
     */
    public function getLicenseHistory(int $playerId): JsonResponse
    {
        try {
            // Vérifier que le joueur existe
            $player = Player::with(['club', 'association'])->find($playerId);
            
            if (!$player) {
                return response()->json([
                    'success' => false,
                    'message' => 'Joueur non trouvé'
                ], 404);
            }

            // Récupérer toutes les licences du joueur
            $licenses = PlayerLicense::where('player_id', $playerId)
                ->with(['club', 'club.association'])
                ->orderBy('issued_date', 'desc')
                ->get();

            // Récupérer la licence actuelle
            $currentLicense = $licenses->where('status', 'active')->first();

            // Calculer les primes FIFA
            $fifaPrimes = $this->calculateFifaTrainingPrimes($licenses, $player);

            // Structurer la réponse
            $response = [
                'success' => true,
                'data' => [
                    'player' => [
                        'id' => $player->id,
                        'name' => $player->first_name . ' ' . $player->last_name,
                        'nationality' => $player->nationality,
                        'current_club' => $player->club ? [
                            'id' => $player->club->id,
                            'name' => $player->club->name,
                            'country' => $player->club->country
                        ] : null,
                        'association' => $player->association ? [
                            'id' => $player->association->id,
                            'name' => $player->association->name,
                            'country' => $player->association->country
                        ] : null
                    ],
                    'current_license' => $currentLicense ? $this->formatLicense($currentLicense) : null,
                    'license_history' => $licenses->map(function ($license) {
                        return $this->formatLicense($license);
                    }),
                    'fifa_training_primes' => $fifaPrimes,
                    'summary' => [
                        'total_licenses' => $licenses->count(),
                        'active_licenses' => $licenses->where('status', 'active')->count(),
                        'total_primes' => collect($fifaPrimes)->sum('amount'),
                        'clubs_involved' => $licenses->pluck('club.name')->unique()->count()
                    ]
                ]
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'historique',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcule les primes FIFA selon les règles officielles
     */
    private function calculateFifaTrainingPrimes($licenses, $player): array
    {
        $primes = [];
        $trainingClubs = [];

        foreach ($licenses as $license) {
            if (!$license->club) continue;

            $clubId = $license->club->id;
            $clubName = $license->club->name;
            
            if (!isset($trainingClubs[$clubId])) {
                $trainingClubs[$clubId] = [
                    'club_id' => $clubId,
                    'club_name' => $clubName,
                    'country' => $license->club->country,
                    'first_season' => null,
                    'last_season' => null,
                    'total_seasons' => 0,
                    'age_range' => [],
                    'prime_amount' => 0
                ];
            }

            // Calculer l'âge du joueur à cette période
            $licenseDate = $license->issued_date ? Carbon::parse($license->issued_date) : null;
            if ($licenseDate) {
                $playerBirthDate = $player->date_of_birth ? Carbon::parse($player->date_of_birth) : null;
                if ($playerBirthDate) {
                    $age = $licenseDate->diffInYears($playerBirthDate);
                    $trainingClubs[$clubId]['age_range'][] = $age;
                }
            }

            // Compter les saisons
            if ($license->season) {
                $trainingClubs[$clubId]['total_seasons']++;
                
                if (!$trainingClubs[$clubId]['first_season'] || 
                    $license->season < $trainingClubs[$clubId]['first_season']) {
                    $trainingClubs[$clubId]['first_season'] = $license->season;
                }
                
                if (!$trainingClubs[$clubId]['last_season'] || 
                    $license->season > $trainingClubs[$clubId]['last_season']) {
                    $trainingClubs[$clubId]['last_season'] = $license->season;
                }
            }
        }

        // Calculer les primes selon les règles FIFA
        foreach ($trainingClubs as $clubId => $club) {
            $primeAmount = $this->calculateClubPrime($club, $player);
            $trainingClubs[$clubId]['prime_amount'] = $primeAmount;
            
            $primes[] = [
                'club_id' => $club['club_id'],
                'club_name' => $club['club_name'],
                'country' => $club['country'],
                'training_period' => [
                    'from' => $club['first_season'],
                    'to' => $club['last_season'],
                    'seasons' => $club['total_seasons']
                ],
                'age_range' => [
                    'min' => !empty($club['age_range']) ? min($club['age_range']) : null,
                    'max' => !empty($club['age_range']) ? max($club['age_range']) : null
                ],
                'amount' => $primeAmount,
                'currency' => 'EUR',
                'calculation_details' => $this->getPrimeCalculationDetails($club, $primeAmount)
            ];
        }

        // Trier par montant décroissant
        usort($primes, function($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });

        return $primes;
    }

    /**
     * Calcule la prime pour un club formateur selon les règles FIFA
     */
    private function calculateClubPrime(array $club, $player): float
    {
        $baseAmount = 0;
        $seasons = $club['total_seasons'];
        $avgAge = !empty($club['age_range']) ? array_sum($club['age_range']) / count($club['age_range']) : 18;

        // Règles FIFA simplifiées pour les primes de formation
        // Ces montants sont des exemples et doivent être adaptés aux vraies règles FIFA
        
        if ($avgAge >= 12 && $avgAge <= 15) {
            // Formation précoce (12-15 ans)
            $baseAmount = 5000 * $seasons;
        } elseif ($avgAge >= 16 && $avgAge <= 18) {
            // Formation junior (16-18 ans)
            $baseAmount = 8000 * $seasons;
        } elseif ($avgAge >= 19 && $avgAge <= 21) {
            // Formation senior (19-21 ans)
            $baseAmount = 12000 * $seasons;
        } else {
            // Formation adulte (22+ ans)
            $baseAmount = 3000 * $seasons;
        }

        // Bonus pour la durée de formation
        if ($seasons >= 3) {
            $baseAmount *= 1.2; // +20% pour 3+ saisons
        }
        if ($seasons >= 5) {
            $baseAmount *= 1.5; // +50% pour 5+ saisons
        }

        // Bonus pour la qualité de formation (exemple)
        $qualityBonus = 1.0;
        if (in_array($club['country'], ['France', 'Allemagne', 'Espagne', 'Angleterre', 'Italie'])) {
            $qualityBonus = 1.3; // +30% pour les grandes ligues
        }

        return round($baseAmount * $qualityBonus, 2);
    }

    /**
     * Retourne les détails du calcul de la prime
     */
    private function getPrimeCalculationDetails(array $club, float $amount): array
    {
        $avgAge = !empty($club['age_range']) ? array_sum($club['age_range']) / count($club['age_range']) : 18;
        
        return [
            'base_calculation' => "Formation de {$club['total_seasons']} saison(s) entre {$club['first_season']} et {$club['last_season']}",
            'age_factor' => "Âge moyen: " . round($avgAge, 1) . " ans",
            'seasons_factor' => "Multiplicateur saisons: " . ($club['total_seasons'] >= 5 ? '1.5x' : ($club['total_seasons'] >= 3 ? '1.2x' : '1.0x')),
            'quality_factor' => "Facteur qualité: " . (in_array($club['country'], ['France', 'Allemagne', 'Espagne', 'Angleterre', 'Italie']) ? '1.3x' : '1.0x')
        ];
    }

    /**
     * Formate une licence pour l'API
     */
    private function formatLicense(PlayerLicense $license): array
    {
        return [
            'id' => $license->id,
            'license_number' => $license->license_number,
            'registration_number' => $license->registration_number,
            'type' => $license->license_type,
            'category' => $license->license_category,
            'status' => $license->status,
            'approval_status' => $license->approval_status,
            'issued_date' => $license->issued_date,
            'expiry_date' => $license->expiry_date,
            'season' => $license->season,
            'club' => $license->club ? [
                'id' => $license->club->id,
                'name' => $license->club->name,
                'country' => $license->club->country,
                'logo_url' => $license->club->logo_url
            ] : null,
            'association' => $license->club && $license->club->association ? [
                'id' => $license->club->association->id,
                'name' => $license->club->association->name,
                'country' => $license->club->association->country
            ] : null,
            'contract' => [
                'start_date' => $license->contract_start_date,
                'end_date' => $license->contract_end_date,
                'type' => $license->contract_type
            ],
            'fifa_connect_id' => $license->fifa_connect_id,
            'is_current' => $license->status === 'active',
            'created_at' => $license->created_at,
            'updated_at' => $license->updated_at
        ];
    }
}




