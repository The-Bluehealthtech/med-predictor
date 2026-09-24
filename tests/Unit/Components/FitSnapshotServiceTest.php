<?php

namespace Tests\Unit\Components;

use App\Models\FitScoreSnapshot;
use App\Models\PerformanceMetric;
use App\Models\Player;
use App\Services\Fit\FitScoreService;
use App\Services\Fit\FitSnapshotService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FitSnapshotServiceTest extends TestCase
{
    private Player $player;
    private FitSnapshotService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('fit_score_snapshots');
        Schema::dropIfExists('performance_metrics');

        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->string('metric_type');
            $table->string('metric_name');
            $table->decimal('metric_value', 10, 4);
            $table->string('metric_unit', 50);
            $table->timestamp('measurement_date');
            $table->string('data_source');
            $table->decimal('confidence_score', 3, 2)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fit_score_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamp('snapshot_at');
            $table->decimal('physical_score', 5, 2)->nullable();
            $table->decimal('technical_score', 5, 2)->nullable();
            $table->decimal('tactical_score', 5, 2)->nullable();
            $table->decimal('mental_score', 5, 2)->nullable();
            $table->decimal('social_score', 5, 2)->nullable();
            $table->decimal('fit_score', 5, 2)->nullable();
            $table->decimal('confidence_score', 5, 4)->nullable();
            $table->boolean('is_complete')->default(false);
            $table->unsignedSmallInteger('window_days')->default(30);
            $table->string('calculation_version', 32);
            $table->string('input_signature', 64)->nullable();
            $table->json('evidence')->nullable();

            $table->unique([
                'player_id',
                'calculation_version',
                'input_signature',
            ]);
            $table->unsignedBigInteger('generated_by_user_id')->nullable();
            $table->timestamps();
        });

        $this->player = new Player();
        $this->player->id = 1;
        $this->player->tenant_id = 42;

        $this->service = new FitSnapshotService(
            new FitScoreService()
        );
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('fit_score_snapshots');
        Schema::dropIfExists('performance_metrics');

        parent::tearDown();
    }

    public function test_complete_fit_calculation_is_persisted_as_snapshot(): void
    {
        $snapshotAt = now()->startOfSecond();

        $this->createCompleteMetrics($snapshotAt);

        $result = $this->service->createSnapshot(
            $this->player,
            30,
            $snapshotAt
        );

        $snapshot = $result['snapshot'];

        $this->assertTrue($snapshot->exists);
        $this->assertSame(42, $snapshot->tenant_id);
        $this->assertTrue($snapshot->is_complete);
        $this->assertEquals(84.0, $snapshot->fit_score);
        $this->assertEquals(0.90, $snapshot->confidence_score);
        $this->assertSame(
            FitSnapshotService::CALCULATION_VERSION,
            $snapshot->calculation_version
        );

        $this->assertNotEmpty(
            $snapshot->evidence['axes']['technical']['metrics']
        );

        $this->assertNull(
            $result['evolution']['previous_snapshot_id']
        );
        $this->assertNull($result['evolution']['points']);
        $this->assertNull($result['evolution']['percent']);
    }

    public function test_evolution_uses_previous_complete_snapshot_only(): void
    {
        $snapshotAt = now()->startOfSecond();

        $previous = FitScoreSnapshot::create([
            'player_id' => 1,
            'snapshot_at' => $snapshotAt->copy()->subDays(10),
            'physical_score' => 80,
            'technical_score' => 80,
            'tactical_score' => 80,
            'mental_score' => 80,
            'social_score' => 80,
            'fit_score' => 80,
            'confidence_score' => 0.90,
            'is_complete' => true,
            'window_days' => 30,
            'calculation_version' => FitSnapshotService::CALCULATION_VERSION,
        ]);

        FitScoreSnapshot::create([
            'player_id' => 1,
            'snapshot_at' => $snapshotAt->copy()->subDays(5),
            'physical_score' => 90,
            'technical_score' => 90,
            'tactical_score' => null,
            'mental_score' => 90,
            'social_score' => 90,
            'fit_score' => null,
            'confidence_score' => 0.90,
            'is_complete' => false,
            'window_days' => 30,
            'calculation_version' => FitSnapshotService::CALCULATION_VERSION,
        ]);

        $this->createCompleteMetrics($snapshotAt);

        $result = $this->service->createSnapshot(
            $this->player,
            30,
            $snapshotAt
        );

        $evolution = $result['evolution'];

        $this->assertSame(
            $previous->id,
            $evolution['previous_snapshot_id']
        );
        $this->assertEquals(80.0, $evolution['previous_score']);
        $this->assertEquals(84.0, $evolution['current_score']);
        $this->assertEquals(4.0, $evolution['points']);
        $this->assertEquals(5.0, $evolution['percent']);
    }

    public function test_snapshot_from_another_calculation_version_is_not_compared(): void
    {
        $snapshotAt = now()->startOfSecond();

        FitScoreSnapshot::create([
            'player_id' => 1,
            'snapshot_at' => $snapshotAt->copy()->subDays(10),
            'physical_score' => 80,
            'technical_score' => 80,
            'tactical_score' => 80,
            'mental_score' => 80,
            'social_score' => 80,
            'fit_score' => 80,
            'confidence_score' => 0.90,
            'is_complete' => true,
            'window_days' => 30,
            'calculation_version' => 'fit_v0',
        ]);

        $this->createCompleteMetrics($snapshotAt);

        $result = $this->service->createSnapshot(
            $this->player,
            30,
            $snapshotAt
        );

        $this->assertNull(
            $result['evolution']['previous_snapshot_id']
        );
        $this->assertNull($result['evolution']['points']);
        $this->assertNull($result['evolution']['percent']);
    }

    public function test_incomplete_snapshot_never_produces_fit_evolution(): void
    {
        $snapshotAt = now()->startOfSecond();

        FitScoreSnapshot::create([
            'player_id' => 1,
            'snapshot_at' => $snapshotAt->copy()->subDays(10),
            'physical_score' => 80,
            'technical_score' => 80,
            'tactical_score' => 80,
            'mental_score' => 80,
            'social_score' => 80,
            'fit_score' => 80,
            'confidence_score' => 0.90,
            'is_complete' => true,
            'window_days' => 30,
            'calculation_version' => FitSnapshotService::CALCULATION_VERSION,
        ]);

        $this->createMetric(
            'physical',
            'endurance',
            80,
            '%',
            $snapshotAt->copy()->subDay()
        );

        $result = $this->service->createSnapshot(
            $this->player,
            30,
            $snapshotAt
        );

        $this->assertFalse($result['snapshot']->is_complete);
        $this->assertNull($result['snapshot']->fit_score);

        $this->assertNull(
            $result['evolution']['previous_snapshot_id']
        );
        $this->assertNull($result['evolution']['points']);
        $this->assertNull($result['evolution']['percent']);
    }

    public function test_latest_for_player_uses_only_past_complete_current_version_snapshots(): void
    {
        $now = now()->startOfSecond();

        $previous = FitScoreSnapshot::create([
            'player_id' => 1,
            'snapshot_at' => $now->copy()->subDays(10),
            'physical_score' => 78,
            'technical_score' => 78,
            'tactical_score' => 78,
            'mental_score' => 78,
            'social_score' => 78,
            'fit_score' => 78,
            'confidence_score' => 0.90,
            'is_complete' => true,
            'window_days' => 30,
            'calculation_version' => FitSnapshotService::CALCULATION_VERSION,
        ]);

        $current = FitScoreSnapshot::create([
            'player_id' => 1,
            'snapshot_at' => $now->copy()->subDay(),
            'physical_score' => 82,
            'technical_score' => 82,
            'tactical_score' => 82,
            'mental_score' => 82,
            'social_score' => 82,
            'fit_score' => 82,
            'confidence_score' => 0.92,
            'is_complete' => true,
            'window_days' => 30,
            'calculation_version' => FitSnapshotService::CALCULATION_VERSION,
        ]);

        $incomplete = FitScoreSnapshot::create([
            'player_id' => 1,
            'snapshot_at' => $now,
            'physical_score' => 80,
            'technical_score' => 81,
            'tactical_score' => null,
            'mental_score' => null,
            'social_score' => null,
            'fit_score' => null,
            'is_complete' => false,
            'window_days' => 30,
            'calculation_version' => FitSnapshotService::CALCULATION_VERSION,
        ]);

        // Un snapshot futur de la bonne version ne doit jamais être affiché.
        FitScoreSnapshot::create([
            'player_id' => 1,
            'snapshot_at' => $now->copy()->addMinute(),
            'physical_score' => 99,
            'technical_score' => 99,
            'tactical_score' => 99,
            'mental_score' => 99,
            'social_score' => 99,
            'fit_score' => 99,
            'is_complete' => true,
            'window_days' => 30,
            'calculation_version' => FitSnapshotService::CALCULATION_VERSION,
        ]);

        // Une autre version, même plus récente, n'est pas comparable.
        FitScoreSnapshot::create([
            'player_id' => 1,
            'snapshot_at' => $now->copy()->subHours(12),
            'physical_score' => 97,
            'technical_score' => 97,
            'tactical_score' => 97,
            'mental_score' => 97,
            'social_score' => 97,
            'fit_score' => 97,
            'is_complete' => true,
            'window_days' => 30,
            'calculation_version' => 'fit_v0',
        ]);

        $result = $this->service->latestForPlayer($this->player);

        $this->assertSame($current->id, $result['snapshot']->id);
        $this->assertSame($previous->id, $result['previous_snapshot']->id);
        $this->assertEquals(4.0, $result['evolution']['points']);
        $this->assertEquals(5.1, $result['evolution']['percent']);

        $this->assertSame(
            $incomplete->id,
            $result['latest_attempt']->id
        );

        $this->assertSame(
            ['tactical', 'mental', 'social'],
            $result['missing_axes']
        );
    }

    public function test_same_inputs_do_not_create_duplicate_snapshots(): void
    {
        $firstAt = now()->startOfSecond();

        $this->createCompleteMetrics($firstAt);

        $first = $this->service->createSnapshot(
            $this->player,
            30,
            $firstAt
        );

        $second = $this->service->createSnapshot(
            $this->player,
            30,
            $firstAt->copy()->addHour()
        );

        $this->assertTrue($first['created']);
        $this->assertFalse($second['created']);

        $this->assertSame(
            $first['snapshot']->id,
            $second['snapshot']->id
        );

        $this->assertSame(
            $first['snapshot']->input_signature,
            $second['snapshot']->input_signature
        );

        $this->assertSame(
            1,
            FitScoreSnapshot::query()
                ->where('player_id', $this->player->id)
                ->whereNotNull('input_signature')
                ->count()
        );
    }

    public function test_changed_verified_input_creates_new_snapshot_and_evolution(): void
    {
        $firstAt = now()->startOfSecond();

        $this->createCompleteMetrics($firstAt);

        $first = $this->service->createSnapshot(
            $this->player,
            30,
            $firstAt
        );

        $secondAt = $firstAt->copy()->addHour();

        $this->createMetric(
            'technical',
            'passing',
            92,
            '%',
            $secondAt->copy()->subMinute()
        );

        $second = $this->service->createSnapshot(
            $this->player,
            30,
            $secondAt
        );

        $this->assertTrue($first['created']);
        $this->assertTrue($second['created']);

        $this->assertNotSame(
            $first['snapshot']->id,
            $second['snapshot']->id
        );

        $this->assertNotSame(
            $first['snapshot']->input_signature,
            $second['snapshot']->input_signature
        );

        $this->assertEquals(84.0, $first['snapshot']->fit_score);
        $this->assertEquals(86.0, $second['snapshot']->fit_score);

        $this->assertSame(
            $first['snapshot']->id,
            $second['evolution']['previous_snapshot_id']
        );

        $this->assertEquals(2.0, $second['evolution']['points']);
        $this->assertEquals(2.4, $second['evolution']['percent']);

        $this->assertSame(
            2,
            FitScoreSnapshot::query()
                ->where('player_id', $this->player->id)
                ->whereNotNull('input_signature')
                ->count()
        );
    }

    private function createCompleteMetrics($snapshotAt): void
    {
        $date = $snapshotAt->copy()->subDay();

        $this->createMetric('physical', 'endurance', 80, '%', $date);
        $this->createMetric('technical', 'passing', 82, '%', $date);
        $this->createMetric('tactical', 'positioning', 8.4, 'score', $date);
        $this->createMetric('mental', 'concentration', 86, '%', $date);
        $this->createMetric('social', 'team_cohesion', 88, '%', $date);
    }

    private function createMetric(
        string $type,
        string $name,
        float $value,
        string $unit,
        $date
    ): PerformanceMetric {
        return PerformanceMetric::create([
            'player_id' => 1,
            'metric_type' => $type,
            'metric_name' => $name,
            'metric_value' => $value,
            'metric_unit' => $unit,
            'measurement_date' => $date,
            'data_source' => 'manual',
            'confidence_score' => 0.90,
            'is_verified' => true,
            'verified_by' => 1,
            'verified_at' => $date,
            'created_by' => 1,
        ]);
    }
}
