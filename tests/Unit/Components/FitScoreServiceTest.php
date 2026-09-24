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

    public function test_social_percentage_metric_is_accepted(): void
    {
        $this->createMetric(
            type: 'social',
            name: 'team_cohesion',
            value: 76,
            unit: '%'
        );

        $result = $this->service->calculate($this->player);

        $this->assertEquals(76.0, $result['social_score']);
        $this->assertNull($result['overall_score']);
    }

    public function test_social_metric_with_explicit_metadata_scale_is_normalized(): void
    {
        $this->createMetric(
            type: 'social',
            name: 'communication_skills',
            value: 4.5,
            unit: 'score',
            metadata: [
                'scale_min' => 0,
                'scale_max' => 5,
            ]
        );

        $result = $this->service->calculate($this->player);

        $this->assertEquals(90.0, $result['social_score']);
    }

    public function test_all_five_axes_produce_a_global_fit_score(): void
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
            value: 88,
            unit: '%'
        );

        $result = $this->service->calculate($this->player);

        $this->assertEquals(80.0, $result['physical_score']);
        $this->assertEquals(82.0, $result['technical_score']);
        $this->assertEquals(84.0, $result['tactical_score']);
        $this->assertEquals(86.0, $result['mental_score']);
        $this->assertEquals(88.0, $result['social_score']);

        $this->assertTrue($result['complete']);
        $this->assertEquals(84.0, $result['overall_score']);
        $this->assertEquals(0.90, $result['confidence']);
    }

    public function test_result_contains_traceable_metric_evidence(): void
    {
        $metric = $this->createMetric(
            type: 'technical',
            name: 'passing',
            value: 87,
            unit: '%'
        );

        $result = $this->service->calculate($this->player);

        $evidence = $result['axes']['technical']['metrics'];

        $this->assertCount(1, $evidence);
        $this->assertSame($metric->id, $evidence[0]['metric_id']);
        $this->assertSame('passing', $evidence[0]['name']);
        $this->assertEquals(87.0, $evidence[0]['raw_value']);
        $this->assertSame('%', $evidence[0]['unit']);
        $this->assertEquals(87.0, $evidence[0]['score']);
        $this->assertEquals(0.90, $evidence[0]['confidence']);
        $this->assertSame('manual', $evidence[0]['source']);
        $this->assertNotNull($evidence[0]['measurement_date']);
    }

    public function test_calculation_respects_snapshot_reference_date(): void
    {
        $asOf = now()->subDays(5);

        $this->createMetric(
            type: 'technical',
            name: 'passing',
            value: 81,
            unit: '%',
            date: $asOf->copy()->subDay()
        );

        $this->createMetric(
            type: 'technical',
            name: 'passing',
            value: 96,
            unit: '%',
            date: $asOf->copy()->addDay()
        );

        $result = $this->service->calculate(
            $this->player,
            30,
            $asOf
        );

        $this->assertEquals(81.0, $result['technical_score']);
    }

    public function test_diagnosis_reports_verified_accepted_metrics_and_missing_axes(): void
    {
        $asOf = now()->startOfSecond();

        // Vérifiée et exploitable.
        $this->createMetric(
            type: 'physical',
            name: 'endurance',
            value: 84,
            unit: '%',
            date: $asOf->copy()->subDay()
        );

        // Vérifiée, mais unité incompatible : comptée comme vérifiée,
        // jamais retenue par le moteur FIT.
        $this->createMetric(
            type: 'technical',
            name: 'passing',
            value: 8,
            unit: 'score',
            date: $asOf->copy()->subDay()
        );

        // Vérifiée et exploitable.
        $this->createMetric(
            type: 'mental',
            name: 'motivation',
            value: 76,
            unit: '%',
            date: $asOf->copy()->subDay()
        );

        // Non vérifiée : totalement ignorée.
        $this->createMetric(
            type: 'tactical',
            name: 'positioning',
            value: 8,
            unit: 'score',
            verified: false,
            date: $asOf->copy()->subDay()
        );

        // Vérifiée mais hors fenêtre de 30 jours.
        $this->createMetric(
            type: 'social',
            name: 'team_cohesion',
            value: 90,
            unit: '%',
            date: $asOf->copy()->subDays(31)
        );

        $diagnosis = $this->service->diagnose(
            $this->player,
            30,
            $asOf
        );

        $this->assertSame(30, $diagnosis['window_days']);
        $this->assertSame(5, $diagnosis['total_metric_count_all_time']);
        $this->assertSame(4, $diagnosis['recent_metric_count']);
        $this->assertSame(3, $diagnosis['verified_metric_count']);
        $this->assertSame(4, $diagnosis['verified_metric_count_all_time']);
        $this->assertNotNull($diagnosis['latest_verified_metric_date']);
        $this->assertSame(2, $diagnosis['accepted_metric_count']);

        $this->assertSame(
            ['technical', 'tactical', 'social'],
            $diagnosis['missing_axes']
        );

        $this->assertEquals(
            84.0,
            $diagnosis['calculation']['physical_score']
        );

        $this->assertEquals(
            76.0,
            $diagnosis['calculation']['mental_score']
        );

        $this->assertNull(
            $diagnosis['calculation']['technical_score']
        );
    }

    private function createMetric(
        string $type,
        string $name,
        float $value,
        string $unit,
        bool $verified = true,
        $date = null,
        ?array $metadata = null
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
            'metadata' => $metadata,
        ]);
    }
}
