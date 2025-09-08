<?php

namespace App\Console\Commands;

use App\Services\FifaConnectService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FifaConnectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fifa:connect 
                            {action : The action to perform (test, sync, clear-cache, stats, sync-player)}
                            {--fifa-id= : FIFA ID for specific player sync}
                            {--filters=* : Filters for sync operation}
                            {--batch-size=50 : Batch size for sync operations}
                            {--force : Force sync and clear cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage FIFA Connect operations';

    protected $fifaConnectService;

    public function __construct(FifaConnectService $fifaConnectService)
    {
        parent::__construct();
        $this->fifaConnectService = $fifaConnectService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'test':
                $this->testConnectivity();
                break;
            case 'sync':
                $this->syncPlayers();
                break;
            case 'sync-player':
                $this->syncPlayer();
                break;
            case 'clear-cache':
                $this->clearCache();
                break;
            case 'stats':
                $this->showStatistics();
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->showHelp();
                return 1;
        }

        return 0;
    }

    /**
     * Test FIFA Connect connectivity
     */
    private function testConnectivity()
    {
        $this->info('Testing FIFA Connect connectivity...');
        
        $status = $this->fifaConnectService->testConnectivity();
        
        if ($status['success']) {
            $this->info('✅ FIFA Connect is connected!');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Status', $status['status']],
                    ['Response Time', $status['response_time'] . 'ms'],
                    ['API Version', $status['api_version'] ?? 'N/A'],
                    ['Last Checked', $status['last_checked']],
                ]
            );
        } else {
            $this->error('❌ FIFA Connect is not connected!');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Status', $status['status']],
                    ['Error', $status['error']],
                    ['Last Checked', $status['last_checked']],
                ]
            );
        }
    }

    /**
     * Sync players from FIFA Connect
     */
    private function syncPlayers()
    {
        $this->info('Starting FIFA Connect player sync...');
        
        $filters = $this->option('filters');
        $batchSize = (int) $this->option('batch-size');
        $force = $this->option('force');

        if ($force) {
            $this->warn('Force flag detected - clearing caches...');
            $this->fifaConnectService->clearCaches();
        }

        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        try {
            $result = $this->fifaConnectService->syncPlayers($filters, $batchSize);
            
            $progressBar->finish();
            $this->newLine(2);

            if (empty($result['errors'])) {
                $this->info('✅ Sync completed successfully!');
            } else {
                $this->warn('⚠️  Sync completed with some errors');
            }

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Processed', $result['total_processed']],
                    ['New Players', $result['new_players']],
                    ['Updated Players', $result['updated_players']],
                    ['Errors', count($result['errors'])],
                    ['Started At', $result['started_at']->format('Y-m-d H:i:s')],
                    ['Completed At', $result['completed_at']->format('Y-m-d H:i:s')],
                ]
            );

            if (!empty($result['errors'])) {
                $this->warn('Errors encountered:');
                foreach (array_slice($result['errors'], 0, 10) as $error) {
                    $this->line("  - {$error}");
                }
                if (count($result['errors']) > 10) {
                    $this->line("  ... and " . (count($result['errors']) - 10) . " more errors");
                }
            }

        } catch (\Exception $e) {
            $progressBar->finish();
            $this->newLine(2);
            $this->error('❌ Sync failed: ' . $e->getMessage());
            Log::error('FIFA Connect sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Sync specific player
     */
    private function syncPlayer()
    {
        $fifaId = $this->option('fifa-id');
        
        if (!$fifaId) {
            $this->error('❌ FIFA ID is required for sync-player action');
            $this->line('Usage: php artisan fifa:connect sync-player --fifa-id=FIFA_ID');
            return;
        }

        $this->info("Syncing player with FIFA ID: {$fifaId}");
        
        try {
            $playerData = $this->fifaConnectService->fetchPlayer($fifaId);
            
            if (!$playerData['success']) {
                $this->error("❌ Player not found: {$fifaId}");
                $this->line("Error: " . $playerData['error']);
                return;
            }

            $this->info('✅ Player data retrieved successfully!');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Name', $playerData['data']['name'] ?? 'N/A'],
                    ['Position', $playerData['data']['position'] ?? 'N/A'],
                    ['Nationality', $playerData['data']['nationality'] ?? 'N/A'],
                    ['Overall Rating', $playerData['data']['overall_rating'] ?? 'N/A'],
                    ['Source', $playerData['source']],
                ]
            );

        } catch (\Exception $e) {
            $this->error('❌ Failed to sync player: ' . $e->getMessage());
            Log::error("FIFA Connect player sync failed for {$fifaId}: " . $e->getMessage());
        }
    }

    /**
     * Clear FIFA Connect caches
     */
    private function clearCache()
    {
        $this->info('Clearing FIFA Connect caches...');
        
        try {
            $this->fifaConnectService->clearCaches();
            $this->info('✅ FIFA Connect caches cleared successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Failed to clear caches: ' . $e->getMessage());
            Log::error('FIFA Connect cache clear failed: ' . $e->getMessage());
        }
    }

    /**
     * Show FIFA Connect statistics
     */
    private function showStatistics()
    {
        $this->info('FIFA Connect Statistics');
        $this->newLine();
        
        $stats = $this->fifaConnectService->getStatistics();
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Players (with FIFA ID)', $stats['total_players']],
                ['Total Clubs (with FIFA ID)', $stats['total_clubs']],
                ['Total Associations (with FIFA ID)', $stats['total_associations']],
                ['Last Sync', $stats['last_sync'] ? $stats['last_sync']->format('Y-m-d H:i:s') : 'Never'],
                ['Cache Driver', $stats['cache_status']['driver']],
                ['Cache Prefix', $stats['cache_status']['prefix']],
            ]
        );

        // Show connectivity status
        $this->newLine();
        $this->info('Connectivity Status:');
        $connectivity = $stats['connectivity_status'];
        
        if ($connectivity['success']) {
            $this->info('✅ Connected to FIFA Connect API');
            $this->line("Response Time: {$connectivity['response_time']}ms");
            $this->line("API Version: {$connectivity['api_version']}");
        } else {
            $this->error('❌ Not connected to FIFA Connect API');
            $this->line("Error: {$connectivity['error']}");
        }
    }

    /**
     * Show help information
     */
    private function showHelp()
    {
        $this->line('Available actions:');
        $this->line('  test         - Test FIFA Connect connectivity');
        $this->line('  sync         - Sync players from FIFA Connect');
        $this->line('  sync-player  - Sync specific player by FIFA ID');
        $this->line('  clear-cache  - Clear FIFA Connect caches');
        $this->line('  stats        - Show FIFA Connect statistics');
        $this->newLine();
        $this->line('Examples:');
        $this->line('  php artisan fifa:connect test');
        $this->line('  php artisan fifa:connect sync --batch-size=100 --force');
        $this->line('  php artisan fifa:connect sync-player --fifa-id=FIFA_123');
        $this->line('  php artisan fifa:connect sync --filters=position:ST --filters=nationality:France');
    }
} 