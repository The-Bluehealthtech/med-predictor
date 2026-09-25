<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class FifaConnectService
{
    protected $baseUrl;
    protected $apiKey;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.fifa_connect.base_url', 'https://api.fifa.com/v1');
        $this->apiKey = config('services.fifa_connect.api_key');
        $this->timeout = config('services.fifa_connect.timeout', 30);
    }

    /**
     * Check if mock mode is enabled
     */
    private function isMockMode(): bool
    {
        return config('services.fifa_connect.mock_mode', false) || 
               config('app.env') === 'local' && !$this->apiKey;
    }

    /**
     * Sync player data from FIFA Connect
     */
    public function syncPlayerData(string $fifaId): array
    {
        try {
            // Check cache first
            $cacheKey = "fifa_player_{$fifaId}";
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/players/{$fifaId}");

            if ($response->successful()) {
                $data = $response->json();
                
                // Cache the response for 1 hour
                Cache::put($cacheKey, $data, 3600);
                
                // Update local player record
                $this->updatePlayerFromFifaData($fifaId, $data);
                
                return $data;
            }

            throw new Exception("FIFA API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FIFA Connect sync error', [
                'fifa_id' => $fifaId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check FIFA Connect service connectivity
     */
    public function checkConnectivity(): array
    {
        // Check if mock mode is enabled
        if ($this->isMockMode()) {
            return [
                'connected' => true,
                'status' => 'mock',
                'response_time' => 0.1,
                'timestamp' => now()->toISOString(),
                'mock_mode' => true
            ];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/health");

            if ($response->successful()) {
                return [
                    'connected' => true,
                    'status' => 'online',
                    'response_time' => $response->handlerStats()['total_time'] ?? null,
                    'timestamp' => now()->toISOString()
                ];
            }

            // If we get a 503 or other server error, fall back to mock mode in development
            if (in_array($response->status(), [503, 502, 504]) && config('app.env') === 'local') {
                Log::warning('FIFA Connect service unavailable, using mock mode', [
                    'status' => $response->status(),
                    'base_url' => $this->baseUrl
                ]);
                
                return [
                    'connected' => true,
                    'status' => 'mock',
                    'response_time' => 0.1,
                    'timestamp' => now()->toISOString(),
                    'mock_mode' => true,
                    'fallback_reason' => 'Service unavailable (HTTP ' . $response->status() . ')'
                ];
            }

            return [
                'connected' => false,
                'status' => 'error',
                'error' => 'FIFA Connect service returned status: ' . $response->status(),
                'timestamp' => now()->toISOString()
            ];
        } catch (Exception $e) {
            Log::error('FIFA Connect connectivity check failed', [
                'error' => $e->getMessage(),
                'base_url' => $this->baseUrl
            ]);

            // In development, fall back to mock mode for connection errors
            if (config('app.env') === 'local') {
                Log::warning('FIFA Connect connection failed, using mock mode', [
                    'error' => $e->getMessage()
                ]);
                
                return [
                    'connected' => true,
                    'status' => 'mock',
                    'response_time' => 0.1,
                    'timestamp' => now()->toISOString(),
                    'mock_mode' => true,
                    'fallback_reason' => 'Connection failed: ' . $e->getMessage()
                ];
            }

            return [
                'connected' => false,
                'status' => 'offline',
                'error' => 'Unable to connect to FIFA Connect service: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ];
        }
    }

    /**
     * Handle FIFA API errors gracefully
     */
    public function handleApiError(Exception $e): array
    {
        Log::error('FIFA Connect API error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'success' => false,
            'error' => 'FIFA Connect service temporarily unavailable',
            'retry_after' => 300 // 5 minutes
        ];
    }

    /**
     * Cache player data for performance
     */
    public function cachePlayerData(string $fifaId, array $data, int $ttl = 3600): void
    {
        $cacheKey = "fifa_player_{$fifaId}";
        Cache::put($cacheKey, $data, $ttl);
    }

    /**
     * Validate FIFA compliance requirements
     */
    public function validateCompliance(string $fifaId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/compliance/{$fifaId}");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'compliant' => false,
                'errors' => ['Unable to verify compliance status']
            ];
        } catch (Exception $e) {
            Log::error('FIFA compliance check error', [
                'fifa_id' => $fifaId,
                'error' => $e->getMessage()
            ]);

            return [
                'compliant' => false,
                'errors' => ['Compliance check failed']
            ];
        }
    }

    /**
     * Sync club data from FIFA Connect
     */
    public function syncClubData(string $fifaClubId): array
    {
        try {
            $cacheKey = "fifa_club_{$fifaClubId}";
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/clubs/{$fifaClubId}");

            if ($response->successful()) {
                $data = $response->json();
                Cache::put($cacheKey, $data, 3600);
                
                // Update local club record
                $this->updateClubFromFifaData($fifaClubId, $data);
                
                return $data;
            }

            throw new Exception("FIFA API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FIFA club sync error', [
                'fifa_club_id' => $fifaClubId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Sync federation data from FIFA Connect
     */
    public function syncFederationData(string $fifaFederationId): array
    {
        try {
            $cacheKey = "fifa_federation_{$fifaFederationId}";
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/federations/{$fifaFederationId}");

            if ($response->successful()) {
                $data = $response->json();
                Cache::put($cacheKey, $data, 3600);
                
                // Update local federation record
                $this->updateFederationFromFifaData($fifaFederationId, $data);
                
                return $data;
            }

            throw new Exception("FIFA API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FIFA federation sync error', [
                'fifa_federation_id' => $fifaFederationId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle network timeouts
     */
    public function handleNetworkTimeout(): array
    {
        return [
            'success' => false,
            'error' => 'Network timeout occurred',
            'retry_after' => 60 // 1 minute
        ];
    }

    /**
     * Log errors for debugging
     */
    public function logError(string $context, array $data): void
    {
        Log::error("FIFA Connect {$context}", $data);
    }

    /**
     * Retry failed requests
     */
    public function retryRequest(callable $request, int $maxRetries = 3): array
    {
        $attempts = 0;
        
        while ($attempts < $maxRetries) {
            try {
                return $request();
            } catch (Exception $e) {
                $attempts++;
                Log::warning("FIFA Connect retry attempt {$attempts}", [
                    'error' => $e->getMessage()
                ]);
                
                if ($attempts >= $maxRetries) {
                    throw $e;
                }
                
                // Exponential backoff
                sleep(pow(2, $attempts));
            }
        }
    }

    /**
     * Clear cache for a specific FIFA ID
     */
    public function clearCache(string $fifaId): void
    {
        $cacheKey = "fifa_player_{$fifaId}";
        Cache::forget($cacheKey);
    }

    /**
     * Generate a FIFA Connect ID for players
     */
    public function generatePlayerId(): string
    {
        return 'FIFA_' . uniqid() . '_' . time();
    }

    /**
     * Generate a FIFA Connect ID for competitions
     */
    public function generateCompetitionId(): string
    {
        return 'COMP_' . uniqid() . '_' . time();
    }

    /**
     * Generate a FIFA Connect ID for health records
     */
    public function generateHealthRecordId(): string
    {
        return 'HEALTH_' . uniqid() . '_' . time();
    }

    /**
     * Generate a FIFA Connect ID for any entity type
     */
    public function generateFifaConnectId(string $type, string $prefix = ''): string
    {
        $type = strtoupper($type);
        $prefix = $prefix ? $prefix . '_' : '';
        return $prefix . $type . '_' . uniqid() . '_' . time();
    }

    /**
     * Sync player data (alias for syncPlayerData)
     */
    public function syncPlayer($player): array
    {
        if (is_object($player) && method_exists($player, 'fifa_id')) {
            return $this->syncPlayerData($player->fifa_id);
        }
        
        if (is_string($player)) {
            return $this->syncPlayerData($player);
        }
        
        throw new Exception('Invalid player parameter');
    }

    /**
     * Sync competition data
     */
    public function syncCompetition($competition): array
    {
        try {
            if (is_object($competition) && method_exists($competition, 'fifa_id')) {
                $fifaId = $competition->fifa_id;
            } elseif (is_string($competition)) {
                $fifaId = $competition;
            } else {
                throw new Exception('Invalid competition parameter');
            }

            $cacheKey = "fifa_competition_{$fifaId}";
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/competitions/{$fifaId}");

            if ($response->successful()) {
                $data = $response->json();
                Cache::put($cacheKey, $data, 3600);
                return $data;
            }

            throw new Exception("FIFA API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FIFA competition sync error', [
                'competition' => $competition,
                'error' => $e->getMessage()
            ]);
            return $this->handleApiError($e);
        }
    }

    /**
     * Sync health record data
     */
    public function syncHealthRecord($healthRecord): array
    {
        try {
            if (is_object($healthRecord) && method_exists($healthRecord, 'fifa_id')) {
                $fifaId = $healthRecord->fifa_id;
            } elseif (is_string($healthRecord)) {
                $fifaId = $healthRecord;
            } else {
                throw new Exception('Invalid health record parameter');
            }

            $cacheKey = "fifa_health_record_{$fifaId}";
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/health-records/{$fifaId}");

            if ($response->successful()) {
                $data = $response->json();
                Cache::put($cacheKey, $data, 3600);
                return $data;
            }

            throw new Exception("FIFA API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FIFA health record sync error', [
                'health_record' => $healthRecord,
                'error' => $e->getMessage()
            ]);
            return $this->handleApiError($e);
        }
    }

    /**
     * Bulk sync players
     */
    public function bulkSyncPlayers(array $playerIds): array
    {
        $results = [];
        foreach ($playerIds as $playerId) {
            try {
                $results[$playerId] = $this->syncPlayerData($playerId);
            } catch (Exception $e) {
                $results[$playerId] = $this->handleApiError($e);
            }
        }
        return $results;
    }

    /**
     * Fetch players from FIFA Connect
     */
    public function fetchPlayers(array $filters = [], int $page = 1, int $limit = 50): array
    {
        try {
            $query = http_build_query(array_merge($filters, ['page' => $page, 'limit' => $limit]));
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/players?{$query}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("FIFA API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FIFA fetch players error', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            return $this->handleApiError($e);
        }
    }

    /**
     * Fetch a single player from FIFA Connect
     */
    public function fetchPlayer(string $id): array
    {
        return $this->syncPlayerData($id);
    }

    /**
     * Fetch player stats from FIFA Connect
     */
    public function fetchPlayerStats(string $id): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/players/{$id}/stats");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("FIFA API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FIFA fetch player stats error', [
                'player_id' => $id,
                'error' => $e->getMessage()
            ]);
            return $this->handleApiError($e);
        }
    }

    /**
     * Clear all caches
     */
    public function clearCaches(): void
    {
        // Clear all FIFA-related caches
        $keys = Cache::get('fifa_cache_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget('fifa_cache_keys');
    }

    /**
     * Sync players with filters
     */
    public function syncPlayers(array $filters = [], int $batchSize = 50): array
    {
        try {
            $players = $this->fetchPlayers($filters, 1, $batchSize);
            $results = [];
            
            if (isset($players['data'])) {
                foreach ($players['data'] as $player) {
                    if (isset($player['fifa_id'])) {
                        $results[$player['fifa_id']] = $this->syncPlayerData($player['fifa_id']);
                    }
                }
            }
            
            return $results;
        } catch (Exception $e) {
            Log::error('FIFA sync players error', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            return $this->handleApiError($e);
        }
    }

    /**
     * Test connectivity (alias for checkConnectivity)
     */
    public function testConnectivity(): array
    {
        return $this->checkConnectivity();
    }

    /**
     * Get FIFA Connect statistics
     */
    public function getStatistics(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/statistics");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'total_players' => 0,
                'total_clubs' => 0,
                'total_federations' => 0,
                'last_sync' => null,
                'status' => 'offline'
            ];
        } catch (Exception $e) {
            Log::error('FIFA get statistics error', [
                'error' => $e->getMessage()
            ]);
            return [
                'total_players' => 0,
                'total_clubs' => 0,
                'total_federations' => 0,
                'last_sync' => null,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Fetch clubs from FIFA Connect
     */
    public function fetchClubs(array $filters = [], int $page = 1, int $limit = 50): array
    {
        try {
            $query = http_build_query(array_merge($filters, ['page' => $page, 'limit' => $limit]));
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/clubs?{$query}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("FIFA API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FIFA fetch clubs error', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            return $this->handleApiError($e);
        }
    }

    /**
     * Fetch associations from FIFA Connect
     */
    public function fetchAssociations(array $filters = [], int $page = 1, int $limit = 50): array
    {
        try {
            $query = http_build_query(array_merge($filters, ['page' => $page, 'limit' => $limit]));
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/federations?{$query}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("FIFA API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FIFA fetch associations error', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            return $this->handleApiError($e);
        }
    }

    /**
     * Sync club players
     */
    public function syncClubPlayers($club): array
    {
        try {
            if (is_object($club) && method_exists($club, 'fifa_id')) {
                $fifaId = $club->fifa_id;
            } elseif (is_string($club)) {
                $fifaId = $club;
            } else {
                throw new Exception('Invalid club parameter');
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$this->baseUrl}/clubs/{$fifaId}/players");

            if ($response->successful()) {
                $data = $response->json();
                return $data;
            }

            throw new Exception("FIFA API error: " . $response->status());
        } catch (Exception $e) {
            Log::error('FIFA sync club players error', [
                'club' => $club,
                'error' => $e->getMessage()
            ]);
            return $this->handleApiError($e);
        }
    }

    /**
     * Check FIFA compliance for a player (alias for validateCompliance)
     */
    public function checkCompliance(string $fifaId): array
    {
        return $this->validateCompliance($fifaId);
    }

    /**
     * Update local player record from FIFA data
     */
    protected function updatePlayerFromFifaData(string $fifaId, array $data): void
    {
        $player = Player::where('fifa_connect_id', $fifaId)->first();
        
        if ($player) {
            $player->update([
                'name' => $data['name'] ?? $player->name,
                'first_name' => $data['first_name'] ?? $player->first_name,
                'last_name' => $data['last_name'] ?? $player->last_name,
                'birth_date' => $data['birth_date'] ?? $player->birth_date,
                'nationality' => $data['nationality'] ?? $player->nationality,
                'position' => $data['position'] ?? $player->position,
                'jersey_number' => $data['jersey_number'] ?? $player->jersey_number,
                'overall_rating' => $data['overall_rating'] ?? $player->overall_rating,
                'potential_rating' => $data['potential_rating'] ?? $player->potential_rating,
                'age' => $data['age'] ?? $player->age,
                'height' => $data['height'] ?? $player->height,
                'weight' => $data['weight'] ?? $player->weight,
                'preferred_foot' => $data['preferred_foot'] ?? $player->preferred_foot,
                'work_rate' => $data['work_rate'] ?? $player->work_rate,
                'fifa_data' => json_encode($data)
            ]);
        }
    }

    /**
     * Update local club record from FIFA data
     */
    protected function updateClubFromFifaData(string $fifaClubId, array $data): void
    {
        $club = Club::where('fifa_connect_id', $fifaClubId)->first();
        
        if ($club) {
            $club->update([
                'name' => $data['name'] ?? $club->name,
                'country' => $data['country'] ?? $club->country,
                'league' => $data['league'] ?? $club->league,
                'fifa_data' => json_encode($data)
            ]);
        }
    }

    /**
     * Update local federation record from FIFA data
     */
    protected function updateFederationFromFifaData(string $fifaFederationId, array $data): void
    {
        $federation = Association::where('fifa_connect_id', $fifaFederationId)->first();
        
        if ($federation) {
            $federation->update([
                'name' => $data['name'] ?? $federation->name,
                'country' => $data['country'] ?? $federation->country,
                'fifa_data' => json_encode($data)
            ]);
        }
    }
} 