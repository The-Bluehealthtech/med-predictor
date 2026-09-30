<?php
namespace Tests\Feature;

use App\Http\Controllers\FormationRatesController;
use App\Http\Controllers\PlayerPortalAccountController;
use App\Models\Club;
use App\Models\Player;
use App\Models\User;
use App\Services\MedicalRecordAccess;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

final class RepairedRouteActionsTest extends TestCase
{
    public function test_pcma_transitions_persist_and_pdf_escapes_input(): void
    {
        $previous = DB::getDefaultConnection();
        config()->set('database.connections.pcma_route_test', ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
        DB::setDefaultConnection('pcma_route_test');
        try {
            Schema::create('pcmas', function (Blueprint $table) {
                $table->id(); $table->string('status'); $table->dateTime('completed_at')->nullable(); $table->timestamps();
            });
            DB::table('pcmas')->insert(['id' => 1, 'status' => 'pending']);
            $pcma = \App\Models\PCMA::findOrFail(1);
            $pcma->setRelation('player', null)->setRelation('athlete', null);
            $request = Request::create('/api/v1/pcmas/1/complete', 'POST');
            $request->setUserResolver(fn () => new User(['role' => 'system_admin']));
            $controller = new \App\Http\Controllers\PcmaStatusController();
            self::assertSame(200, $controller->complete($request, $pcma)->getStatusCode());
            self::assertSame('completed', DB::table('pcmas')->value('status'));
            self::assertSame(200, $controller->fail($request, $pcma)->getStatusCode());
            self::assertSame('failed', DB::table('pcmas')->value('status'));
            $pcma->setRelation('assessor', new User(['name' => 'Fixture']));
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pcma.pdf', [
                'pcma' => $pcma, 'athlete' => null, 'formData' => ['notes' => '<script>alert(1)</script>'],
                'generatedAt' => now(), 'isDraft' => true,
            ])->output();
            self::assertStringStartsWith('%PDF', $pdf);
            $html = view('pcma.pdf', ['pcma' => $pcma, 'athlete' => null,
                'formData' => ['notes' => '<script>alert(1)</script>'], 'generatedAt' => now(), 'isDraft' => true])->render();
            self::assertStringNotContainsString('<script>', $html);
            self::assertStringContainsString('&lt;script&gt;', $html);
        } finally {
            DB::purge('pcma_route_test'); DB::setDefaultConnection($previous);
        }
    }

    public function test_all_registered_controller_actions_exist(): void
    {
        // Charger aussi les routes web actives, absentes du bootstrap de test léger.
        Route::middleware('web')->group(base_path('routes/web.php'));
        foreach (Route::getRoutes() as $route) {
            $action = $route->getActionName();
            if (!str_contains($action, '@')) continue;
            [$class, $method] = explode('@', $action, 2);
            self::assertTrue(method_exists($class, $method), $route->uri().' '.$action);
        }
    }

    public function test_official_rates_are_unavailable_without_fabricating_amounts(): void
    {
        $response = (new FormationRatesController())->index();
        self::assertSame(503, $response->getStatusCode());
        self::assertSame([], $response->getData(true)['data']);
    }

    public function test_missing_account_player_is_denied_before_database_lookup(): void
    {
        $request = Request::create('/player-portal/profile');
        $request->setUserResolver(fn () => new User(['role' => 'player']));
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(0);
        (new PlayerPortalAccountController())->profile($request);
    }

    public function test_medical_record_of_another_club_is_forbidden(): void
    {
        $player = new Player();
        $club = new Club();
        $club->forceFill(['id' => 2, 'association_id' => 5]);
        $player->setRelation('club', $club);
        $user = new User(['role' => 'club_medical', 'club_id' => 1]);
        try {
            (new MedicalRecordAccess())->authorize($user, $player, null);
            self::fail('Accès interclub interdit attendu');
        } catch (HttpException $exception) {
            self::assertSame(403, $exception->getStatusCode());
        }
        $user->club_id = 2;
        (new MedicalRecordAccess())->authorize($user, $player, null);
        self::assertTrue(true);
    }

    public function test_contact_update_uses_account_identity_and_preserves_other_player(): void
    {
        $previous = DB::getDefaultConnection();
        config()->set('database.connections.route_test', ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
        DB::setDefaultConnection('route_test');
        try {
            Schema::create('players', function (Blueprint $table) {
                $table->id(); $table->string('name');
                $table->string('address')->nullable(); $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable(); $table->timestamps();
            });
            DB::table('players')->insert([['id' => 1, 'name' => 'Fixture A'], ['id' => 2, 'name' => 'Fixture B']]);
            $request = Request::create('/player-portal/profile', 'PUT', ['player_id' => 2, 'name' => 'Altered', 'role' => 'system_admin', 'contact_email' => 'fixture@example.test']);
            $request->headers->set('Accept', 'application/json');
            $request->setUserResolver(fn () => new User(['role' => 'player', 'player_id' => 1]));
            $response = (new PlayerPortalAccountController())->updateProfile($request);
            self::assertSame(200, $response->getStatusCode());
            self::assertSame('fixture@example.test', DB::table('players')->where('id', 1)->value('contact_email'));
            self::assertNull(DB::table('players')->where('id', 2)->value('contact_email'));
            self::assertSame('Fixture A', DB::table('players')->where('id', 1)->value('name'));
        } finally {
            DB::purge('route_test'); DB::setDefaultConnection($previous);
        }
    }
}
