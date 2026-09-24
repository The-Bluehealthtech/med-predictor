<?php

namespace Tests\Feature\Api;

use App\Models\PerformanceMetric;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceMetricVerificationTest extends TestCase
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
        Cache::flush();
        DB::purge('sqlite');

        parent::tearDown();
    }

    public function test_system_admin_can_verify_fit_metric(): void
    {
        $tenantId = $this->createTenant('system-admin');
        $user = User::factory()->create([
            'role' => 'system_admin',
        ]);
        $playerId = $this->createPlayer($tenantId, 'system-admin');
        $metricId = $this->createMetric($playerId, $user->id);

        $this->actingAs($user);

        $this->postJson($this->endpoint($playerId, $metricId))
            ->assertOk()
            ->assertJsonPath('data.is_verified', true)
            ->assertJsonPath('data.verified_by', $user->id)
            ->assertJsonPath('meta.already_verified', false);

        $this->assertDatabaseHas('performance_metrics', [
            'id' => $metricId,
            'is_verified' => true,
            'verified_by' => $user->id,
        ]);
    }

    public function test_sports_scientist_can_verify_metric_in_accessible_tenant(): void
    {
        $tenantId = $this->createTenant('sports-scientist');
        $user = User::factory()->create([
            'role' => 'sports_scientist',
            'tenant_id' => $tenantId,
            'permissions' => null,
        ]);
        $playerId = $this->createPlayer($tenantId, 'sports-scientist');
        $metricId = $this->createMetric($playerId, $user->id);

        $this->actingAs($user);

        $this->postJson($this->endpoint($playerId, $metricId))
            ->assertOk()
            ->assertJsonPath('data.is_verified', true)
            ->assertJsonPath('data.verified_by', $user->id);
    }

    public function test_user_without_verification_permission_is_forbidden(): void
    {
        $tenantId = $this->createTenant('forbidden');
        $user = User::factory()->create([
            'role' => 'player',
            'tenant_id' => $tenantId,
            'permissions' => null,
        ]);
        $playerId = $this->createPlayer($tenantId, 'forbidden');
        $metricId = $this->createMetric($playerId, $user->id);

        $this->actingAs($user);

        $this->postJson($this->endpoint($playerId, $metricId))
            ->assertForbidden();

        $this->assertDatabaseHas('performance_metrics', [
            'id' => $metricId,
            'is_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
        ]);
    }

    public function test_metric_from_inaccessible_tenant_is_not_exposed(): void
    {
        $allowedTenantId = $this->createTenant('allowed');
        $otherTenantId = $this->createTenant('other');

        $user = User::factory()->create([
            'role' => 'sports_scientist',
            'tenant_id' => $allowedTenantId,
            'permissions' => null,
        ]);

        $playerId = $this->createPlayer($otherTenantId, 'other');
        $metricId = $this->createMetric($playerId, $user->id);

        $this->actingAs($user);

        $this->postJson($this->endpoint($playerId, $metricId))
            ->assertNotFound();

        $this->assertDatabaseHas('performance_metrics', [
            'id' => $metricId,
            'is_verified' => false,
        ]);
    }

    public function test_non_fit_metric_cannot_be_verified_by_fit_endpoint(): void
    {
        $tenantId = $this->createTenant('unsupported');

        $user = User::factory()->create([
            'role' => 'sports_scientist',
            'tenant_id' => $tenantId,
            'permissions' => null,
        ]);

        $playerId = $this->createPlayer($tenantId, 'unsupported');
        $metricId = $this->createMetric(
            $playerId,
            $user->id,
            'Synthetic Readiness Score',
            'score',
            8
        );

        $this->actingAs($user);

        $this->postJson($this->endpoint($playerId, $metricId))
            ->assertStatus(422);

        $this->assertDatabaseHas('performance_metrics', [
            'id' => $metricId,
            'is_verified' => false,
            'verified_by' => null,
        ]);
    }

    public function test_reverification_preserves_original_verifier_and_timestamp(): void
    {
        Carbon::setTestNow('2026-09-24 10:00:00');

        $tenantId = $this->createTenant('idempotent');

        $firstVerifier = User::factory()->create([
            'role' => 'sports_scientist',
            'tenant_id' => $tenantId,
            'permissions' => null,
        ]);

        $secondVerifier = User::factory()->create([
            'role' => 'system_admin',
        ]);

        $playerId = $this->createPlayer($tenantId, 'idempotent');
        $metricId = $this->createMetric(
            $playerId,
            $firstVerifier->id
        );

        $this->actingAs($firstVerifier);

        $this->postJson($this->endpoint($playerId, $metricId))
            ->assertOk()
            ->assertJsonPath('meta.already_verified', false);

        $firstState = PerformanceMetric::query()->findOrFail($metricId);
        $verifiedAt = $firstState->verified_at->toDateTimeString();

        Carbon::setTestNow('2026-09-24 11:00:00');
        $this->actingAs($secondVerifier);

        $this->postJson($this->endpoint($playerId, $metricId))
            ->assertOk()
            ->assertJsonPath('meta.already_verified', true);

        $finalState = PerformanceMetric::query()->findOrFail($metricId);

        $this->assertSame(
            $firstVerifier->id,
            $finalState->verified_by
        );
        $this->assertSame(
            $verifiedAt,
            $finalState->verified_at->toDateTimeString()
        );

        Carbon::setTestNow();
    }

    private function endpoint(int $playerId, int $metricId): string
    {
        return "/api/fit/players/{$playerId}/performance-metrics/{$metricId}/verify";
    }

    private function createTenant(string $suffix): int
    {
        return DB::table('tenants')->insertGetId([
            'name' => "Tenant {$suffix}",
            'slug' => "fit-test-{$suffix}",
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
            'fifa_connect_id' => "FIT-{$suffix}",
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createMetric(
        int $playerId,
        int $createdBy,
        string $name = 'endurance',
        string $unit = '%',
        float $value = 82
    ): int {
        return DB::table('performance_metrics')->insertGetId([
            'player_id' => $playerId,
            'metric_type' => 'physical',
            'metric_name' => $name,
            'metric_value' => $value,
            'metric_unit' => $unit,
            'measurement_date' => now(),
            'data_source' => 'manual',
            'confidence_score' => 0.95,
            'is_verified' => false,
            'created_by' => $createdBy,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
