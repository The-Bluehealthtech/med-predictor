<?php

namespace Tests\Feature;

use App\Services\PerformanceScoreCalculator;
use App\Services\PerformanceScoreDataSource;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class PerformanceScoreDataSourceTest extends TestCase
{
    private string $previousConnection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousConnection = DB::getDefaultConnection();
        config()->set('database.connections.score_test', [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        DB::setDefaultConnection('score_test');
        Schema::create('players', function (Blueprint $table): void {
            $table->id();
            $table->string('position')->nullable();
        });
        Schema::create('performances', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->date('match_date');
            $table->string('score_origin')->default('unverified');
            $table->string('position_played')->nullable();
            $table->integer('minutes_played')->nullable();
            foreach (['goals_scored', 'assists', 'shots_on_target', 'passes_attempted',
                'passes_completed', 'tackles_attempted', 'tackles_won', 'yellow_cards',
                'red_cards'] as $column) $table->integer($column)->nullable();
            $table->json('additional_metrics')->nullable();
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('score_test');
        DB::purge('score_test');
        DB::setDefaultConnection($this->previousConnection);
        parent::tearDown();
    }

    public function test_internal_player_id_works_without_fifa_id_and_demo_is_excluded(): void
    {
        DB::table('players')->insert(['id' => 853, 'position' => 'CB']);
        $base = ['player_id' => 853, 'match_date' => '2026-01-01', 'minutes_played' => 90,
            'passes_attempted' => 30, 'passes_completed' => 25, 'tackles_attempted' => 4,
            'tackles_won' => 3, 'goals_scored' => 0, 'assists' => 0, 'shots_on_target' => 0,
            'yellow_cards' => 0, 'red_cards' => 0];
        DB::table('performances')->insert($base + ['score_origin' => 'synthetic']);
        DB::table('performances')->insert(array_merge($base, ['match_date' => '2026-01-02',
            'score_origin' => 'observed', 'position_played' => 'CB']));
        $rows = (new PerformanceScoreDataSource())->all();
        self::assertCount(1, $rows);
        self::assertCount(1, $rows[0]['matches']);
        self::assertSame('2026-01-02', $rows[0]['matches'][0]['date']);
        self::assertSame(853, $rows[0]['id']);
    }

    public function test_unverified_rows_do_not_produce_a_score(): void
    {
        DB::table('players')->insert(['id' => 853, 'position' => 'CB']);
        DB::table('performances')->insert(['player_id' => 853, 'match_date' => '2026-01-01',
            'minutes_played' => 90, 'passes_attempted' => 30, 'passes_completed' => 25]);
        $rows = (new PerformanceScoreDataSource())->all();
        self::assertSame([], $rows[0]['matches']);
        $result = (new PerformanceScoreCalculator(config('player_performance_score')))->calculate($rows)[0];
        self::assertNull($result['score_corrige']);
        self::assertSame('aucune donnée observée', $result['raison']);
    }
}
