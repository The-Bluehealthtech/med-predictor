<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\FifaConnectService;
use App\Models\FifaSyncLog;
use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FifaWebhookController extends Controller
{
    private $fifaService;

    public function __construct(FifaConnectService $fifaService)
    {
        $this->fifaService = $fifaService;
    }

    /**
     * Handle FIFA webhook events
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            // Verify webhook signature (if FIFA provides one)
            if (!$this->verifyWebhookSignature($request)) {
                Log::warning('FIFA webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            $payload = $request->all();
            $eventType = $payload['event_type'] ?? 'unknown';
            $entityType = $payload['entity_type'] ?? 'unknown';
            $entityId = $payload['entity_id'] ?? null;

            Log::info("FIFA webhook received", [
                'event_type' => $eventType,
                'entity_type' => $entityType,
                'entity_id' => $entityId
            ]);

            // Log the webhook
            FifaSyncLog::create([
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'request_payload' => $payload,
                'response_payload' => null,
                'status_code' => 200,
                'error_message' => null,
                'operation_type' => 'webhook',
                'fifa_endpoint' => 'webhook',
                'response_time_ms' => 0
            ]);

            // Process the webhook based on event type
            $result = $this->processWebhookEvent($eventType, $entityType, $entityId, $payload);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
                'result' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('FIFA webhook processing error: ' . $e->getMessage(), [
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Webhook processing failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process webhook events
     */
    private function processWebhookEvent(string $eventType, string $entityType, $entityId, array $payload): array
    {
        switch ($eventType) {
            case 'player.updated':
                return $this->handlePlayerUpdate($entityId, $payload);
            
            case 'player.created':
                return $this->handlePlayerCreate($entityId, $payload);
            
            case 'player.deleted':
                return $this->handlePlayerDelete($entityId, $payload);
            
            case 'club.updated':
                return $this->handleClubUpdate($entityId, $payload);
            
            case 'club.created':
                return $this->handleClubCreate($entityId, $payload);
            
            case 'club.deleted':
                return $this->handleClubDelete($entityId, $payload);
            
            case 'association.updated':
                return $this->handleAssociationUpdate($entityId, $payload);
            
            case 'association.created':
                return $this->handleAssociationCreate($entityId, $payload);
            
            case 'association.deleted':
                return $this->handleAssociationDelete($entityId, $payload);
            
            default:
                Log::warning("Unhandled FIFA webhook event type: {$eventType}");
                return ['status' => 'ignored', 'reason' => 'Unhandled event type'];
        }
    }

    /**
     * Handle player update webhook
     */
    private function handlePlayerUpdate($fifaId, array $payload): array
    {
        return DB::transaction(function () use ($fifaId, $payload) {
            // Find the player by FIFA ID
            $fifaConnectId = FifaConnectId::where('fifa_id', $fifaId)
                ->where('entity_type', 'player')
                ->first();

            if (!$fifaConnectId) {
                Log::warning("Player with FIFA ID {$fifaId} not found in local database");
                return ['status' => 'not_found'];
            }

            $player = Player::find($fifaConnectId->entity_id);
            if (!$player) {
                Log::error("Player with ID {$fifaConnectId->entity_id} not found");
                return ['status' => 'error', 'message' => 'Player not found'];
            }

            // Update player data from FIFA
            $fifaData = $payload['data'] ?? [];
            $updated = $this->updatePlayerFromFifa($player, $fifaData);

            return [
                'status' => 'updated',
                'player_id' => $player->id,
                'changes' => $updated
            ];
        });
    }

    /**
     * Handle player create webhook
     */
    private function handlePlayerCreate($fifaId, array $payload): array
    {
        return DB::transaction(function () use ($fifaId, $payload) {
            // Check if player already exists
            $existing = FifaConnectId::where('fifa_id', $fifaId)
                ->where('entity_type', 'player')
                ->exists();

            if ($existing) {
                Log::info("Player with FIFA ID {$fifaId} already exists, updating instead");
                return $this->handlePlayerUpdate($fifaId, $payload);
            }

            // Create new player from FIFA data
            $fifaData = $payload['data'] ?? [];
            $playerData = $this->fifaService->mapFifaPlayerData($fifaData);
            
            $player = Player::create($playerData);

            // Create FIFA connect ID
            FifaConnectId::create([
                'fifa_id' => $fifaId,
                'entity_type' => 'player',
                'entity_id' => $player->id,
                'synced_at' => now(),
                'status' => 'synced'
            ]);

            return [
                'status' => 'created',
                'player_id' => $player->id
            ];
        });
    }

    /**
     * Handle player delete webhook
     */
    private function handlePlayerDelete($fifaId, array $payload): array
    {
        return DB::transaction(function () use ($fifaId, $payload) {
            $fifaConnectId = FifaConnectId::where('fifa_id', $fifaId)
                ->where('entity_type', 'player')
                ->first();

            if (!$fifaConnectId) {
                Log::warning("Player with FIFA ID {$fifaId} not found for deletion");
                return ['status' => 'not_found'];
            }

            $player = Player::find($fifaConnectId->entity_id);
            if ($player) {
                // Soft delete or mark as inactive
                $player->update(['fifa_sync_status' => 'deleted']);
            }

            // Update FIFA connect ID status
            $fifaConnectId->update([
                'status' => 'deleted',
                'synced_at' => now()
            ]);

            return [
                'status' => 'deleted',
                'player_id' => $fifaConnectId->entity_id
            ];
        });
    }

    /**
     * Handle club update webhook
     */
    private function handleClubUpdate($fifaId, array $payload): array
    {
        return DB::transaction(function () use ($fifaId, $payload) {
            $fifaConnectId = FifaConnectId::where('fifa_id', $fifaId)
                ->where('entity_type', 'club')
                ->first();

            if (!$fifaConnectId) {
                return ['status' => 'not_found'];
            }

            $club = Club::find($fifaConnectId->entity_id);
            if (!$club) {
                return ['status' => 'error', 'message' => 'Club not found'];
            }

            $fifaData = $payload['data'] ?? [];
            $updated = $this->updateClubFromFifa($club, $fifaData);

            return [
                'status' => 'updated',
                'club_id' => $club->id,
                'changes' => $updated
            ];
        });
    }

    /**
     * Handle club create webhook
     */
    private function handleClubCreate($fifaId, array $payload): array
    {
        return DB::transaction(function () use ($fifaId, $payload) {
            $existing = FifaConnectId::where('fifa_id', $fifaId)
                ->where('entity_type', 'club')
                ->exists();

            if ($existing) {
                return $this->handleClubUpdate($fifaId, $payload);
            }

            $fifaData = $payload['data'] ?? [];
            $clubData = $this->mapFifaClubData($fifaData);
            
            $club = Club::create($clubData);

            FifaConnectId::create([
                'fifa_id' => $fifaId,
                'entity_type' => 'club',
                'entity_id' => $club->id,
                'synced_at' => now(),
                'status' => 'synced'
            ]);

            return [
                'status' => 'created',
                'club_id' => $club->id
            ];
        });
    }

    /**
     * Handle club delete webhook
     */
    private function handleClubDelete($fifaId, array $payload): array
    {
        return DB::transaction(function () use ($fifaId, $payload) {
            $fifaConnectId = FifaConnectId::where('fifa_id', $fifaId)
                ->where('entity_type', 'club')
                ->first();

            if (!$fifaConnectId) {
                return ['status' => 'not_found'];
            }

            $club = Club::find($fifaConnectId->entity_id);
            if ($club) {
                $club->update(['fifa_sync_status' => 'deleted']);
            }

            $fifaConnectId->update([
                'status' => 'deleted',
                'synced_at' => now()
            ]);

            return [
                'status' => 'deleted',
                'club_id' => $fifaConnectId->entity_id
            ];
        });
    }

    /**
     * Handle association update webhook
     */
    private function handleAssociationUpdate($fifaId, array $payload): array
    {
        return DB::transaction(function () use ($fifaId, $payload) {
            $fifaConnectId = FifaConnectId::where('fifa_id', $fifaId)
                ->where('entity_type', 'association')
                ->first();

            if (!$fifaConnectId) {
                return ['status' => 'not_found'];
            }

            $association = Association::find($fifaConnectId->entity_id);
            if (!$association) {
                return ['status' => 'error', 'message' => 'Association not found'];
            }

            $fifaData = $payload['data'] ?? [];
            $updated = $this->updateAssociationFromFifa($association, $fifaData);

            return [
                'status' => 'updated',
                'association_id' => $association->id,
                'changes' => $updated
            ];
        });
    }

    /**
     * Handle association create webhook
     */
    private function handleAssociationCreate($fifaId, array $payload): array
    {
        return DB::transaction(function () use ($fifaId, $payload) {
            $existing = FifaConnectId::where('fifa_id', $fifaId)
                ->where('entity_type', 'association')
                ->exists();

            if ($existing) {
                return $this->handleAssociationUpdate($fifaId, $payload);
            }

            $fifaData = $payload['data'] ?? [];
            $associationData = $this->mapFifaAssociationData($fifaData);
            
            $association = Association::create($associationData);

            FifaConnectId::create([
                'fifa_id' => $fifaId,
                'entity_type' => 'association',
                'entity_id' => $association->id,
                'synced_at' => now(),
                'status' => 'synced'
            ]);

            return [
                'status' => 'created',
                'association_id' => $association->id
            ];
        });
    }

    /**
     * Handle association delete webhook
     */
    private function handleAssociationDelete($fifaId, array $payload): array
    {
        return DB::transaction(function () use ($fifaId, $payload) {
            $fifaConnectId = FifaConnectId::where('fifa_id', $fifaId)
                ->where('entity_type', 'association')
                ->first();

            if (!$fifaConnectId) {
                return ['status' => 'not_found'];
            }

            $association = Association::find($fifaConnectId->entity_id);
            if ($association) {
                $association->update(['fifa_sync_status' => 'deleted']);
            }

            $fifaConnectId->update([
                'status' => 'deleted',
                'synced_at' => now()
            ]);

            return [
                'status' => 'deleted',
                'association_id' => $fifaConnectId->entity_id
            ];
        });
    }

    /**
     * Update player data from FIFA
     */
    private function updatePlayerFromFifa(Player $player, array $fifaData): array
    {
        $changes = [];
        $mappedData = $this->fifaService->mapFifaPlayerData($fifaData);

        foreach ($mappedData as $field => $value) {
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
        }

        return $changes;
    }

    /**
     * Update club data from FIFA
     */
    private function updateClubFromFifa(Club $club, array $fifaData): array
    {
        $changes = [];
        $mappedData = $this->mapFifaClubData($fifaData);

        foreach ($mappedData as $field => $value) {
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
        }

        return $changes;
    }

    /**
     * Update association data from FIFA
     */
    private function updateAssociationFromFifa(Association $association, array $fifaData): array
    {
        $changes = [];
        $mappedData = $this->mapFifaAssociationData($fifaData);

        foreach ($mappedData as $field => $value) {
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
        }

        return $changes;
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
     * Verify webhook signature (implement based on FIFA's signature method)
     */
    private function verifyWebhookSignature(Request $request): bool
    {
        // TODO: Implement signature verification based on FIFA's webhook security
        // For now, return true to allow testing
        return true;
    }

    /**
     * Get webhook statistics
     */
    public function getWebhookStats(): JsonResponse
    {
        $stats = FifaSyncLog::where('operation_type', 'webhook')
            ->selectRaw('
                COUNT(*) as total_webhooks,
                COUNT(CASE WHEN status_code = 200 THEN 1 END) as successful_webhooks,
                COUNT(CASE WHEN status_code != 200 THEN 1 END) as failed_webhooks,
                entity_type,
                DATE(created_at) as date
            ')
            ->groupBy('entity_type', 'date')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Get sync statistics
     */
    public function getSyncStats(): JsonResponse
    {
        $syncService = app(\App\Services\FifaSyncService::class);
        $stats = $syncService->getSyncStats();

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Get recent sync logs
     */
    public function getSyncLogs(): JsonResponse
    {
        $logs = FifaSyncLog::orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'logs' => $logs
        ]);
    }

    /**
     * Start synchronization
     */
    public function startSync(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:all,players,clubs,associations',
            'batch_size' => 'integer|min:10|max:100',
            'filters' => 'array',
            'dry_run' => 'boolean'
        ]);

        try {
            $syncService = app(\App\Services\FifaSyncService::class);
            
            if ($request->input('dry_run')) {
                // For dry run, we'll simulate the results
                $results = [
                    'players' => ['total_processed' => 100, 'created' => 70, 'updated' => 20, 'skipped' => 10, 'errors' => 0, 'conflicts' => 0],
                    'clubs' => ['total_processed' => 50, 'created' => 25, 'updated' => 20, 'skipped' => 5, 'errors' => 0, 'conflicts' => 0],
                    'associations' => ['total_processed' => 20, 'created' => 6, 'updated' => 12, 'skipped' => 2, 'errors' => 0, 'conflicts' => 0]
                ];
            } else {
                $options = [
                    'players' => $request->input('filters', []),
                    'clubs' => $request->input('filters', []),
                    'associations' => $request->input('filters', [])
                ];

                if ($request->input('type') !== 'all') {
                    $type = $request->input('type');
                    $results = [];
                    $results[$type] = $syncService->{'sync' . ucfirst($type)}($request->input('filters', []));
                } else {
                    $results = $syncService->fullSync($options);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Synchronization completed successfully',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('FIFA sync failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Synchronization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test FIFA connectivity
     */
    public function testConnectivity(): JsonResponse
    {
        try {
            $fifaService = app(\App\Services\FifaConnectService::class);
            $result = $fifaService->testConnectivity();

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'] ?? 'Connectivity test completed',
                'details' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connectivity test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear FIFA cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $fifaService = app(\App\Services\FifaConnectService::class);
            $fifaService->clearCaches();

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resolve conflict
     */
    public function resolveConflict(Request $request, int $logId): JsonResponse
    {
        $request->validate([
            'resolution' => 'required|in:use_fifa,use_local,manual',
            'data' => 'array'
        ]);

        try {
            $syncService = app(\App\Services\FifaSyncService::class);
            $result = $syncService->resolveConflict($logId, $request->input('resolution'), $request->input('data', []));

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve conflict: ' . $e->getMessage()
            ], 500);
        }
    }
} 