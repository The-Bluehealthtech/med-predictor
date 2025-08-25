# FIFA Data Model and API Connection Enhancements

## 1. Current FIFA Data Model Analysis

### **Existing FIFA Integration**

#### **Database Entities with FIFA Integration**

```sql
-- Players table with FIFA fields
players (
    id, fifa_connect_id, name, first_name, last_name,
    date_of_birth, nationality, position, height, weight,
    overall_rating, potential_rating, value_eur, wage_eur,
    preferred_foot, weak_foot, skill_moves, international_reputation,
    work_rate, body_type, real_face, release_clause_eur,
    player_face_url, club_logo_url, nation_flag_url,
    contract_valid_until, fifa_version, last_updated
)

-- Clubs table with FIFA fields
clubs (
    id, name, fifa_connect_id, fifa_id, association_id,
    logo_url, website, email, phone, address,
    founded_year, stadium_name, capacity, country
)

-- Associations table with FIFA fields
associations (
    id, name, fifa_id, confederation, fifa_ranking,
    association_logo_url, nation_flag_url, fifa_version,
    last_updated, country, region
)

-- Player licenses with FIFA integration
player_licenses (
    id, player_id, club_id, fifa_connect_id, license_number,
    license_type, status, issue_date, expiry_date,
    medical_clearance, fitness_certificate, international_clearance
)

-- Users with FIFA Connect IDs
users (
    id, name, email, role, fifa_connect_id,
    entity_type, club_id, association_id
)
```

#### **Current FIFA Connect Service**

```php
class FifaConnectService {
    private $baseUrl = 'https://api.fifa.com/v1';
    private $apiKey;
    private $timeout = 30;

    // Current capabilities
    public function generateConnectId(User $user): string
    public function syncUser(User $user): bool
    public function syncPlayer(Player $player): bool
    public function getConnectivityStatus(): array
    public function getUserStatus(string $fifaConnectId): ?array
}
```

---

## 2. Enhanced FIFA Data Model

### **A. Expanded FifaConnectId Table**

#### **Enhanced Schema**

```sql
CREATE TABLE fifa_connect_ids (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fifa_id VARCHAR(255) UNIQUE NOT NULL,
    entity_type ENUM('player', 'club', 'association', 'competition', 'user', 'match', 'team') NOT NULL,
    entity_id BIGINT UNSIGNED NOT NULL,
    fifa_entity_type VARCHAR(50) NOT NULL,
    synced_at TIMESTAMP NULL,
    sync_status ENUM('pending', 'synced', 'failed', 'conflict') DEFAULT 'pending',
    last_error TEXT NULL,
    retry_count INT DEFAULT 0,
    max_retries INT DEFAULT 3,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_fifa_id (fifa_id),
    INDEX idx_sync_status (sync_status)
);
```

#### **Enhanced Model**

```php
class FifaConnectId extends Model {
    protected $fillable = [
        'fifa_id', 'entity_type', 'entity_id', 'fifa_entity_type',
        'synced_at', 'sync_status', 'last_error', 'retry_count', 'max_retries'
    ];

    protected $casts = [
        'synced_at' => 'datetime',
        'retry_count' => 'integer',
        'max_retries' => 'integer'
    ];

    // Relationships
    public function entity() {
        return $this->morphTo();
    }

    // Scopes
    public function scopePending($query) {
        return $query->where('sync_status', 'pending');
    }

    public function scopeFailed($query) {
        return $query->where('sync_status', 'failed');
    }

    public function scopeSynced($query) {
        return $query->where('sync_status', 'synced');
    }
}
```

### **B. FIFA Sync Logs Table**

#### **Schema for Audit Trail**

```sql
CREATE TABLE fifa_sync_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fifa_connect_id BIGINT UNSIGNED NOT NULL,
    operation_type ENUM('create', 'update', 'delete', 'sync', 'webhook') NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id BIGINT UNSIGNED NOT NULL,
    request_payload JSON NULL,
    response_data JSON NULL,
    status ENUM('success', 'failed', 'partial') NOT NULL,
    error_message TEXT NULL,
    response_time_ms INT NULL,
    fifa_api_version VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (fifa_connect_id) REFERENCES fifa_connect_ids(id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_operation (operation_type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

#### **Model for Sync Logging**

```php
class FifaSyncLog extends Model {
    protected $fillable = [
        'fifa_connect_id', 'operation_type', 'entity_type', 'entity_id',
        'request_payload', 'response_data', 'status', 'error_message',
        'response_time_ms', 'fifa_api_version'
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_data' => 'array',
        'response_time_ms' => 'integer'
    ];

    public function fifaConnectId() {
        return $this->belongsTo(FifaConnectId::class);
    }
}
```

### **C. Enhanced Player Model**

#### **Additional FIFA Fields**

```php
class Player extends Model {
    protected $fillable = [
        // Existing fields...
        'fifa_connect_id', 'fifa_sync_status', 'fifa_sync_date',
        'fifa_last_error', 'fifa_version', 'fifa_ranking',
        'fifa_points', 'fifa_previous_ranking', 'fifa_ranking_change',
        'fifa_competitions_played', 'fifa_goals_scored', 'fifa_assists',
        'fifa_yellow_cards', 'fifa_red_cards', 'fifa_minutes_played',
        'fifa_matches_played', 'fifa_clean_sheets', 'fifa_saves',
        'fifa_pass_accuracy', 'fifa_shot_accuracy', 'fifa_tackle_success',
        'fifa_aerial_duels_won', 'fifa_duels_won', 'fifa_interceptions',
        'fifa_clearances', 'fifa_blocks', 'fifa_fouls_committed',
        'fifa_fouls_suffered', 'fifa_offsides', 'fifa_penalties_scored',
        'fifa_penalties_missed', 'fifa_free_kicks_scored',
        'fifa_crosses_accuracy', 'fifa_long_balls_accuracy',
        'fifa_through_balls_accuracy', 'fifa_dribbles_completed',
        'fifa_dribbles_attempted', 'fifa_dribble_success_rate'
    ];

    protected $casts = [
        'fifa_sync_date' => 'datetime',
        'fifa_ranking' => 'integer',
        'fifa_points' => 'integer',
        'fifa_previous_ranking' => 'integer',
        'fifa_ranking_change' => 'integer',
        'fifa_competitions_played' => 'integer',
        'fifa_goals_scored' => 'integer',
        'fifa_assists' => 'integer',
        'fifa_yellow_cards' => 'integer',
        'fifa_red_cards' => 'integer',
        'fifa_minutes_played' => 'integer',
        'fifa_matches_played' => 'integer',
        'fifa_clean_sheets' => 'integer',
        'fifa_saves' => 'integer',
        'fifa_pass_accuracy' => 'decimal:2',
        'fifa_shot_accuracy' => 'decimal:2',
        'fifa_tackle_success' => 'decimal:2',
        'fifa_aerial_duels_won' => 'integer',
        'fifa_duels_won' => 'integer',
        'fifa_interceptions' => 'integer',
        'fifa_clearances' => 'integer',
        'fifa_blocks' => 'integer',
        'fifa_fouls_committed' => 'integer',
        'fifa_fouls_suffered' => 'integer',
        'fifa_offsides' => 'integer',
        'fifa_penalties_scored' => 'integer',
        'fifa_penalties_missed' => 'integer',
        'fifa_free_kicks_scored' => 'integer',
        'fifa_crosses_accuracy' => 'decimal:2',
        'fifa_long_balls_accuracy' => 'decimal:2',
        'fifa_through_balls_accuracy' => 'decimal:2',
        'fifa_dribbles_completed' => 'integer',
        'fifa_dribbles_attempted' => 'integer',
        'fifa_dribble_success_rate' => 'decimal:2'
    ];
}
```

---

## 3. Enhanced FIFA API Connection

### **A. Advanced FifaConnectService**

#### **Enhanced Service Class**

```php
class FifaConnectService {
    private $baseUrl;
    private $apiKey;
    private $timeout;
    private $maxRetries;
    private $retryDelay;

    public function __construct() {
        $this->baseUrl = config('services.fifa.base_url', 'https://api.fifa.com/v1');
        $this->apiKey = config('services.fifa.api_key', 'demo-key');
        $this->timeout = config('services.fifa.timeout', 30);
        $this->maxRetries = config('services.fifa.max_retries', 3);
        $this->retryDelay = config('services.fifa.retry_delay', 5);
    }

    // Enhanced sync methods with retry logic
    public function syncPlayerWithRetry(Player $player): bool {
        $attempts = 0;
        $lastError = null;

        while ($attempts < $this->maxRetries) {
            try {
                $result = $this->syncPlayer($player);
                if ($result) {
                    $this->logSyncSuccess($player, $attempts);
                    return true;
                }
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                $attempts++;

                if ($attempts < $this->maxRetries) {
                    sleep($this->retryDelay * $attempts); // Exponential backoff
                }
            }
        }

        $this->logSyncFailure($player, $lastError, $attempts);
        return false;
    }

    // Bulk sync operations
    public function bulkSyncPlayers(array $playerIds, int $batchSize = 50): array {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        $batches = array_chunk($playerIds, $batchSize);

        foreach ($batches as $batch) {
            $batchResults = $this->processBatch($batch, 'player');
            $results['success'] += $batchResults['success'];
            $results['failed'] += $batchResults['failed'];
            $results['errors'] = array_merge($results['errors'], $batchResults['errors']);

            // Rate limiting
            usleep(100000); // 100ms delay between batches
        }

        return $results;
    }

    // Webhook handling
    public function handleWebhook(array $payload): bool {
        try {
            $eventType = $payload['event_type'] ?? null;
            $entityType = $payload['entity_type'] ?? null;
            $entityId = $payload['entity_id'] ?? null;

            switch ($eventType) {
                case 'player_transfer':
                    return $this->handlePlayerTransfer($payload);
                case 'player_suspension':
                    return $this->handlePlayerSuspension($payload);
                case 'match_result':
                    return $this->handleMatchResult($payload);
                case 'competition_update':
                    return $this->handleCompetitionUpdate($payload);
                default:
                    Log::warning('Unknown FIFA webhook event type', $payload);
                    return false;
            }
        } catch (\Exception $e) {
            Log::error('FIFA webhook processing failed', [
                'payload' => $payload,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    // Advanced caching with Redis
    public function getCachedFifaData(string $key, callable $callback, int $ttl = 3600) {
        return Cache::tags(['fifa', 'data'])->remember($key, $ttl, $callback);
    }

    // Rate limiting
    private function checkRateLimit(): bool {
        $key = 'fifa_rate_limit:' . date('Y-m-d-H');
        $current = Cache::get($key, 0);

        if ($current >= config('services.fifa.rate_limit', 1000)) {
            return false;
        }

        Cache::put($key, $current + 1, 3600);
        return true;
    }
}
```

### **B. FIFA API Endpoints**

#### **Enhanced API Routes**

```php
// FIFA Connect API routes
Route::prefix('fifa')->name('fifa.')->middleware(['auth'])->group(function () {
    // Player endpoints
    Route::get('/players', [FifaConnectController::class, 'getPlayers'])->name('players');
    Route::get('/players/{id}', [FifaConnectController::class, 'getPlayer'])->name('player');
    Route::get('/players/{id}/stats', [FifaConnectController::class, 'getPlayerStats'])->name('player.stats');
    Route::post('/players/sync', [FifaConnectController::class, 'syncPlayers'])->name('players.sync');
    Route::post('/players/bulk-sync', [FifaConnectController::class, 'bulkSyncPlayers'])->name('players.bulk-sync');

    // Club endpoints
    Route::get('/clubs', [FifaConnectController::class, 'getClubs'])->name('clubs');
    Route::get('/clubs/{id}', [FifaConnectController::class, 'getClub'])->name('club');
    Route::post('/clubs/sync', [FifaConnectController::class, 'syncClubs'])->name('clubs.sync');

    // Association endpoints
    Route::get('/associations', [FifaConnectController::class, 'getAssociations'])->name('associations');
    Route::get('/associations/{id}', [FifaConnectController::class, 'getAssociation'])->name('association');
    Route::post('/associations/sync', [FifaConnectController::class, 'syncAssociations'])->name('associations.sync');

    // Competition endpoints
    Route::get('/competitions', [FifaConnectController::class, 'getCompetitions'])->name('competitions');
    Route::get('/competitions/{id}', [FifaConnectController::class, 'getCompetition'])->name('competition');
    Route::get('/competitions/{id}/matches', [FifaConnectController::class, 'getCompetitionMatches'])->name('competition.matches');
    Route::post('/competitions/sync', [FifaConnectController::class, 'syncCompetitions'])->name('competitions.sync');

    // Match endpoints
    Route::get('/matches', [FifaConnectController::class, 'getMatches'])->name('matches');
    Route::get('/matches/{id}', [FifaConnectController::class, 'getMatch'])->name('match');
    Route::get('/matches/{id}/events', [FifaConnectController::class, 'getMatchEvents'])->name('match.events');
    Route::post('/matches/sync', [FifaConnectController::class, 'syncMatches'])->name('matches.sync');

    // Search and filtering
    Route::get('/search', [FifaConnectController::class, 'searchPlayers'])->name('search');
    Route::get('/search/advanced', [FifaConnectController::class, 'advancedSearch'])->name('search.advanced');

    // Sync management
    Route::get('/sync/status', [FifaConnectController::class, 'getSyncStatus'])->name('sync.status');
    Route::post('/sync/retry-failed', [FifaConnectController::class, 'retryFailedSyncs'])->name('sync.retry');
    Route::post('/sync/clear-cache', [FifaConnectController::class, 'clearCache'])->name('sync.clear-cache');

    // Webhook endpoint
    Route::post('/webhook', [FifaConnectController::class, 'handleWebhook'])->name('webhook');

    // Health and monitoring
    Route::get('/health', [FifaConnectController::class, 'getHealth'])->name('health');
    Route::get('/stats', [FifaConnectController::class, 'getApiStats'])->name('stats');
    Route::get('/connectivity', [FifaConnectController::class, 'getConnectivityStatus'])->name('connectivity');
});
```

### **C. Enhanced Controller Methods**

#### **Advanced Search and Filtering**

```php
public function advancedSearch(Request $request): JsonResponse {
    $filters = $request->validate([
        'query' => 'nullable|string|max:255',
        'position' => 'nullable|string|in:GK,DF,MF,FW',
        'nationality' => 'nullable|string|max:100',
        'club' => 'nullable|string|max:255',
        'association' => 'nullable|string|max:255',
        'min_rating' => 'nullable|integer|min:1|max:99',
        'max_rating' => 'nullable|integer|min:1|max:99',
        'min_age' => 'nullable|integer|min:16|max:50',
        'max_age' => 'nullable|integer|min:16|max:50',
        'preferred_foot' => 'nullable|string|in:Left,Right,Both',
        'work_rate' => 'nullable|string|max:50',
        'contract_status' => 'nullable|string|in:active,expired,free_agent',
        'license_status' => 'nullable|string|in:active,suspended,expired',
        'sort_by' => 'nullable|string|in:name,rating,age,value,goals,assists',
        'sort_order' => 'nullable|string|in:asc,desc',
        'page' => 'nullable|integer|min:1',
        'limit' => 'nullable|integer|min:1|max:100'
    ]);

    try {
        $cacheKey = 'fifa_advanced_search_' . md5(serialize($filters));

        $results = Cache::tags(['fifa', 'search'])->remember($cacheKey, 1800, function () use ($filters) {
            return $this->fifaService->advancedSearch($filters);
        });

        return response()->json([
            'success' => true,
            'data' => $results['data'],
            'total' => $results['total'],
            'filters' => $filters,
            'pagination' => $results['pagination']
        ]);
    } catch (\Exception $e) {
        Log::error('FIFA advanced search failed', [
            'filters' => $filters,
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Search failed',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

---

## 4. FIFA Data Synchronization

### **A. Comprehensive Sync Jobs**

#### **Scheduled Sync Jobs**

```php
// App\Console\Commands\SyncFifaData.php
class SyncFifaData extends Command {
    protected $signature = 'fifa:sync {entity?} {--force} {--batch-size=50}';
    protected $description = 'Sync data with FIFA Connect API';

    public function handle() {
        $entity = $this->argument('entity');
        $force = $this->option('force');
        $batchSize = $this->option('batch-size');

        $syncService = app(FifaSyncService::class);

        if ($force) {
            Cache::tags(['fifa'])->flush();
        }

        switch ($entity) {
            case 'players':
                $this->syncPlayers($syncService, $batchSize);
                break;
            case 'clubs':
                $this->syncClubs($syncService, $batchSize);
                break;
            case 'associations':
                $this->syncAssociations($syncService, $batchSize);
                break;
            case 'competitions':
                $this->syncCompetitions($syncService, $batchSize);
                break;
            case 'matches':
                $this->syncMatches($syncService, $batchSize);
                break;
            default:
                $this->syncAll($syncService, $batchSize);
        }
    }

    private function syncPlayers(FifaSyncService $service, int $batchSize) {
        $this->info('Starting player sync...');

        $players = Player::where('fifa_sync_status', '!=', 'synced')
            ->orWhereNull('fifa_sync_date')
            ->orWhere('fifa_sync_date', '<', now()->subHours(24))
            ->chunk($batchSize, function ($playerBatch) use ($service) {
                $results = $service->bulkSyncPlayers($playerBatch->pluck('id')->toArray());

                $this->info("Processed batch: {$results['success']} success, {$results['failed']} failed");

                if (!empty($results['errors'])) {
                    $this->error('Errors: ' . implode(', ', $results['errors']));
                }
            });

        $this->info('Player sync completed');
    }
}
```

### **B. Webhook Processing**

#### **Webhook Handler**

```php
public function handleWebhook(Request $request): JsonResponse {
    try {
        $payload = $request->all();
        $signature = $request->header('X-FIFA-Signature');

        // Verify webhook signature
        if (!$this->verifyWebhookSignature($payload, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $eventType = $payload['event_type'] ?? null;
        $entityType = $payload['entity_type'] ?? null;
        $entityId = $payload['entity_id'] ?? null;

        // Log webhook
        FifaSyncLog::create([
            'fifa_connect_id' => $payload['fifa_connect_id'] ?? null,
            'operation_type' => 'webhook',
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'request_payload' => $payload,
            'status' => 'success'
        ]);

        // Process webhook
        $result = $this->fifaService->handleWebhook($payload);

        if ($result) {
            return response()->json(['status' => 'processed']);
        } else {
            return response()->json(['status' => 'failed'], 500);
        }
    } catch (\Exception $e) {
        Log::error('Webhook processing failed', [
            'payload' => $request->all(),
            'error' => $e->getMessage()
        ]);

        return response()->json(['error' => 'Processing failed'], 500);
    }
}
```

---

## 5. FIFA Compliance and Security

### **A. Data Validation**

#### **FIFA Data Validator**

```php
class FifaDataValidator {
    public function validatePlayerData(array $data): array {
        $rules = [
            'fifa_connect_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:100',
            'position' => 'required|string|in:GK,DF,MF,FW',
            'height' => 'nullable|integer|min:100|max:250',
            'weight' => 'nullable|integer|min:30|max:150',
            'overall_rating' => 'nullable|integer|min:1|max:99',
            'potential_rating' => 'nullable|integer|min:1|max:99',
            'value_eur' => 'nullable|numeric|min:0',
            'wage_eur' => 'nullable|numeric|min:0',
            'contract_valid_until' => 'nullable|date|after:today',
            'preferred_foot' => 'nullable|string|in:Left,Right,Both',
            'weak_foot' => 'nullable|integer|min:1|max:5',
            'skill_moves' => 'nullable|integer|min:1|max:5',
            'international_reputation' => 'nullable|integer|min:1|max:5',
            'work_rate' => 'nullable|string|max:50',
            'body_type' => 'nullable|string|max:50',
            'real_face' => 'nullable|boolean',
            'release_clause_eur' => 'nullable|numeric|min:0',
            'player_face_url' => 'nullable|url|max:500',
            'club_logo_url' => 'nullable|url|max:500',
            'nation_flag_url' => 'nullable|url|max:500',
            'fifa_version' => 'nullable|string|max:20'
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->toArray()
            ];
        }

        return ['valid' => true, 'data' => $validator->validated()];
    }
}
```

### **B. Audit Logging**

#### **Comprehensive Audit System**

```php
class FifaAuditService {
    public function logFifaAccess(string $action, array $data, User $user): void {
        FifaSyncLog::create([
            'fifa_connect_id' => $data['fifa_connect_id'] ?? null,
            'operation_type' => $action,
            'entity_type' => $data['entity_type'] ?? null,
            'entity_id' => $data['entity_id'] ?? null,
            'request_payload' => $data,
            'status' => 'success',
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public function logFifaError(string $action, array $data, string $error, User $user = null): void {
        FifaSyncLog::create([
            'fifa_connect_id' => $data['fifa_connect_id'] ?? null,
            'operation_type' => $action,
            'entity_type' => $data['entity_type'] ?? null,
            'entity_id' => $data['entity_id'] ?? null,
            'request_payload' => $data,
            'status' => 'failed',
            'error_message' => $error,
            'user_id' => $user?->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
```

---

## 6. Performance Optimization

### **A. Advanced Caching Strategy**

#### **Redis Caching Implementation**

```php
class FifaCacheService {
    private $redis;
    private $defaultTtl = 3600; // 1 hour

    public function __construct() {
        $this->redis = Redis::connection();
    }

    public function cacheFifaData(string $key, $data, int $ttl = null): void {
        $ttl = $ttl ?? $this->defaultTtl;

        $this->redis->setex(
            "fifa:data:{$key}",
            $ttl,
            json_encode($data)
        );
    }

    public function getCachedFifaData(string $key) {
        $data = $this->redis->get("fifa:data:{$key}");
        return $data ? json_decode($data, true) : null;
    }

    public function invalidateFifaCache(string $pattern = '*'): void {
        $keys = $this->redis->keys("fifa:data:{$pattern}");
        if (!empty($keys)) {
            $this->redis->del($keys);
        }
    }

    public function cachePlayerStats(int $playerId, array $stats): void {
        $this->cacheFifaData("player:stats:{$playerId}", $stats, 1800); // 30 minutes
    }

    public function cacheCompetitionData(int $competitionId, array $data): void {
        $this->cacheFifaData("competition:{$competitionId}", $data, 7200); // 2 hours
    }
}
```

### **B. Database Optimization**

#### **Optimized Queries**

```php
class FifaOptimizedQueries {
    public function getPlayersWithFifaData(int $limit = 50): Collection {
        return Player::with([
            'club:id,name,fifa_id',
            'association:id,name,fifa_id',
            'fifaConnectId:id,fifa_id,sync_status'
        ])
        ->select([
            'id', 'name', 'first_name', 'last_name', 'position',
            'overall_rating', 'fifa_connect_id', 'fifa_sync_status'
        ])
        ->whereNotNull('fifa_connect_id')
        ->orderBy('fifa_sync_date', 'desc')
        ->limit($limit)
        ->get();
    }

    public function getFifaSyncStats(): array {
        return FifaConnectId::selectRaw('
            entity_type,
            sync_status,
            COUNT(*) as count,
            MAX(updated_at) as last_sync
        ')
        ->groupBy('entity_type', 'sync_status')
        ->get()
        ->groupBy('entity_type')
        ->toArray();
    }
}
```

---

## 7. Monitoring and Analytics

### **A. FIFA API Monitoring**

#### **Health Monitoring Service**

```php
class FifaHealthMonitor {
    public function checkApiHealth(): array {
        $startTime = microtime(true);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.fifa.api_key'),
                    'Accept' => 'application/json'
                ])
                ->get(config('services.fifa.base_url') . '/health');

            $responseTime = (microtime(true) - $startTime) * 1000;

            return [
                'status' => $response->successful() ? 'healthy' : 'unhealthy',
                'response_time_ms' => round($responseTime, 2),
                'status_code' => $response->status(),
                'last_check' => now(),
                'details' => $response->json()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'response_time_ms' => null,
                'status_code' => null,
                'last_check' => now(),
                'error' => $e->getMessage()
            ];
        }
    }

    public function getSyncMetrics(): array {
        $last24Hours = now()->subDay();

        return [
            'total_syncs' => FifaSyncLog::where('created_at', '>=', $last24Hours)->count(),
            'successful_syncs' => FifaSyncLog::where('status', 'success')
                ->where('created_at', '>=', $last24Hours)->count(),
            'failed_syncs' => FifaSyncLog::where('status', 'failed')
                ->where('created_at', '>=', $last24Hours)->count(),
            'average_response_time' => FifaSyncLog::where('response_time_ms', '>', 0)
                ->where('created_at', '>=', $last24Hours)
                ->avg('response_time_ms'),
            'pending_syncs' => FifaConnectId::where('sync_status', 'pending')->count(),
            'failed_syncs_pending_retry' => FifaConnectId::where('sync_status', 'failed')
                ->where('retry_count', '<', 'max_retries')->count()
        ];
    }
}
```

### **B. Performance Analytics**

#### **FIFA Performance Dashboard**

```php
class FifaPerformanceAnalytics {
    public function getPerformanceMetrics(): array {
        $lastWeek = now()->subWeek();

        return [
            'api_calls' => $this->getApiCallMetrics($lastWeek),
            'sync_performance' => $this->getSyncPerformanceMetrics($lastWeek),
            'error_rates' => $this->getErrorRateMetrics($lastWeek),
            'cache_hit_rates' => $this->getCacheHitRateMetrics($lastWeek),
            'data_volume' => $this->getDataVolumeMetrics($lastWeek)
        ];
    }

    private function getApiCallMetrics(Carbon $since): array {
        return FifaSyncLog::where('created_at', '>=', $since)
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as total_calls,
                AVG(response_time_ms) as avg_response_time,
                COUNT(CASE WHEN status = "success" THEN 1 END) as successful_calls,
                COUNT(CASE WHEN status = "failed" THEN 1 END) as failed_calls
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }
}
```

---

## 8. Implementation Roadmap

### **Phase 1: Foundation (Week 1-2)**

-   [ ] Enhanced FifaConnectId table migration
-   [ ] FifaSyncLog table creation
-   [ ] Basic enhanced FifaConnectService
-   [ ] Improved error handling and retry logic

### **Phase 2: Advanced Features (Week 3-4)**

-   [ ] Webhook implementation
-   [ ] Bulk sync operations
-   [ ] Advanced caching with Redis
-   [ ] Performance monitoring

### **Phase 3: Compliance & Security (Week 5-6)**

-   [ ] FIFA data validation
-   [ ] Comprehensive audit logging
-   [ ] Security enhancements
-   [ ] Rate limiting implementation

### **Phase 4: Analytics & Optimization (Week 7-8)**

-   [ ] Performance analytics dashboard
-   [ ] Advanced query optimization
-   [ ] Monitoring and alerting
-   [ ] Documentation and testing

---

This comprehensive FIFA data model and API enhancement plan provides a robust, scalable, and FIFA-compliant integration system that will significantly improve the Med Predictor platform's capabilities and reliability.
