<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PerformanceMetricRecordingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'cache.default' => 'array',
        ]);

        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        DB::reconnect('sqlite');

        Cache::flush();
        Carbon::setTestNow('2026-09-24 12:00:00');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->nullable();
            $table->json('permissions')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('club_id')->nullable();
            $table->unsignedBigInteger('association_id')->nullable();
            $table->unsignedBigInteger('federation_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_system_role')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('organization');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('parent_tenant_id')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('language')->default('en');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('fifa_connect_id')->unique();
            $table->string('status')->default('active');
            $table->timestamps();
        });

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
            $table->unsignedBigInteger('fifa_connect_id')->nullable();
            $table->string('hl7_fhir_resource_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Cache::flush();
        DB::purge('sqlite');

        parent::tearDown();
    }

    public function test_sports_scientist_can_record_real_fit_metric(): void
    {
        $tenantId = $this->createTenant('record');
        $user = User::factory()->create([
            'role' => 'sports_scientist',
            'tenant_id' => $tenantId,
            'permissions' => null,
        ]);
        $playerId = $this->createPlayer($tenantId, 'record');

        $this->actingAs($user);

        $this->postJson(
            $this->endpoint($playerId),
            $this->validPayload()
        )
            ->assertCreated()
            ->assertJsonPath('data.metric_type', 'physical')
            ->assertJsonPath('data.metric_name', 'endurance')
            ->assertJsonPath('data.is_verified', false)
            ->assertJsonPath('data.created_by', $user->id);

        $this->assertDatabaseHas('performance_metrics', [
            'player_id' => $playerId,
            'metric_type' => 'physical',
            'metric_name' => 'endurance',
            'is_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
            'created_by' => $user->id,
        ]);
    }

    public function test_client_cannot_self_verify_recorded_metric(): void
    {
        $tenantId = $this->createTenant('spoof');
        $user = User::factory()->create([
            'role' => 'sports_scientist',
            'tenant_id' => $tenantId,
            'permissions' => null,
        ]);
        $playerId = $this->createPlayer($tenantId, 'spoof');

        $this->actingAs($user);

        $payload = array_merge($this->validPayload(), [
            'is_verified' => true,
            'verified_by' => $user->id,
            'verified_at' => now()->toISOString(),
        ]);

        $this->postJson($this->endpoint($playerId), $payload)
            ->assertCreated()
            ->assertJsonPath('data.is_verified', false);

        $this->assertDatabaseHas('performance_metrics', [
            'player_id' => $playerId,
            'is_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
        ]);
    }

    public function test_user_without_record_permission_is_forbidden(): void
    {
        $tenantId = $this->createTenant('forbidden');
        $user = User::factory()->create([
            'role' => 'player',
            'tenant_id' => $tenantId,
            'permissions' => null,
        ]);
        $playerId = $this->createPlayer($tenantId, 'forbidden');

        $this->actingAs($user);

        $this->postJson(
            $this->endpoint($playerId),
            $this->validPayload()
        )->assertForbidden();

        $this->assertDatabaseCount('performance_metrics', 0);
    }

    public function test_inaccessible_tenant_player_is_not_exposed(): void
    {
        $allowedTenantId = $this->createTenant('allowed');
        $otherTenantId = $this->createTenant('other');

        $user = User::factory()->create([
            'role' => 'sports_scientist',
            'tenant_id' => $allowedTenantId,
            'permissions' => null,
        ]);
        $playerId = $this->createPlayer($otherTenantId, 'other');

        $this->actingAs($user);

        $this->postJson(
            $this->endpoint($playerId),
            $this->validPayload()
        )->assertNotFound();

        $this->assertDatabaseCount('performance_metrics', 0);
    }

    public function test_non_fit_metric_is_rejected(): void
    {
        $tenantId = $this->createTenant('unsupported');
        $user = User::factory()->create([
            'role' => 'sports_scientist',
            'tenant_id' => $tenantId,
            'permissions' => null,
        ]);
        $playerId = $this->createPlayer($tenantId, 'unsupported');

        $this->actingAs($user);

        $payload = array_merge($this->validPayload(), [
            'metric_name' => 'Synthetic Readiness Score',
            'metric_unit' => 'score',
            'metric_value' => 8,
        ]);

        $this->postJson($this->endpoint($playerId), $payload)
            ->assertStatus(422);

        $this->assertDatabaseCount('performance_metrics', 0);
    }

    public function test_external_source_cannot_be_claimed_by_manual_entry(): void
    {
        $tenantId = $this->createTenant('external-source');
        $user = User::factory()->create([
            'role' => 'sports_scientist',
            'tenant_id' => $tenantId,
            'permissions' => null,
        ]);
        $playerId = $this->createPlayer(
            $tenantId,
            'external-source'
        );

        $this->actingAs($user);

        $payload = array_merge($this->validPayload(), [
            'data_source' => 'fifa_connect',
        ]);

        $this->postJson($this->endpoint($playerId), $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['data_source']);

        $this->assertDatabaseCount('performance_metrics', 0);
    }

    public function test_future_measurement_is_rejected(): void
    {
        $tenantId = $this->createTenant('future');
        $user = User::factory()->create([
            'role' => 'sports_scientist',
            'tenant_id' => $tenantId,
            'permissions' => null,
        ]);
        $playerId = $this->createPlayer($tenantId, 'future');

        $this->actingAs($user);

        $payload = array_merge($this->validPayload(), [
            'measurement_date' => now()->addMinute()->toISOString(),
        ]);

        $this->postJson($this->endpoint($playerId), $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['measurement_date']);

        $this->assertDatabaseCount('performance_metrics', 0);
    }

    public function test_social_score_without_explicit_scale_is_rejected(): void
    {
        $tenantId = $this->createTenant('social-no-scale');
        $user = User::factory()->create([
            'role' => 'sports_scientist',
            'tenant_id' => $tenantId,
            'permissions' => null,
        ]);
        $playerId = $this->createPlayer(
            $tenantId,
            'social-no-scale'
        );

        $this->actingAs($user);

        $payload = array_merge($this->validPayload(), [
            'metric_type' => 'social',
            'metric_name' => 'team_cohesion',
            'metric_value' => 4,
            'metric_unit' => 'score',
            'metadata' => null,
        ]);

        $this->postJson($this->endpoint($playerId), $payload)
            ->assertStatus(422);

        $this->assertDatabaseCount('performance_metrics', 0);
    }

    public function test_social_metric_requires_explicit_valid_scale(): void
    {
        $tenantId = $this->createTenant('social');
        $user = User::factory()->create([
            'role' => 'sports_scientist',
            'tenant_id' => $tenantId,
            'permissions' => null,
        ]);
        $playerId = $this->createPlayer($tenantId, 'social');

        $this->actingAs($user);

        $payload = array_merge($this->validPayload(), [
            'metric_type' => 'social',
            'metric_name' => 'team_cohesion',
            'metric_value' => 4,
            'metric_unit' => 'score',
            'metadata' => [
                'scale_min' => 1,
                'scale_max' => 5,
            ],
        ]);

        $this->postJson($this->endpoint($playerId), $payload)
            ->assertCreated()
            ->assertJsonPath('data.is_verified', false);
    }

    private function endpoint(int $playerId): string
    {
        return "/api/fit/players/{$playerId}/performance-metrics";
    }

    private function validPayload(): array
    {
        return [
            'metric_type' => 'physical',
            'metric_name' => 'endurance',
            'metric_value' => 82,
            'metric_unit' => '%',
            'measurement_date' => now()->subHour()->toISOString(),
            'data_source' => 'manual',
            'confidence_score' => 0.95,
        ];
    }

    private function createTenant(string $suffix): int
    {
        return DB::table('tenants')->insertGetId([
            'name' => "Tenant {$suffix}",
            'slug' => "fit-record-{$suffix}",
            'type' => 'organization',
            'status' => 'active',
            'timezone' => 'UTC',
            'language' => 'en',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createPlayer(int $tenantId, string $suffix): int
    {
        return DB::table('players')->insertGetId([
            'tenant_id' => $tenantId,
            'name' => "Player {$suffix}",
            'first_name' => 'FIT',
            'last_name' => $suffix,
            'fifa_connect_id' => "FIT-RECORD-{$suffix}",
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
