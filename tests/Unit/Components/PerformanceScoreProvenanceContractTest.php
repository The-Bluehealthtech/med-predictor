<?php

namespace Tests\Unit\Components;

use App\Services\PerformanceScoreCalculator;
use App\Services\PerformanceScoreDataSource;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PerformanceScoreProvenanceContractTest extends TestCase
{
    private string $previousConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousConnection = DB::getDefaultConnection();
        config()->set('database.connections.score_contract', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        DB::setDefaultConnection('score_contract');

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
            $table->integer('minutes_played')->default(0);
            foreach ([
                'goals_scored', 'assists', 'shots_on_target',
                'passes_attempted', 'passes_completed',
                'tackles_attempted', 'tackles_won',
                'yellow_cards', 'red_cards',
            ] as $column) {
                $table->integer($column)->nullable();
            }

            $table->json('additional_metrics')->nullable();
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('score_contract');
        DB::purge('score_contract');
        DB::setDefaultConnection($this->previousConnection);

        parent::tearDown();
    }

    public function test_only_observed_direct_player_data_is_used(): void
    {
        DB::table('players')->insert(['id' => 853, 'position' => 'CB']);

        $base = [
            'player_id' => 853,
            'minutes_played' => 90,
            'passes_attempted' => 30,
            'passes_completed' => 25,
            'tackles_attempted' => 4,
            'tackles_won' => 3,
            'goals_scored' => 0,
            'assists' => 0,
            'shots_on_target' => 0,
            'yellow_cards' => 0,
            'red_cards' => 0,
        ];
        DB::table('performances')->insert($base + [
            'match_date' => '2026-01-01',
            'score_origin' => 'synthetic',
        ]);

        DB::table('performances')->insert($base + [
            'match_date' => '2026-01-02',
            'score_origin' => 'observed',
            'position_played' => 'CB',
        ]);

        $rows = (new PerformanceScoreDataSource())->all();

        $this->assertCount(1, $rows);
        $this->assertSame(853, $rows[0]['id']);
        $this->assertCount(1, $rows[0]['matches']);
        $this->assertSame('2026-01-02', $rows[0]['matches'][0]['date']);
    }

    public function test_unverified_data_does_not_produce_a_score(): void
    {
        DB::table('players')->insert(['id' => 853, 'position' => 'CB']);
        DB::table('performances')->insert([
            'player_id' => 853,
            'match_date' => '2026-01-01',
            'minutes_played' => 90,
            'passes_attempted' => 30,
            'passes_completed' => 25,
        ]);

        $rows = (new PerformanceScoreDataSource())->all();
        $result = (new PerformanceScoreCalculator(config('player_performance_score')))
            ->calculate($rows)[0];

        $this->assertSame([], $rows[0]['matches']);
        $this->assertNull($result['score_corrige']);
        $this->assertSame('aucune donnée observée', $result['raison']);
    }
}
