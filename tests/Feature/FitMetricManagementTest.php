<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FitMetricManagementTest extends TestCase
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

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(
                ['notifiable_type', 'notifiable_id'],
                'notifications_notifiable_index'
            );
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
            $table->string('fifa_connect_id')->nullable();
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

    public function test_sports_scientist_can_open_canonical_fit_screen(): void
    {
        $tenantId = $this->createTenant('main');

        $user = $this->createUser(
            $tenantId,
            'sports_scientist',
            null
        );

        $playerId = $this->createPlayer(
            $tenantId,
            'Accessible',
            'Player'
        );

        $this->createMetric($playerId, $user->id, 'endurance', 82, '%');

        $this->actingAs($user);

        $this->get(
            route('performances.fit-metrics', [
                'player_id' => $playerId,
            ])
        )
            ->assertOk()
            ->assertSee('Métriques FIT canoniques')
            ->assertSee('endurance')
            ->assertSee('team_cohesion')
            ->assertSee('Accessible Player')
            ->assertSee('Acceptée')
            ->assertSee('En attente')
            ->assertSee('Vérification autorisée :')
            ->assertSee('oui')
            ->assertSee('id="fit-metric-recording-form"', false)
            ->assertSee('Enregistrer une métrique FIT')
            ->assertSee('Sélectionner un axe')
            ->assertSee('Sélectionner d\'abord un axe', false)
            ->assertSee('Niveau de confiance (0 à 1)')
            ->assertSee(
                json_encode(
                    route(
                        'api.fit.performance-metrics.store',
                        ['player' => $playerId],
                        false
                    )
                ),
                false
            );
    }

    public function test_player_without_record_permission_cannot_open_screen(): void
    {
        $tenantId = $this->createTenant('forbidden');

        $user = $this->createUser(
            $tenantId,
            'player',
            null
        );

        $this->actingAs($user);

        $this->get(route('performances.fit-metrics'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_unrelated_tenant_player_is_not_listed(): void
    {
        $allowedTenantId = $this->createTenant('allowed');
        $otherTenantId = $this->createTenant('other');

        $user = $this->createUser(
            $allowedTenantId,
            'sports_scientist',
            null
        );

        $this->createPlayer(
            $allowedTenantId,
            'Visible',
            'Athlete'
        );

        $this->createPlayer(
            $otherTenantId,
            'Hidden',
            'Athlete'
        );

        $this->actingAs($user);

        $this->get(route('performances.fit-metrics'))
            ->assertOk()
            ->assertSee('Visible Athlete')
            ->assertDontSee('Hidden Athlete');
    }

    public function test_unrelated_tenant_player_cannot_be_selected(): void
    {
        $allowedTenantId = $this->createTenant('selection-allowed');
        $otherTenantId = $this->createTenant('selection-other');

        $user = $this->createUser(
            $allowedTenantId,
            'sports_scientist',
            null
        );

        $hiddenPlayerId = $this->createPlayer(
            $otherTenantId,
            'Forbidden',
            'Selection'
        );

        $this->actingAs($user);

        $this->get(
            route('performances.fit-metrics', [
                'player_id' => $hiddenPlayerId,
            ])
        )->assertNotFound();
    }

    private function createTenant(string $suffix): int
    {
        return DB::table('tenants')->insertGetId([
            'name' => "Tenant {$suffix}",
            'slug' => "fit-ui-{$suffix}",
            'type' => 'organization',
            'status' => 'active',
            'timezone' => 'UTC',
            'language' => 'en',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createUser(
        int $tenantId,
        string $role,
        ?array $permissions
    ): User {
        DB::table('users')->insert([
            'name' => "User {$role}",
            'email' => "{$role}-{$tenantId}@example.test",
            'password' => bcrypt('password'),
            'role' => $role,
            'permissions' => $permissions
                ? json_encode($permissions)
                : null,
            'tenant_id' => $tenantId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::query()
            ->where('email', "{$role}-{$tenantId}@example.test")
            ->firstOrFail();
    }

    private function createPlayer(
        int $tenantId,
        string $firstName,
        string $lastName
    ): int {
        return DB::table('players')->insertGetId([
            'tenant_id' => $tenantId,
            'name' => "{$firstName} {$lastName}",
            'first_name' => $firstName,
            'last_name' => $lastName,
            'fifa_connect_id' => null,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createMetric(
        int $playerId,
        int $userId,
        string $name,
        float $value,
        string $unit
    ): int {
        return DB::table('performance_metrics')->insertGetId([
            'player_id' => $playerId,
            'metric_type' => 'physical',
            'metric_name' => $name,
            'metric_value' => $value,
            'metric_unit' => $unit,
            'measurement_date' => now()->subHour(),
            'data_source' => 'manual',
            'confidence_score' => 0.95,
            'is_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
            'metadata' => null,
            'created_by' => $userId,
            'updated_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
