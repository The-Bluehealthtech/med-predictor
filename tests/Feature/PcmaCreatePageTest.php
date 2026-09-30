<?php
namespace Tests\Feature;

use App\Http\Controllers\PCMAController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class PcmaCreatePageTest extends TestCase
{
    private string $previousConnection;
    protected function setUp(): void
    {
        parent::setUp();
        // Tester les routes réelles : le bootstrap de test utilise sinon routes/testing.php.
        Route::middleware('web')->group(base_path('routes/web.php'));
        Route::getRoutes()->refreshNameLookups();
        $this->previousConnection = DB::getDefaultConnection();
        config()->set('database.connections.pcma_create_test', ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
        DB::setDefaultConnection('pcma_create_test');
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); $table->string('type'); $table->morphs('notifiable');
            $table->text('data'); $table->timestamp('read_at')->nullable(); $table->timestamps();
        });
        Schema::create('players', function (Blueprint $table) {
            $table->id(); $table->string('first_name'); $table->string('last_name');
            $table->string('name')->nullable(); $table->unsignedBigInteger('club_id')->nullable();
        });
    }
    protected function tearDown(): void
    {
        DB::purge('pcma_create_test');
        DB::setDefaultConnection($this->previousConnection);
        parent::tearDown();
    }
    public function test_create_route_renders_in_french_and_english_without_teamdoctor(): void
    {
        self::assertSame(PCMAController::class.'@create', Route::getRoutes()->match(\Illuminate\Http\Request::create('/pcma/create'))->getActionName());
        $user = new User(['role' => 'system_admin']);
        $user->forceFill(['id' => 9001]);
        $this->withoutExceptionHandling();
        foreach (['fr', 'en'] as $locale) {
            app()->setLocale($locale);
            $this->withoutMiddleware()->actingAs($user)->get('/pcma/create')
                ->assertOk()->assertViewIs('pcma.create')
                ->assertViewHas('teamDoctorRegistration', null)
                ->assertViewHas('athletes', fn ($rows) => $rows->isEmpty())
                ->assertViewHas('users', fn ($rows) => $rows->isEmpty())
                ->assertDontSee('Test Assessor')->assertDontSee('Test Player');
        }
    }
    public function test_system_admin_receives_persisted_players(): void
    {
        DB::table('players')->insert(['id' => 10, 'first_name' => 'Fixture', 'last_name' => 'Only', 'name' => 'Fixture Only', 'club_id' => 1]);
        $this->actingAs(new User(['role' => 'system_admin']));
        $view = app(PCMAController::class)->create();
        self::assertSame([10], $view->getData()['athletes']->pluck('id')->all());
        self::assertArrayHasKey('teamDoctorRegistration', $view->getData());
    }
    public function test_club_medical_sees_only_its_persisted_players(): void
    {
        DB::table('players')->insert([
            ['id' => 10, 'first_name' => 'Fixture', 'last_name' => 'Own', 'club_id' => 1],
            ['id' => 11, 'first_name' => 'Fixture', 'last_name' => 'Other', 'club_id' => 2],
        ]);
        $this->actingAs(new User(['role' => 'club_medical', 'club_id' => 1]));
        $view = app(PCMAController::class)->create();
        self::assertSame([10], $view->getData()['athletes']->pluck('id')->all());
        self::assertNull($view->getData()['teamDoctorRegistration']);
        self::assertTrue($view->getData()['users']->isEmpty());
    }
}

