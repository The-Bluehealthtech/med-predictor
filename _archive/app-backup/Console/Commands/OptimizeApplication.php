<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Services\CacheService;
use App\Models\Player;
use App\Models\Club;
use App\Models\Competition;
use App\Models\HealthRecord;
use App\Models\MedicalPrediction;

class OptimizeApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize {--type=all : Type of optimization (all, database, cache, indexes)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize application performance by running database optimization, cache warming, and performance checks';

    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        
        $this->info('Starting application optimization...');
        
        switch ($type) {
            case 'database':
                $this->optimizeDatabase();
                break;
            case 'cache':
                $this->optimizeCache();
                break;
            case 'indexes':
                $this->optimizeIndexes();
                break;
            case 'all':
            default:
                $this->optimizeDatabase();
                $this->optimizeCache();
                $this->optimizeIndexes();
                $this->runPerformanceChecks();
                break;
        }
        
        $this->info('Application optimization completed successfully!');
    }

    /**
     * Optimize database
     */
    private function optimizeDatabase(): void
    {
        $this->info('Optimizing database...');
        
        try {
            // Analyze tables
            $tables = ['players', 'clubs', 'competitions', 'matches', 'health_records', 'medical_predictions'];
            
            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $this->line("Analyzing table: {$table}");
                    DB::statement("ANALYZE TABLE {$table}");
                }
            }
            
            // Clear query cache if supported
            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement('FLUSH QUERY CACHE');
            }
            
            $this->info('Database optimization completed.');
        } catch (\Exception $e) {
            $this->error("Database optimization failed: {$e->getMessage()}");
        }
    }

    /**
     * Optimize cache
     */
    private function optimizeCache(): void
    {
        $this->info('Optimizing cache...');
        
        try {
            // Clear all caches
            $this->line('Clearing existing caches...');
            $this->cacheService->clearAllCaches();
            
            // Clear Laravel caches
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            
            // Warm up caches
            $this->line('Warming up caches...');
            $this->cacheService->warmUpCaches();
            
            // Cache frequently accessed data
            $this->cacheFrequentlyAccessedData();
            
            $this->info('Cache optimization completed.');
        } catch (\Exception $e) {
            $this->error("Cache optimization failed: {$e->getMessage()}");
        }
    }

    /**
     * Optimize indexes
     */
    private function optimizeIndexes(): void
    {
        $this->info('Optimizing database indexes...');
        
        try {
            // Run the performance indexes migration
            Artisan::call('migrate', ['--path' => 'database/migrations/2025_07_15_000000_add_performance_indexes.php']);
            
            $this->info('Database indexes optimization completed.');
        } catch (\Exception $e) {
            $this->error("Index optimization failed: {$e->getMessage()}");
        }
    }

    /**
     * Cache frequently accessed data
     */
    private function cacheFrequentlyAccessedData(): void
    {
        $this->line('Caching frequently accessed data...');
        
        // Cache player statistics
        Player::chunk(100, function ($players) {
            foreach ($players as $player) {
                $this->cacheService->getPlayerStats($player);
            }
        });
        
        // Cache club statistics
        Club::chunk(50, function ($clubs) {
            foreach ($clubs as $club) {
                $this->cacheService->getClubStats($club);
            }
        });
        
        // Cache competition schedules
        Competition::chunk(25, function ($competitions) {
            foreach ($competitions as $competition) {
                $this->cacheService->getCompetitionSchedule($competition);
            }
        });
        
        // Cache dashboard statistics for different user types
        $userTypes = ['system_admin', 'association', 'club'];
        foreach ($userTypes as $userType) {
            $this->cacheService->getDashboardStats($userType, 1);
        }
    }

    /**
     * Run performance checks
     */
    private function runPerformanceChecks(): void
    {
        $this->info('Running performance checks...');
        
        // Check database connection
        $startTime = microtime(true);
        DB::connection()->getPdo();
        $dbConnectionTime = microtime(true) - $startTime;
        
        // Check cache performance
        $startTime = microtime(true);
        Cache::put('performance_test', 'test', 60);
        Cache::get('performance_test');
        $cacheTime = microtime(true) - $startTime;
        
        // Check query performance
        $startTime = microtime(true);
        Player::count();
        $queryTime = microtime(true) - $startTime;
        
        // Display performance metrics
        $this->table(
            ['Metric', 'Time (seconds)', 'Status'],
            [
                ['Database Connection', number_format($dbConnectionTime, 4), $dbConnectionTime < 0.1 ? '✅ Good' : '⚠️ Slow'],
                ['Cache Operations', number_format($cacheTime, 4), $cacheTime < 0.01 ? '✅ Good' : '⚠️ Slow'],
                ['Basic Query', number_format($queryTime, 4), $queryTime < 0.1 ? '✅ Good' : '⚠️ Slow'],
            ]
        );
        
        // Check system statistics
        $stats = [
            'total_players' => Player::count(),
            'total_clubs' => Club::count(),
            'total_competitions' => Competition::count(),
            'total_health_records' => HealthRecord::count(),
            'total_predictions' => MedicalPrediction::count(),
        ];
        
        $this->table(
            ['Entity', 'Count'],
            collect($stats)->map(fn($count, $entity) => [$entity, number_format($count)])->toArray()
        );
    }
} 