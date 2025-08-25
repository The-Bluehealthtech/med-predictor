<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class FifaTmsLicenseService
{
    protected $baseUrl;
    protected $apiKey;
    protected $timeout;
    protected $mockMode;

    public function __construct()
    {
        $this->baseUrl = config('services.fifa_tms.base_url', 'https://api.fifa.com/tms/v1');
        $this->apiKey = config('services.fifa_tms.api_key');
        $this->timeout = config('services.fifa_tms.timeout', 15);
        $this->mockMode = config('services.fifa_tms.mock_mode', false) || config('app.env') === 'local';
    }

    /**
     * Récupère les licences d'un joueur depuis l'API FIFA TMS
     */
    public function getPlayerLicenses(string $fifaId): array
    {
        try {
            // Vérifier le cache d'abord
            $cacheKey = "fifa_tms_licenses_{$fifaId}";
            if (Cache::has($cacheKey)) {
                Log::info("Licences FIFA TMS récupérées depuis le cache pour le joueur {$fifaId}");
                return Cache::get($cacheKey);
            }

            if ($this->mockMode) {
                return $this->getMockLicenses($fifaId);
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                    'X-FIFA-API-Version' => '2024',
                ])
                ->get("{$this->baseUrl}/players/{$fifaId}/licenses");

            if ($response->successful()) {
                $data = $response->json();
                $licenses = $this->normalizeTmsLicenses($data);
                
                // Mettre en cache pour 1 heure
                Cache::put($cacheKey, $licenses, 3600);
                
                Log::info("Licences FIFA TMS récupérées avec succès pour le joueur {$fifaId}", [
                    'count' => count($licenses),
                    'sources' => array_unique(array_column($licenses, 'source_donnee'))
                ]);
                
                return $licenses;
            }

            throw new Exception("FIFA TMS API error: " . $response->status() . " - " . $response->body());

        } catch (Exception $e) {
            Log::error("Erreur lors de la récupération des licences FIFA TMS pour le joueur {$fifaId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // En cas d'erreur, retourner un tableau vide pour ne pas bloquer l'application
            return [];
        }
    }

    /**
     * Récupère l'historique des transferts d'un joueur depuis FIFA TMS
     */
    public function getPlayerTransferHistory(string $fifaId): array
    {
        try {
            $cacheKey = "fifa_tms_transfers_{$fifaId}";
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            if ($this->mockMode) {
                return $this->getMockTransferHistory($fifaId);
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                    'X-FIFA-API-Version' => '2024',
                ])
                ->get("{$this->baseUrl}/players/{$fifaId}/transfers");

            if ($response->successful()) {
                $data = $response->json();
                $transfers = $this->normalizeTmsTransfers($data);
                
                Cache::put($cacheKey, $transfers, 3600);
                return $transfers;
            }

            throw new Exception("FIFA TMS Transfers API error: " . $response->status());

        } catch (Exception $e) {
            Log::error("Erreur lors de la récupération de l'historique des transferts FIFA TMS", [
                'fifa_id' => $fifaId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Normalise les données de licences FIFA TMS
     */
    private function normalizeTmsLicenses(array $tmsData): array
    {
        $licenses = [];
        
        if (!isset($tmsData['licenses']) || !is_array($tmsData['licenses'])) {
            return $licenses;
        }

        foreach ($tmsData['licenses'] as $license) {
            $licenses[] = [
                'date_debut' => $license['start_date'] ?? null,
                'date_fin' => $license['end_date'] ?? null,
                'club' => $license['club']['name'] ?? $license['club_name'] ?? 'Club inconnu',
                'association' => $license['federation']['name'] ?? $license['federation_name'] ?? 'Association inconnue',
                'type_licence' => $this->mapLicenseType($license['type'] ?? 'unknown'),
                'source_donnee' => 'FIFA TMS',
                'license_number' => $license['license_number'] ?? $license['id'] ?? null,
                'status' => $this->mapLicenseStatus($license['status'] ?? 'active'),
                'metadata' => [
                    'fifa_tms_id' => $license['id'] ?? null,
                    'license_category' => $license['category'] ?? null,
                    'transfer_fee' => $license['transfer_fee'] ?? null,
                    'contract_type' => $license['contract_type'] ?? null,
                    'registration_date' => $license['registration_date'] ?? null,
                ]
            ];
        }

        return $licenses;
    }

    /**
     * Normalise les données de transferts FIFA TMS
     */
    private function normalizeTmsTransfers(array $tmsData): array
    {
        $transfers = [];
        
        if (!isset($tmsData['transfers']) || !is_array($tmsData['transfers'])) {
            return $transfers;
        }

        foreach ($tmsData['transfers'] as $transfer) {
            $transfers[] = [
                'date_transfer' => $transfer['transfer_date'] ?? null,
                'club_depart' => $transfer['from_club']['name'] ?? 'Club inconnu',
                'club_arrivee' => $transfer['to_club']['name'] ?? 'Club inconnu',
                'type_transfer' => $transfer['transfer_type'] ?? 'Permanent',
                'montant' => $transfer['transfer_fee'] ?? null,
                'devise' => $transfer['currency'] ?? 'EUR',
                'status' => $transfer['status'] ?? 'Completed',
                'source_donnee' => 'FIFA TMS',
                'metadata' => [
                    'fifa_tms_id' => $transfer['id'] ?? null,
                    'transfer_window' => $transfer['transfer_window'] ?? null,
                    'contract_end_date' => $transfer['contract_end_date'] ?? null,
                ]
            ];
        }

        return $transfers;
    }

    /**
     * Mappe les types de licences FIFA TMS vers nos types
     */
    private function mapLicenseType(string $fifaType): string
    {
        return match(strtolower($fifaType)) {
            'professional', 'pro' => 'Pro',
            'amateur', 'am' => 'Amateur',
            'youth', 'junior', 'u21', 'u19', 'u17' => 'Jeunes',
            'temporary', 'temp' => 'Temporaire',
            'emergency' => 'Urgence',
            default => 'Pro'
        };
    }

    /**
     * Mappe les statuts de licences FIFA TMS
     */
    private function mapLicenseStatus(string $fifaStatus): string
    {
        return match(strtolower($fifaStatus)) {
            'active', 'valid' => 'active',
            'expired', 'invalid' => 'expired',
            'suspended' => 'suspended',
            'cancelled', 'revoked' => 'cancelled',
            'pending' => 'pending',
            default => 'active'
        };
    }

    /**
     * Données de test pour le mode mock
     */
    private function getMockLicenses(string $fifaId): array
    {
        Log::info("Mode mock activé - Génération de licences de test pour le joueur {$fifaId}");
        
        return [
            [
                'date_debut' => '2020-07-01',
                'date_fin' => null,
                'club' => 'US Monastir',
                'association' => 'FTF (Tunisie)',
                'type_licence' => 'Pro',
                'source_donnee' => 'FIFA TMS (Mock)',
                'license_number' => 'TMS-' . $fifaId . '-001',
                'status' => 'active',
                'metadata' => [
                    'fifa_tms_id' => 'MOCK_TMS_001',
                    'license_category' => 'Professional',
                    'contract_type' => 'Permanent',
                ]
            ],
            [
                'date_debut' => '2018-08-01',
                'date_fin' => '2020-06-30',
                'club' => 'Club Africain',
                'association' => 'FTF (Tunisie)',
                'type_licence' => 'Pro',
                'source_donnee' => 'FIFA TMS (Mock)',
                'license_number' => 'TMS-' . $fifaId . '-002',
                'status' => 'expired',
                'metadata' => [
                    'fifa_tms_id' => 'MOCK_TMS_002',
                    'license_category' => 'Professional',
                    'contract_type' => 'Permanent',
                ]
            ]
        ];
    }

    /**
     * Données de test pour l'historique des transferts
     */
    private function getMockTransferHistory(string $fifaId): array
    {
        return [
            [
                'date_transfer' => '2020-07-01',
                'club_depart' => 'Club Africain',
                'club_arrivee' => 'US Monastir',
                'type_transfer' => 'Permanent',
                'montant' => 500000,
                'devise' => 'EUR',
                'status' => 'Completed',
                'source_donnee' => 'FIFA TMS (Mock)',
            ]
        ];
    }

    /**
     * Teste la connectivité à l'API FIFA TMS
     */
    public function testConnectivity(): array
    {
        if ($this->mockMode) {
            return [
                'connected' => true,
                'status' => 'mock',
                'response_time' => 0.1,
                'timestamp' => now()->toISOString(),
                'mock_mode' => true
            ];
        }

        try {
            $startTime = microtime(true);
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/health");

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($response->successful()) {
                return [
                    'connected' => true,
                    'status' => 'connected',
                    'response_time' => $responseTime,
                    'timestamp' => now()->toISOString(),
                    'api_version' => $response->header('X-FIFA-API-Version'),
                    'mock_mode' => false
                ];
            }

            return [
                'connected' => false,
                'status' => 'error',
                'response_time' => $responseTime,
                'timestamp' => now()->toISOString(),
                'error' => "HTTP {$response->status()}: {$response->body()}",
                'mock_mode' => false
            ];

        } catch (Exception $e) {
            return [
                'connected' => false,
                'status' => 'error',
                'response_time' => null,
                'timestamp' => now()->toISOString(),
                'error' => $e->getMessage(),
                'mock_mode' => false
            ];
        }
    }
}
