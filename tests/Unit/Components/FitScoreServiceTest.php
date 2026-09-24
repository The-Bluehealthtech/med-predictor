<?php

namespace Tests\Unit\Components;

use App\Models\PerformanceMetric;
use App\Models\Player;
use App\Services\Fit\FitScoreService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FitScoreServiceTest extends TestCase
{
    private Player $player;
    private FitScoreService $service;

    protected function setUp(): void
    {
        parent::setUp();

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
            $table->decimal('confidence_score', 3, 2)->default(1.00);
            $table->boolean('is_verified')->default(false);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->player = new Player();
        $this->player->id = 1;

        $this->service = new FitScoreService();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('performance_metrics');

        parent::tearDown();
    }

    public function test_verified_percentage_metric_is_kept_on_100_scale(): void
    {
        $this->createMetric(
            type: 'physical',
            name: 'endurance',
            value: 84,
            unit: '%'
        );

        $result = $this->service->calculate($this->player);

        $this->assertEquals(84.0, $result['physical_score']);
        $this->assertFalse($result['complete']);
        $this->assertNull($result['overall_score']);
    }

    public function test_verified_score_out_of_ten_is_converted_to_100_scale(): void
    {
        $this->createMetric(
            type: 'tactical',
            name: 'decision_making',
            value: 8.2,
            unit: 'score'
        );

        $result = $this->service->calculate($this->player);

        $this->assertEquals(82.0, $result['tactical_score']);
        $this->assertNull($result['overall_score']);
    }

    public function test_unverified_metric_is_completely_ignored(): void
    {
        $this->createMetric(
            type: 'technical',
            name: 'passing',
            value: 91,
            unit: '%',
            verified: false
        );

        $result = $this->service->calculate($this->player);

        $this->assertNull($result['technical_score']);
        $this->assertNull($result['overall_score']);
    }

    public function test_metric_with_incompatible_unit_is_rejected(): void
    {
        $this->createMetric(
            type: 'technical',
            name: 'passing',
            value: 85,
            unit: 'km/h'
        );

        $result = $this->service->calculate($this->player);

        $this->assertNull($result['technical_score']);
        $this->assertNull($result['overall_score']);
    }

    public function test_latest_verified_metric_is_used_for_same_metric_name(): void
    {
        $this->createMetric(
            type: 'mental',
            name: 'motivation',
            value: 70,
            unit: '%',
            date: now()->subDays(5)
        );

        $this->createMetric(
            type: 'mental',
            name: 'motivation',
            value: 88,
            unit: '%',
            date: now()->subDay()
        );

        $result = $this->service->calculate($this->player);

        $this->assertEquals(88.0, $result['mental_score']);
    }

    public function test_fit_score_remains_null_while_social_axis_is_unavailable(): void
    {
        $this->createMetric(
            type: 'physical',
            name: 'endurance',
            value: 80,
            unit: '%'
        );

        $this->createMetric(
            type: 'technical',
            name: 'passing',
            value: 82,
            unit: '%'
        );

        $this->createMetric(
            type: 'tactical',
            name: 'positioning',
            value: 8.4,
            unit: 'score'
        );

        $this->createMetric(
            type: 'mental',
            name: 'concentration',
            value: 86,
            unit: '%'
        );

        $this->createMetric(
            type: 'social',
            name: 'team_cohesion',
            value: 9,
            unit: 'score'
        );

        $result = $this->service->calculate($this->player);

        $this->assertEquals(80.0, $result['physical_score']);
        $this->assertEquals(82.0, $result['technical_score']);
        $this->assertEquals(84.0, $result['tactical_score']);
        $this->assertEquals(86.0, $result['mental_score']);

        $this->assertNull($result['social_score']);
        $this->assertFalse($result['complete']);
        $this->assertNull($result['overall_score']);
    }

    private function createMetric(
        string $type,
        string $name,
        float $value,
        string $unit,
        bool $verified = true,
        $date = null
    ): PerformanceMetric {
        return PerformanceMetric::create([
            'player_id' => $this->player->id,
            'metric_type' => $type,
            'metric_name' => $name,
            'metric_value' => $value,
            'metric_unit' => $unit,
            'measurement_date' => $date ?? now(),
            'data_source' => 'manual',
            'confidence_score' => 0.90,
            'is_verified' => $verified,
            'verified_by' => $verified ? 1 : null,
            'verified_at' => $verified ? now() : null,
            'created_by' => 1,
        ]);
    }
}
