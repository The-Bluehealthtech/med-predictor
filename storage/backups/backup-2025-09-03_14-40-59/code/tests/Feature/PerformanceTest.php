<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Competition;
use App\Models\Team;
use App\Models\GameMatch;
use App\Models\Club;
use App\Services\CacheService;
use App\Services\LeagueSchedulingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_schedule_generation_performance()
    {
        // Create test data
        $competition = Competition::factory()->create();
        $teams = Team::factory()->count(20)->create();
        $competition->teams()->attach($teams->pluck('id'));

        $startTime = microtime(true);
        
        // Generate full schedule
        $schedulingService = new LeagueSchedulingService();
        $result = $schedulingService->generateFullSchedule($competition, '2025-01-01', 7);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertTrue($result['success']);
        $this->assertLessThan(5.0, $executionTime, 'Schedule generation should complete within 5 seconds');
        
        // Verify matches were created
        $this->assertDatabaseCount('game_matches', 380); // 20 teams * 19 matches each
    }

    public function test_schedule_retrieval_performance()
    {
        // Create test data with existing matches
        $competition = Competition::factory()->create();
        $teams = Team::factory()->count(8)->create();
        $competition->teams()->attach($teams->pluck('id'));

        // Create some matches
        GameMatch::factory()->count(50)->create([
            'competition_id' => $competition->id,
        ]);

        $startTime = microtime(true);
        
        // Test schedule retrieval
        $cacheService = new CacheService();
        $schedule = $cacheService->getCompetitionSchedule($competition);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertIsArray($schedule);
        $this->assertLessThan(1.0, $executionTime, 'Schedule retrieval should complete within 1 second');
    }

    public function test_cache_effectiveness()
    {
        $competition = Competition::factory()->create();
        $teams = Team::factory()->count(10)->create();
        $competition->teams()->attach($teams->pluck('id'));

        $cacheService = new CacheService();

        // First call - should hit database
        $startTime = microtime(true);
        $schedule1 = $cacheService->getCompetitionSchedule($competition);
        $firstCallTime = microtime(true) - $startTime;

        // Second call - should hit cache
        $startTime = microtime(true);
        $schedule2 = $cacheService->getCompetitionSchedule($competition);
        $secondCallTime = microtime(true) - $startTime;

        $this->assertEquals($schedule1, $schedule2);
        $this->assertLessThan($firstCallTime, $secondCallTime, 'Cached call should be faster');
    }

    public function test_query_optimization_with_indexes()
    {
        $competition = Competition::factory()->create();
        $teams = Team::factory()->count(16)->create();
        $competition->teams()->attach($teams->pluck('id'));

        // Create matches
        GameMatch::factory()->count(100)->create([
            'competition_id' => $competition->id,
        ]);

        // Test query performance with indexes
        $startTime = microtime(true);
        
        $matches = GameMatch::where('competition_id', $competition->id)
            ->where('match_status', 'scheduled')
            ->where('kickoff_time', '>=', now())
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('kickoff_time')
            ->get();
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(0.5, $executionTime, 'Optimized query should complete within 0.5 seconds');
    }

    public function test_eager_loading_performance()
    {
        $competition = Competition::factory()->create();
        $teams = Team::factory()->count(12)->create();
        $competition->teams()->attach($teams->pluck('id'));

        // Create matches with relationships
        GameMatch::factory()->count(50)->create([
            'competition_id' => $competition->id,
        ]);

        // Test without eager loading
        $startTime = microtime(true);
        $matchesWithoutEager = GameMatch::where('competition_id', $competition->id)->get();
        foreach ($matchesWithoutEager as $match) {
            $match->homeTeam->name; // This would trigger N+1 queries
        }
        $timeWithoutEager = microtime(true) - $startTime;

        // Test with eager loading
        $startTime = microtime(true);
        $matchesWithEager = GameMatch::where('competition_id', $competition->id)
            ->with(['homeTeam', 'awayTeam'])
            ->get();
        foreach ($matchesWithEager as $match) {
            $match->homeTeam->name; // This should be preloaded
        }
        $timeWithEager = microtime(true) - $startTime;

        $this->assertLessThan($timeWithoutEager, $timeWithEager, 'Eager loading should be faster');
    }

    public function test_cache_invalidation()
    {
        $competition = Competition::factory()->create();
        $cacheService = new CacheService();

        // Get initial schedule
        $schedule1 = $cacheService->getCompetitionSchedule($competition);
        
        // Create a new match
        GameMatch::factory()->create([
            'competition_id' => $competition->id,
        ]);

        // Clear cache
        $cacheService->clearCompetitionCache($competition);
        
        // Get schedule again
        $schedule2 = $cacheService->getCompetitionSchedule($competition);
        
        // Should be different due to new match
        $this->assertNotEquals($schedule1, $schedule2);
    }

    public function test_database_query_count_optimization()
    {
        $competition = Competition::factory()->create();
        $teams = Team::factory()->count(8)->create();
        $competition->teams()->attach($teams->pluck('id'));

        GameMatch::factory()->count(30)->create([
            'competition_id' => $competition->id,
        ]);

        // Count queries without optimization
        DB::enableQueryLog();
        
        $matches = GameMatch::where('competition_id', $competition->id)->get();
        foreach ($matches as $match) {
            $match->homeTeam->name;
            $match->awayTeam->name;
        }
        
        $queryCountWithoutOptimization = count(DB::getQueryLog());
        DB::flushQueryLog();

        // Count queries with optimization
        DB::enableQueryLog();
        
        $matches = GameMatch::where('competition_id', $competition->id)
            ->with(['homeTeam', 'awayTeam'])
            ->get();
        foreach ($matches as $match) {
            $match->homeTeam->name;
            $match->awayTeam->name;
        }
        
        $queryCountWithOptimization = count(DB::getQueryLog());

        $this->assertLessThan($queryCountWithoutOptimization, $queryCountWithOptimization, 
            'Optimized queries should use fewer database calls');
    }
} 