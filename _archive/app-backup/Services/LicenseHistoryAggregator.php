<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PlayerLicenseHistory;
use App\Models\PlayerPassport;
use App\Models\PlayerLicense;
use App\Services\FifaTmsLicenseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LicenseHistoryAggregator
{
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Obtient l'historique complet des licences d'un joueur
     */
    public function __construct(
        private FifaTmsLicenseService $fifaTmsService
    ) {
    }

    /**
     * Obtient l'historique complet des licences d'un joueur
     */
    public function getPlayerLicenseHistory(int $playerId): array
    {
        $cacheKey = "license_history:{$playerId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($playerId) {
            return $this->aggregateLicenseData($playerId);
        });
    }

    /**
     * Agrège les données de toutes les sources
     */
    private function aggregateLicenseData(int $playerId): array
    {
        $rows = [];

        try {
            // 1. Données FIT : Table PlayerLicenseHistory
            $fitData = $this->getFITLicenseData($playerId);
            $rows = array_merge($rows, $fitData);
            Log::info("Données FIT PlayerLicenseHistory récupérées pour le joueur {$playerId}", ['count' => count($fitData)]);

            // 2. Données FIT : Table PlayerLicenses
            $fitLicenses = $this->getFITPlayerLicenses($playerId);
            $rows = array_merge($rows, $fitLicenses);
            Log::info("Données FIT PlayerLicenses récupérées pour le joueur {$playerId}", ['count' => count($fitLicenses)]);

            // 3. Données FIT : Table PlayerPassports
            $fitPassports = $this->getFITPassportData($playerId);
            $rows = array_merge($rows, $fitPassports);
            Log::info("Données FIT PlayerPassports récupérées pour le joueur {$playerId}", ['count' => count($fitPassports)]);

            // 4. APIs externes (optionnelles)
            $externalData = $this->getExternalAPIData($playerId);
            $rows = array_merge($rows, $externalData);
            Log::info("Données externes récupérées pour le joueur {$playerId}", ['count' => count($externalData)]);

            // Normalisation et tri
            $rows = $this->normalizeAndSortData($rows);

            // Déduplication
            $rows = $this->deduplicateData($rows);

            Log::info("Historique des licences agrégé pour le joueur {$playerId}", [
                'total_records' => count($rows),
                'sources' => array_unique(array_column($rows, 'source_donnee'))
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'agrégation des licences pour le joueur {$playerId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Retourner les données disponibles même en cas d'erreur partielle
            $rows = $this->getFITLicenseData($playerId);
        }

        return $rows;
    }

    /**
     * Récupère les données de la table PlayerLicenseHistory
     */
    private function getFITLicenseData(int $playerId): array
    {
        $licenses = PlayerLicenseHistory::where('player_id', $playerId)
            ->with(['club', 'association'])
            ->orderBy('date_debut')
            ->get();

        $rows = [];
        foreach ($licenses as $license) {
            $rows[] = [
                'date_debut' => $license->date_debut->format('Y-m-d'),
                'date_fin' => $license->date_fin?->format('Y-m-d'),
                'club' => $license->club?->name ?? 'Club inconnu',
                'association' => $license->association?->name ?? 'Association inconnue',
                'type_licence' => $license->type_licence,
                'source_donnee' => $license->source_donnee,
                'license_number' => $license->license_number,
                'status' => $license->status
            ];
        }

        return $rows;
    }

    /**
     * Récupère les données de la table PlayerLicenses
     */
    private function getFITPlayerLicenses(int $playerId): array
    {
        $licenses = PlayerLicense::where('player_id', $playerId)
            ->with(['club:id,name', 'association:id,name'])
            ->orderBy('issue_date')
            ->get();

        $rows = [];
        foreach ($licenses as $license) {
            // Déterminer le type de licence
            $type = $this->determineLicenseType($license->license_type, $license->license_category);
            
            $rows[] = [
                'date_debut' => $license->issue_date?->format('Y-m-d'),
                'date_fin' => $license->expiry_date?->format('Y-m-d'),
                'club' => $license->club?->name ?? 'Club inconnu',
                'association' => $license->association?->name ?? 'Association inconnue',
                'type_licence' => $type,
                'source_donnee' => 'FIT',
                'license_number' => $license->license_number,
                'status' => $license->status
            ];
        }

        return $rows;
    }

    /**
     * Récupère les données de la table PlayerPassports
     */
    private function getFITPassportData(int $playerId): array
    {
        $passports = PlayerPassport::where('player_id', $playerId)
            ->with(['club:id,name', 'association:id,name'])
            ->orderBy('license_issue_date')
            ->get();

        $rows = [];
        foreach ($passports as $passport) {
            if ($passport->license_issue_date) {
                $rows[] = [
                    'date_debut' => $passport->license_issue_date->format('Y-m-d'),
                    'date_fin' => $passport->license_expiry_date?->format('Y-m-d'),
                    'club' => $passport->club?->name ?? 'Club inconnu',
                    'association' => $passport->association?->name ?? 'Association inconnue',
                    'type_licence' => $this->determineLicenseType($passport->license_type, $passport->license_category),
                    'source_donnee' => 'FIT',
                    'license_number' => $passport->registration_number,
                    'status' => $passport->license_status
                ];
            }
        }

        return $rows;
    }

    /**
     * Récupère les données des APIs externes
     */
    private function getExternalAPIData(int $playerId): array
    {
        $rows = [];

        try {
            // 1. API FIFA locale (données de base)
            $fifaData = $this->fetchFIFALicenseData($playerId);
            if (!empty($fifaData)) {
                $rows = array_merge($rows, $fifaData);
                Log::info("Données FIFA locales récupérées pour le joueur {$playerId}", ['count' => count($fifaData)]);
            }

            // 2. API FIFA TMS (licences officielles)
            $tmsData = $this->fetchFifaTmsLicenseData($playerId);
            if (!empty($tmsData)) {
                $rows = array_merge($rows, $tmsData);
                Log::info("Données FIFA TMS récupérées pour le joueur {$playerId}", ['count' => count($tmsData)]);
            }

            // 3. API Nationale (placeholder - à implémenter quand disponible)
            $nationalData = $this->fetchNationalLicenseData($playerId);
            if (!empty($nationalData)) {
                $rows = array_merge($rows, $nationalData);
                Log::info("Données nationales récupérées pour le joueur {$playerId}", ['count' => count($nationalData)]);
            }

        } catch (\Exception $e) {
            Log::warning("Erreur lors de la récupération des données externes pour le joueur {$playerId}", [
                'error' => $e->getMessage()
            ]);
        }

        return $rows;
    }

    /**
     * Récupère les données de l'API FIFA locale
     */
    private function fetchFIFALicenseData(int $playerId): array
    {
        try {
            // Appeler l'API FIFA locale avec un timeout court
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get("http://localhost:8001/api/fifa/player/{$playerId}");
            
            if (!$response->successful()) {
                Log::warning("Impossible de récupérer les données FIFA pour le joueur {$playerId}", [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return [];
            }

            $fifaData = $response->json();
            
            if (!isset($fifaData['success']) || !$fifaData['success'] || !isset($fifaData['data'])) {
                Log::warning("Données FIFA invalides pour le joueur {$playerId}", [
                    'response' => $fifaData
                ]);
                return [];
            }

            $player = $fifaData['data'];
            $rows = [];

            // Créer une licence basée sur les données FIFA actuelles
            if (isset($player['club']) && isset($player['nationality'])) {
                $rows[] = [
                    'date_debut' => '2020-01-01', // Date par défaut
                    'date_fin' => null, // Licence actuelle
                    'club' => $player['club']['name'] ?? 'Club inconnu',
                    'association' => $player['nationality'] ?? 'Association inconnue',
                    'type_licence' => 'Pro',
                    'source_donnee' => 'FIFA API',
                    'license_number' => null,
                    'status' => 'active'
                ];
            }

            Log::info("Données FIFA récupérées pour le joueur {$playerId}", [
                'total_licenses' => count($rows)
            ]);

            return $rows;

        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des données FIFA pour le joueur {$playerId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Récupère les données de l'API FIFA TMS
     */
    private function fetchFifaTmsLicenseData(int $playerId): array
    {
        try {
            // Récupérer le FIFA ID du joueur
            $player = Player::find($playerId);
            if (!$player || !$player->fifa_id) {
                Log::info("Joueur {$playerId} n'a pas de FIFA ID, impossible de récupérer les données TMS");
                return [];
            }

            // Récupérer les licences depuis FIFA TMS
            $tmsLicenses = $this->fifaTmsService->getPlayerLicenses($player->fifa_id);
            
            if (!empty($tmsLicenses)) {
                Log::info("Licences FIFA TMS récupérées pour le joueur {$playerId} (FIFA ID: {$player->fifa_id})", [
                    'count' => count($tmsLicenses)
                ]);
            }

            return $tmsLicenses;

        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des données FIFA TMS pour le joueur {$playerId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Placeholder pour l'API Nationale
     */
    private function fetchNationalLicenseData(int $playerId): array
    {
        // TODO: Implémenter quand l'API nationale sera disponible
        return [];
    }

    /**
     * Détermine le type de licence basé sur les champs disponibles
     */
    private function determineLicenseType(?string $type, ?string $category): string
    {
        if ($type) {
            return match(strtolower($type)) {
                'pro', 'professionnel', 'professional' => 'Pro',
                'amateur', 'amateur' => 'Amateur',
                'jeunes', 'youth', 'junior' => 'Jeunes',
                default => 'Pro'
            };
        }

        if ($category) {
            return match(strtolower($category)) {
                'senior', 'adulte' => 'Pro',
                'amateur', 'recreationnel' => 'Amateur',
                'jeunes', 'cadet', 'minime', 'benjamin' => 'Jeunes',
                default => 'Pro'
            };
        }

        return 'Pro'; // Par défaut
    }

    /**
     * Normalise et trie les données
     */
    private function normalizeAndSortData(array $rows): array
    {
        // Filtrer les enregistrements sans date de début
        $rows = array_filter($rows, function ($row) {
            return !empty($row['date_debut']);
        });

        // Trier par date de début (croissant)
        usort($rows, function ($a, $b) {
            return strcmp($a['date_debut'], $b['date_debut']);
        });

        return $rows;
    }

    /**
     * Déduplique les données
     */
    private function deduplicateData(array $rows): array
    {
        $seen = [];
        $deduplicated = [];

        foreach ($rows as $row) {
            $key = $row['date_debut'] . '|' . 
                   ($row['date_fin'] ?? 'null') . '|' . 
                   $row['club'] . '|' . 
                   $row['association'] . '|' . 
                   $row['type_licence'];

            if (!in_array($key, $seen)) {
                $seen[] = $key;
                $deduplicated[] = $row;
            }
        }

        return $deduplicated;
    }
}

