<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use App\Models\FifaConnectId;
use App\Models\FifaSyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class FifaSyncService
{
    private $fifaService;
    private $batchSize = 50;
    private $maxRetries = 3;
    private $syncDelay = 1; // seconds between batches

    public function __construct(FifaConnectService $fifaService)
    {
        $this->fifaService = $fifaService;
    }

    /**
     * Full synchronization of all entities
     */
    public function fullSync(array $options = []): array
    {
        $startTime = microtime(true);
        $results = [
            'players' => $this->syncPlayers($options['players'] ?? []),
            'clubs' => $this->syncClubs($options['clubs'] ?? []),
            'associations' => $this->syncAssociations($options['associations'] ?? []),
            'total_time' => 0,
            'success' => true
        ];

        $results['total_time'] = microtime(true) - $startTime;
        
        // Cache sync results
        Cache::put('fifa_last_sync', [
            'timestamp' => now(),
            'results' => $results
        ], now()->addHours(24));

        return $results;
    }

    /**
     * Synchronize players with conflict resolution
     */
    public function syncPlayers(array $filters = []): array
    {
        $stats = [
            'total_processed' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'conflicts' => 0
        ];

        $page = 1;
        $hasMore = true;

        while ($hasMore) {
            try {
                $fifaPlayers = $this->fifaService->fetchPlayers($filters, $page, $this->batchSize);
                
                if (empty($fifaPlayers['data'])) {
                    $hasMore = false;
                    continue;
                }

                foreach ($fifaPlayers['data'] as $fifaPlayer) {
                    $stats['total_processed']++;
                    
                    $result = $this->syncPlayer($fifaPlayer);
                    
                    switch ($result['status']) {
                        case 'created':
                            $stats['created']++;
                            break;
                        case 'updated':
                            $stats['updated']++;
                            break;
                        case 'skipped':
                            $stats['skipped']++;
                            break;
                        case 'conflict':
                            $stats['conflicts']++;
                            break;
                        case 'error':
                            $stats['errors']++;
                            break;
                    }
                }

                $page++;
                
                // Rate limiting
                if ($hasMore) {
                    sleep($this->syncDelay);
                }

            } catch (\Exception $e) {
                Log::error('FIFA player sync error: ' . $e->getMessage());
                $stats['errors']++;
                break;
            }
        }

        return $stats;
    }

    /**
     * Synchronize a single player with conflict resolution
     */
    private function syncPlayer(array $fifaPlayer): array
    {
        return DB::transaction(function () use ($fifaPlayer) {
            $fifaId = $fifaPlayer['id'] ?? null;
            
            if (!$fifaId) {
                return ['status' => 'error', 'message' => 'No FIFA ID provided'];
            }

            // Check if player already exists
            $fifaConnectId = FifaConnectId::where('fifa_id', $fifaId)
                ->where('entity_type', 'player')
                ->first();

            if ($fifaConnectId) {
                $player = Player::find($fifaConnectId->entity_id);
                
                if (!$player) {
                    // Orphaned FIFA connect ID, clean it up
                    $fifaConnectId->delete();
                    return $this->createPlayer($fifaPlayer);
                }

                // Check for conflicts
                $conflicts = $this->detectPlayerConflicts($player, $fifaPlayer);
                
                if (!empty($conflicts)) {
                    // Log conflict for manual resolution
                    $this->logConflict('player', $player->id, $fifaId, $conflicts);
                    return ['status' => 'conflict', 'conflicts' => $conflicts];
                }

                // Update player
                $updated = $this->updatePlayer($player, $fifaPlayer);
                
                return [
                    'status' => $updated ? 'updated' : 'skipped',
                    'player_id' => $player->id,
                    'changes' => $updated
                ];
            }

            return $this->createPlayer($fifaPlayer);
        });
    }

    /**
     * Create a new player from FIFA data
     */
    private function createPlayer(array $fifaPlayer): array
    {
        try {
            $playerData = $this->fifaService->mapFifaPlayerData($fifaPlayer);
            
            // Validate required fields
            if (empty($playerData['first_name']) || empty($playerData['last_name'])) {
                return ['status' => 'error', 'message' => 'Missing required player data'];
            }

            $player = Player::create($playerData);

            // Create FIFA connect ID
            FifaConnectId::create([
                'fifa_id' => $fifaPlayer['id'],
                'entity_type' => 'player',
                'entity_id' => $player->id,
                'synced_at' => now(),
                'status' => 'synced'
            ]);

            return [
                'status' => 'created',
                'player_id' => $player->id
            ];

        } catch (\Exception $e) {
            Log::error('Error creating player from FIFA data: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Update existing player with FIFA data
     */
    private function updatePlayer(Player $player, array $fifaPlayer): array
    {
        $changes = [];
        $fifaData = $this->fifaService->mapFifaPlayerData($fifaPlayer);

        foreach ($fifaData as $field => $value) {
            if ($player->$field !== $value) {
                $changes[$field] = [
                    'old' => $player->$field,
                    'new' => $value
                ];
                $player->$field = $value;
            }
        }

        if (!empty($changes)) {
            $player->fifa_sync_date = now();
            $player->fifa_sync_status = 'synced';
            $player->save();

            // Update FIFA connect ID
            FifaConnectId::where('fifa_id', $fifaPlayer['id'])
                ->where('entity_type', 'player')
                ->update(['synced_at' => now()]);
        }

        return $changes;
    }

    /**
     * Detect conflicts between local and FIFA data
     */
    private function detectPlayerConflicts(Player $player, array $fifaPlayer): array
    {
        $conflicts = [];
        $fifaData = $this->fifaService->mapFifaPlayerData($fifaPlayer);

        // Check for critical conflicts
        $criticalFields = ['first_name', 'last_name', 'date_of_birth'];
        
        foreach ($criticalFields as $field) {
            if (isset($fifaData[$field]) && $player->$field !== $fifaData[$field]) {
                $conflicts[] = [
                    'field' => $field,
                    'local_value' => $player->$field,
                    'fifa_value' => $fifaData[$field],
                    'severity' => 'critical'
                ];
            }
        }

        // Check for moderate conflicts
        $moderateFields = ['nationality', 'position'];
        
        foreach ($moderateFields as $field) {
            if (isset($fifaData[$field]) && $player->$field !== $fifaData[$field]) {
                $conflicts[] = [
                    'field' => $field,
                    'local_value' => $player->$field,
                    'fifa_value' => $fifaData[$field],
                    'severity' => 'moderate'
                ];
            }
        }

        return $conflicts;
    }

    /**
     * Synchronize clubs
     */
    public function syncClubs(array $filters = []): array
    {
        $stats = [
            'total_processed' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'conflicts' => 0
        ];

        $page = 1;
        $hasMore = true;

        while ($hasMore) {
            try {
                $fifaClubs = $this->fifaService->fetchClubs($filters, $page, $this->batchSize);
                
                if (empty($fifaClubs['data'])) {
                    $hasMore = false;
                    continue;
                }

                foreach ($fifaClubs['data'] as $fifaClub) {
                    $stats['total_processed']++;
                    
                    $result = $this->syncClub($fifaClub);
                    
                    switch ($result['status']) {
                        case 'created':
                            $stats['created']++;
                            break;
                        case 'updated':
                            $stats['updated']++;
                            break;
                        case 'skipped':
                            $stats['skipped']++;
                            break;
                        case 'conflict':
                            $stats['conflicts']++;
                            break;
                        case 'error':
                            $stats['errors']++;
                            break;
                    }
                }

                $page++;
                
                if ($hasMore) {
                    sleep($this->syncDelay);
                }

            } catch (\Exception $e) {
                Log::error('FIFA club sync error: ' . $e->getMessage());
                $stats['errors']++;
                break;
            }
        }

        return $stats;
    }

    /**
     * Synchronize a single club
     */
    private function syncClub(array $fifaClub): array
    {
        return DB::transaction(function () use ($fifaClub) {
            $fifaId = $fifaClub['id'] ?? null;
            
            if (!$fifaId) {
                return ['status' => 'error', 'message' => 'No FIFA ID provided'];
            }

            $fifaConnectId = FifaConnectId::where('fifa_id', $fifaId)
                ->where('entity_type', 'club')
                ->first();

            if ($fifaConnectId) {
                $club = Club::find($fifaConnectId->entity_id);
                
                if (!$club) {
                    $fifaConnectId->delete();
                    return $this->createClub($fifaClub);
                }

                $conflicts = $this->detectClubConflicts($club, $fifaClub);
                
                if (!empty($conflicts)) {
                    $this->logConflict('club', $club->id, $fifaId, $conflicts);
                    return ['status' => 'conflict', 'conflicts' => $conflicts];
                }

                $updated = $this->updateClub($club, $fifaClub);
                
                return [
                    'status' => $updated ? 'updated' : 'skipped',
                    'club_id' => $club->id,
                    'changes' => $updated
                ];
            }

            return $this->createClub($fifaClub);
        });
    }

    /**
     * Create a new club from FIFA data
     */
    private function createClub(array $fifaClub): array
    {
        try {
            $clubData = $this->mapFifaClubData($fifaClub);
            
            if (empty($clubData['name'])) {
                return ['status' => 'error', 'message' => 'Missing required club data'];
            }

            $club = Club::create($clubData);

            FifaConnectId::create([
                'fifa_id' => $fifaClub['id'],
                'entity_type' => 'club',
                'entity_id' => $club->id,
                'synced_at' => now(),
                'status' => 'synced'
            ]);

            return [
                'status' => 'created',
                'club_id' => $club->id
            ];

        } catch (\Exception $e) {
            Log::error('Error creating club from FIFA data: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Update existing club with FIFA data
     */
    private function updateClub(Club $club, array $fifaClub): array
    {
        $changes = [];
        $fifaData = $this->mapFifaClubData($fifaClub);

        foreach ($fifaData as $field => $value) {
            if ($club->$field !== $value) {
                $changes[$field] = [
                    'old' => $club->$field,
                    'new' => $value
                ];
                $club->$field = $value;
            }
        }

        if (!empty($changes)) {
            $club->fifa_sync_date = now();
            $club->fifa_sync_status = 'synced';
            $club->save();

            FifaConnectId::where('fifa_id', $fifaClub['id'])
                ->where('entity_type', 'club')
                ->update(['synced_at' => now()]);
        }

        return $changes;
    }

    /**
     * Detect club conflicts
     */
    private function detectClubConflicts(Club $club, array $fifaClub): array
    {
        $conflicts = [];
        $fifaData = $this->mapFifaClubData($fifaClub);

        $criticalFields = ['name', 'country'];
        
        foreach ($criticalFields as $field) {
            if (isset($fifaData[$field]) && $club->$field !== $fifaData[$field]) {
                $conflicts[] = [
                    'field' => $field,
                    'local_value' => $club->$field,
                    'fifa_value' => $fifaData[$field],
                    'severity' => 'critical'
                ];
            }
        }

        return $conflicts;
    }

    /**
     * Synchronize associations
     */
    public function syncAssociations(array $filters = []): array
    {
        $stats = [
            'total_processed' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'conflicts' => 0
        ];

        $page = 1;
        $hasMore = true;

        while ($hasMore) {
            try {
                $fifaAssociations = $this->fifaService->fetchAssociations($filters, $page, $this->batchSize);
                
                if (empty($fifaAssociations['data'])) {
                    $hasMore = false;
                    continue;
                }

                foreach ($fifaAssociations['data'] as $fifaAssociation) {
                    $stats['total_processed']++;
                    
                    $result = $this->syncAssociation($fifaAssociation);
                    
                    switch ($result['status']) {
                        case 'created':
                            $stats['created']++;
                            break;
                        case 'updated':
                            $stats['updated']++;
                            break;
                        case 'skipped':
                            $stats['skipped']++;
                            break;
                        case 'conflict':
                            $stats['conflicts']++;
                            break;
                        case 'error':
                            $stats['errors']++;
                            break;
                    }
                }

                $page++;
                
                if ($hasMore) {
                    sleep($this->syncDelay);
                }

            } catch (\Exception $e) {
                Log::error('FIFA association sync error: ' . $e->getMessage());
                $stats['errors']++;
                break;
            }
        }

        return $stats;
    }

    /**
     * Synchronize a single association
     */
    private function syncAssociation(array $fifaAssociation): array
    {
        return DB::transaction(function () use ($fifaAssociation) {
            $fifaId = $fifaAssociation['id'] ?? null;
            
            if (!$fifaId) {
                return ['status' => 'error', 'message' => 'No FIFA ID provided'];
            }

            $fifaConnectId = FifaConnectId::where('fifa_id', $fifaId)
                ->where('entity_type', 'association')
                ->first();

            if ($fifaConnectId) {
                $association = Association::find($fifaConnectId->entity_id);
                
                if (!$association) {
                    $fifaConnectId->delete();
                    return $this->createAssociation($fifaAssociation);
                }

                $conflicts = $this->detectAssociationConflicts($association, $fifaAssociation);
                
                if (!empty($conflicts)) {
                    $this->logConflict('association', $association->id, $fifaId, $conflicts);
                    return ['status' => 'conflict', 'conflicts' => $conflicts];
                }

                $updated = $this->updateAssociation($association, $fifaAssociation);
                
                return [
                    'status' => $updated ? 'updated' : 'skipped',
                    'association_id' => $association->id,
                    'changes' => $updated
                ];
            }

            return $this->createAssociation($fifaAssociation);
        });
    }

    /**
     * Create a new association from FIFA data
     */
    private function createAssociation(array $fifaAssociation): array
    {
        try {
            $associationData = $this->mapFifaAssociationData($fifaAssociation);
            
            if (empty($associationData['name'])) {
                return ['status' => 'error', 'message' => 'Missing required association data'];
            }

            $association = Association::create($associationData);

            FifaConnectId::create([
                'fifa_id' => $fifaAssociation['id'],
                'entity_type' => 'association',
                'entity_id' => $association->id,
                'synced_at' => now(),
                'status' => 'synced'
            ]);

            return [
                'status' => 'created',
                'association_id' => $association->id
            ];

        } catch (\Exception $e) {
            Log::error('Error creating association from FIFA data: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Update existing association with FIFA data
     */
    private function updateAssociation(Association $association, array $fifaAssociation): array
    {
        $changes = [];
        $fifaData = $this->mapFifaAssociationData($fifaAssociation);

        foreach ($fifaData as $field => $value) {
            if ($association->$field !== $value) {
                $changes[$field] = [
                    'old' => $association->$field,
                    'new' => $value
                ];
                $association->$field = $value;
            }
        }

        if (!empty($changes)) {
            $association->fifa_sync_date = now();
            $association->fifa_sync_status = 'synced';
            $association->save();

            FifaConnectId::where('fifa_id', $fifaAssociation['id'])
                ->where('entity_type', 'association')
                ->update(['synced_at' => now()]);
        }

        return $changes;
    }

    /**
     * Detect association conflicts
     */
    private function detectAssociationConflicts(Association $association, array $fifaAssociation): array
    {
        $conflicts = [];
        $fifaData = $this->mapFifaAssociationData($fifaAssociation);

        $criticalFields = ['name', 'country'];
        
        foreach ($criticalFields as $field) {
            if (isset($fifaData[$field]) && $association->$field !== $fifaData[$field]) {
                $conflicts[] = [
                    'field' => $field,
                    'local_value' => $association->$field,
                    'fifa_value' => $fifaData[$field],
                    'severity' => 'critical'
                ];
            }
        }

        return $conflicts;
    }

    /**
     * Map FIFA club data to local format
     */
    private function mapFifaClubData(array $fifaData): array
    {
        return [
            'name' => $fifaData['name'] ?? '',
            'short_name' => $fifaData['short_name'] ?? '',
            'country' => $fifaData['country'] ?? '',
            'city' => $fifaData['city'] ?? '',
            'founded_year' => $fifaData['founded_year'] ?? null,
            'logo_url' => $fifaData['logo_url'] ?? null,
            'website' => $fifaData['website'] ?? null,
            'fifa_sync_status' => 'synced',
            'fifa_sync_date' => now(),
        ];
    }

    /**
     * Map FIFA association data to local format
     */
    private function mapFifaAssociationData(array $fifaData): array
    {
        return [
            'name' => $fifaData['name'] ?? '',
            'short_name' => $fifaData['short_name'] ?? '',
            'country' => $fifaData['country'] ?? '',
            'founded_year' => $fifaData['founded_year'] ?? null,
            'logo_url' => $fifaData['logo_url'] ?? null,
            'website' => $fifaData['website'] ?? null,
            'fifa_sync_status' => 'synced',
            'fifa_sync_date' => now(),
        ];
    }

    /**
     * Log conflicts for manual resolution
     */
    private function logConflict(string $entityType, int $entityId, string $fifaId, array $conflicts): void
    {
        FifaSyncLog::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'request_payload' => ['fifa_id' => $fifaId],
            'response_payload' => ['conflicts' => $conflicts],
            'status_code' => 409, // Conflict
            'error_message' => 'Data conflicts detected',
            'operation_type' => 'conflict_detection',
            'fifa_endpoint' => 'sync',
            'response_time_ms' => 0
        ]);
    }

    /**
     * Get sync statistics
     */
    public function getSyncStats(): array
    {
        $lastSync = Cache::get('fifa_last_sync');
        
        $stats = [
            'last_sync' => $lastSync ? $lastSync['timestamp'] : null,
            'total_entities' => [
                'players' => Player::count(),
                'clubs' => Club::count(),
                'associations' => Association::count(),
            ],
            'synced_entities' => [
                'players' => Player::where('fifa_sync_status', 'synced')->count(),
                'clubs' => Club::where('fifa_sync_status', 'synced')->count(),
                'associations' => Association::where('fifa_sync_status', 'synced')->count(),
            ],
            'pending_conflicts' => FifaSyncLog::where('status_code', 409)
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            'recent_errors' => FifaSyncLog::where('error_message', '!=', null)
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];

        if ($lastSync) {
            $stats['last_sync_results'] = $lastSync['results'];
        }

        return $stats;
    }

    /**
     * Resolve conflicts manually
     */
    public function resolveConflict(int $syncLogId, string $resolution, array $data = []): array
    {
        $syncLog = FifaSyncLog::find($syncLogId);
        
        if (!$syncLog || $syncLog->status_code !== 409) {
            return ['success' => false, 'message' => 'Conflict not found'];
        }

        try {
            DB::transaction(function () use ($syncLog, $resolution, $data) {
                $entity = $this->getEntity($syncLog->entity_type, $syncLog->entity_id);
                
                if (!$entity) {
                    throw new \Exception('Entity not found');
                }

                switch ($resolution) {
                    case 'use_fifa':
                        $this->applyFifaData($entity, $data);
                        break;
                    case 'use_local':
                        // Keep local data, just mark as resolved
                        break;
                    case 'manual':
                        $this->applyManualData($entity, $data);
                        break;
                    default:
                        throw new \Exception('Invalid resolution type');
                }

                // Update sync status
                $entity->fifa_sync_status = 'synced';
                $entity->fifa_sync_date = now();
                $entity->save();

                // Mark conflict as resolved
                $syncLog->update([
                    'status_code' => 200,
                    'error_message' => "Resolved with: {$resolution}",
                    'response_payload' => array_merge($syncLog->response_payload ?? [], [
                        'resolution' => $resolution,
                        'resolved_at' => now()
                    ])
                ]);
            });

            return ['success' => true, 'message' => 'Conflict resolved successfully'];

        } catch (\Exception $e) {
            Log::error('Error resolving conflict: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get entity by type and ID
     */
    private function getEntity(string $entityType, int $entityId)
    {
        switch ($entityType) {
            case 'player':
                return Player::find($entityId);
            case 'club':
                return Club::find($entityId);
            case 'association':
                return Association::find($entityId);
            default:
                return null;
        }
    }

    /**
     * Apply FIFA data to entity
     */
    private function applyFifaData($entity, array $data): void
    {
        foreach ($data as $field => $value) {
            if (property_exists($entity, $field)) {
                $entity->$field = $value;
            }
        }
    }

    /**
     * Apply manual data to entity
     */
    private function applyManualData($entity, array $data): void
    {
        $this->applyFifaData($entity, $data);
    }
} 