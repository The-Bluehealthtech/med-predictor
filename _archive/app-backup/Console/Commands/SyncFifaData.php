<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FifaSyncService;
use App\Services\FifaConnectService;
use Illuminate\Support\Facades\Log;

class SyncFifaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fifa:sync 
                            {--type=all : Type of data to sync (players, clubs, associations, all)}
                            {--filters= : JSON filters to apply}
                            {--batch-size=50 : Number of records per batch}
                            {--dry-run : Show what would be synced without making changes}
                            {--force : Force sync even if recent sync exists}
                            {--resolve-conflicts : Automatically resolve conflicts using FIFA data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize data with FIFA API';

    private $syncService;
    private $fifaService;

    public function __construct(FifaSyncService $syncService, FifaConnectService $fifaService)
    {
        parent::__construct();
        $this->syncService = $syncService;
        $this->fifaService = $fifaService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting FIFA data synchronization...');

        // Test connectivity first
        if (!$this->testConnectivity()) {
            $this->error('FIFA API connectivity test failed. Please check your configuration.');
            return 1;
        }

        $type = $this->option('type');
        $filters = $this->parseFilters();
        $batchSize = (int) $this->option('batch-size');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Check if recent sync exists (unless forced)
        if (!$force && !$this->shouldProceedWithSync()) {
            $this->warn('Recent sync detected. Use --force to override.');
            return 0;
        }

        $startTime = microtime(true);

        try {
            switch ($type) {
                case 'players':
                    $results = $this->syncPlayers($filters, $batchSize, $dryRun);
                    break;
                case 'clubs':
                    $results = $this->syncClubs($filters, $batchSize, $dryRun);
                    break;
                case 'associations':
                    $results = $this->syncAssociations($filters, $batchSize, $dryRun);
                    break;
                case 'all':
                    $results = $this->syncAll($filters, $batchSize, $dryRun);
                    break;
                default:
                    $this->error("Invalid sync type: {$type}");
                    return 1;
            }

            $duration = microtime(true) - $startTime;
            $this->displayResults($results, $duration, $dryRun);

            // Log the sync operation
            Log::info('FIFA sync completed', [
                'type' => $type,
                'filters' => $filters,
                'results' => $results,
                'duration' => $duration,
                'dry_run' => $dryRun
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            Log::error('FIFA sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Test FIFA API connectivity
     */
    private function testConnectivity(): bool
    {
        $this->info('Testing FIFA API connectivity...');
        
        try {
            $result = $this->fifaService->testConnectivity();
            
            if ($result['success']) {
                $this->info('✓ FIFA API connectivity test passed');
                return true;
            } else {
                $this->error('✗ FIFA API connectivity test failed: ' . ($result['error'] ?? 'Unknown error'));
                return false;
            }
        } catch (\Exception $e) {
            $this->error('✗ FIFA API connectivity test failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if we should proceed with sync
     */
    private function shouldProceedWithSync(): bool
    {
        $lastSync = \Cache::get('fifa_last_sync');
        
        if (!$lastSync) {
            return true;
        }

        $lastSyncTime = $lastSync['timestamp'];
        $hoursSinceLastSync = now()->diffInHours($lastSyncTime);

        if ($hoursSinceLastSync < 1) {
            $this->warn("Last sync was {$hoursSinceLastSync} hours ago");
            return false;
        }

        return true;
    }

    /**
     * Parse filters from command option
     */
    private function parseFilters(): array
    {
        $filtersJson = $this->option('filters');
        
        if (!$filtersJson) {
            return [];
        }

        try {
            $filters = json_decode($filtersJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON format for filters');
                return [];
            }

            return $filters;
        } catch (\Exception $e) {
            $this->error('Error parsing filters: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Sync players
     */
    private function syncPlayers(array $filters, int $batchSize, bool $dryRun): array
    {
        $this->info('Synchronizing players...');
        
        if ($dryRun) {
            return $this->dryRunPlayers($filters, $batchSize);
        }

        return $this->syncService->syncPlayers($filters);
    }

    /**
     * Sync clubs
     */
    private function syncClubs(array $filters, int $batchSize, bool $dryRun): array
    {
        $this->info('Synchronizing clubs...');
        
        if ($dryRun) {
            return $this->dryRunClubs($filters, $batchSize);
        }

        return $this->syncService->syncClubs($filters);
    }

    /**
     * Sync associations
     */
    private function syncAssociations(array $filters, int $batchSize, bool $dryRun): array
    {
        $this->info('Synchronizing associations...');
        
        if ($dryRun) {
            return $this->dryRunAssociations($filters, $batchSize);
        }

        return $this->syncService->syncAssociations($filters);
    }

    /**
     * Sync all entity types
     */
    private function syncAll(array $filters, int $batchSize, bool $dryRun): array
    {
        $this->info('Synchronizing all data types...');
        
        if ($dryRun) {
            return [
                'players' => $this->dryRunPlayers($filters, $batchSize),
                'clubs' => $this->dryRunClubs($filters, $batchSize),
                'associations' => $this->dryRunAssociations($filters, $batchSize)
            ];
        }

        return $this->syncService->fullSync([
            'players' => $filters,
            'clubs' => $filters,
            'associations' => $filters
        ]);
    }

    /**
     * Dry run for players
     */
    private function dryRunPlayers(array $filters, int $batchSize): array
    {
        $this->line('Simulating player sync...');
        
        $fifaPlayers = $this->fifaService->fetchPlayers($filters, 1, $batchSize);
        $totalPlayers = count($fifaPlayers['data'] ?? []);
        
        return [
            'total_processed' => $totalPlayers,
            'created' => $totalPlayers * 0.7, // Estimate
            'updated' => $totalPlayers * 0.2, // Estimate
            'skipped' => $totalPlayers * 0.1, // Estimate
            'errors' => 0,
            'conflicts' => 0,
            'dry_run' => true
        ];
    }

    /**
     * Dry run for clubs
     */
    private function dryRunClubs(array $filters, int $batchSize): array
    {
        $this->line('Simulating club sync...');
        
        $fifaClubs = $this->fifaService->fetchClubs($filters, 1, $batchSize);
        $totalClubs = count($fifaClubs['data'] ?? []);
        
        return [
            'total_processed' => $totalClubs,
            'created' => $totalClubs * 0.5, // Estimate
            'updated' => $totalClubs * 0.4, // Estimate
            'skipped' => $totalClubs * 0.1, // Estimate
            'errors' => 0,
            'conflicts' => 0,
            'dry_run' => true
        ];
    }

    /**
     * Dry run for associations
     */
    private function dryRunAssociations(array $filters, int $batchSize): array
    {
        $this->line('Simulating association sync...');
        
        $fifaAssociations = $this->fifaService->fetchAssociations($filters, 1, $batchSize);
        $totalAssociations = count($fifaAssociations['data'] ?? []);
        
        return [
            'total_processed' => $totalAssociations,
            'created' => $totalAssociations * 0.3, // Estimate
            'updated' => $totalAssociations * 0.6, // Estimate
            'skipped' => $totalAssociations * 0.1, // Estimate
            'errors' => 0,
            'conflicts' => 0,
            'dry_run' => true
        ];
    }

    /**
     * Display sync results
     */
    private function displayResults(array $results, float $duration, bool $dryRun): void
    {
        $this->newLine();
        $this->info('Sync Results:');
        $this->newLine();

        if (isset($results['players'])) {
            // Multi-entity sync results
            $this->displayEntityResults('Players', $results['players'], $dryRun);
            $this->displayEntityResults('Clubs', $results['clubs'], $dryRun);
            $this->displayEntityResults('Associations', $results['associations'], $dryRun);
            
            if (isset($results['total_time'])) {
                $this->line("Total sync time: " . number_format($results['total_time'], 2) . "s");
            }
        } else {
            // Single entity sync results
            $this->displayEntityResults('Data', $results, $dryRun);
        }

        $this->line("Command execution time: " . number_format($duration, 2) . "s");
        
        if ($dryRun) {
            $this->warn('This was a dry run - no actual changes were made');
        }
    }

    /**
     * Display results for a specific entity type
     */
    private function displayEntityResults(string $entityType, array $stats, bool $dryRun): void
    {
        $prefix = $dryRun ? '[DRY RUN] ' : '';
        
        $this->line("{$prefix}{$entityType}:");
        $this->line("  • Total processed: {$stats['total_processed']}");
        $this->line("  • Created: {$stats['created']}");
        $this->line("  • Updated: {$stats['updated']}");
        $this->line("  • Skipped: {$stats['skipped']}");
        $this->line("  • Errors: {$stats['errors']}");
        $this->line("  • Conflicts: {$stats['conflicts']}");
        $this->newLine();
    }
} 