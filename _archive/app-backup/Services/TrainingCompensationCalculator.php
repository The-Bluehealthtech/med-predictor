<?php

namespace App\Services;

use App\Models\Club;
use App\Models\Player;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TrainingCompensationCalculator
{
    private const CACHE_TTL = 300; // 5 minutes

    // Barèmes FIFA par catégorie de club (à paramétrer en DB)
    private const BAREMES_EUR = [
        'I' => 90000,    // Clubs de première division
        'II' => 60000,   // Clubs de deuxième division
        'III' => 30000,  // Clubs de troisième division
        'IV' => 10000    // Clubs amateurs
    ];

    // Tranche d'âge de formation FIFA
    private const AGE_MIN = 12;
    private const AGE_MAX = 21;

    /**
     * Calcule les primes de formation pour un joueur
     */
    public function calculateTrainingCompensation(int $playerId, array $licenseHistory): array
    {
        $cacheKey = "training_compensation:{$playerId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($playerId, $licenseHistory) {
            return $this->computeCompensation($playerId, $licenseHistory);
        });
    }

    /**
     * Calcule la compensation par club formateur
     */
    private function computeCompensation(int $playerId, array $licenseHistory): array
    {
        try {
            $player = Player::find($playerId);
            if (!$player) {
                Log::warning("Joueur {$playerId} non trouvé pour le calcul des primes");
                return [];
            }

            $montants = [];
            $clubStats = [];

            foreach ($licenseHistory as $license) {
                if (empty($license['date_debut']) || empty($license['club'])) {
                    continue;
                }

                $clubName = $license['club'];
                $clubId = $this->resolveClubId($clubName);
                $clubCategory = $this->getClubCategory($clubId);

                // Calculer la durée de formation
                $debut = \Carbon\Carbon::parse($license['date_debut']);
                $fin = $license['date_fin'] ? \Carbon\Carbon::parse($license['date_fin']) : now();
                
                // Vérifier si la période est dans la tranche d'âge de formation
                if ($this->isFormationPeriod($player, $debut, $fin)) {
                    $saisons = $this->calculateSeasons($debut, $fin);
                    $montantSaison = self::BAREMES_EUR[$clubCategory] ?? self::BAREMES_EUR['II'];
                    
                    if (!isset($montants[$clubId])) {
                        $montants[$clubId] = 0;
                        $clubStats[$clubId] = [
                            'club_nom' => $clubName,
                            'club_id' => $clubId,
                            'saisons_total' => 0,
                            'montant_par_saison' => $montantSaison
                        ];
                    }
                    
                    $montants[$clubId] += $saisons * $montantSaison;
                    $clubStats[$clubId]['saisons_total'] += $saisons;
                }
            }

            // Formater le résultat
            $result = [];
            foreach ($montants as $clubId => $montant) {
                $result[] = [
                    'club_id' => $clubId,
                    'club_nom' => $clubStats[$clubId]['club_nom'],
                    'montant_eur' => round($montant, 2),
                    'saisons_total' => round($clubStats[$clubId]['saisons_total'], 2),
                    'montant_par_saison' => $clubStats[$clubId]['montant_par_saison'],
                    'categorie_club' => $this->getClubCategory($clubId)
                ];
            }

            // Trier par montant décroissant
            usort($result, function ($a, $b) {
                return $b['montant_eur'] <=> $a['montant_eur'];
            });

            Log::info("Primes de formation calculées pour le joueur {$playerId}", [
                'total_clubs' => count($result),
                'total_montant' => array_sum(array_column($result, 'montant_eur'))
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error("Erreur lors du calcul des primes de formation pour le joueur {$playerId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [];
        }
    }

    /**
     * Résout l'ID du club à partir du nom
     */
    private function resolveClubId(string $clubName): int
    {
        $club = Club::where('name', $clubName)->first();
        return $club ? $club->id : hash($clubName) % 1000000; // Fallback
    }

    /**
     * Obtient la catégorie d'un club
     */
    private function getClubCategory(int $clubId): string
    {
        // TODO: Implémenter la logique de catégorisation des clubs
        // Pour l'instant, utiliser une logique simple basée sur l'ID
        $club = Club::find($clubId);
        
        if ($club) {
            // Logique basée sur la division ou la réputation
            if ($club->division === '1' || ($club->reputation && $club->reputation >= 80)) {
                return 'I';
            } elseif ($club->division === '2' || ($club->reputation && $club->reputation >= 60)) {
                return 'II';
            } elseif ($club->division === '3' || ($club->reputation && $club->reputation >= 40)) {
                return 'III';
            }
        }
        
        return 'II'; // Catégorie par défaut
    }

    /**
     * Vérifie si une période est dans la tranche d'âge de formation
     */
    private function isFormationPeriod(Player $player, \Carbon\Carbon $debut, \Carbon\Carbon $fin): bool
    {
        if (!$player->date_of_birth) {
            // Si pas de date de naissance, considérer toute la période
            return true;
        }

        $dateNaissance = \Carbon\Carbon::parse($player->date_of_birth);
        
        // Calculer l'âge au début et à la fin de la licence
        $ageDebut = $debut->diffInYears($dateNaissance);
        $ageFin = $fin->diffInYears($dateNaissance);
        
        // Vérifier si la période chevauche la tranche d'âge de formation
        return ($ageDebut <= self::AGE_MAX && $ageFin >= self::AGE_MIN);
    }

    /**
     * Calcule le nombre de saisons couvertes
     */
    private function calculateSeasons(\Carbon\Carbon $debut, \Carbon\Carbon $fin): float
    {
        $jours = $debut->diffInDays($fin);
        $saisons = $jours / 365.25; // Année bissextile
        
        return max($saisons, 0.0);
    }

    /**
     * Obtient les barèmes actuels
     */
    public static function getBarèmes(): array
    {
        return self::BAREMES_EUR;
    }

    /**
     * Met à jour les barèmes (pour configuration future)
     */
    public static function updateBarèmes(array $nouveauxBarèmes): void
    {
        // TODO: Implémenter la mise à jour des barèmes depuis la DB
        Log::info("Mise à jour des barèmes de formation", ['nouveaux_barèmes' => $nouveauxBarèmes]);
    }
}

