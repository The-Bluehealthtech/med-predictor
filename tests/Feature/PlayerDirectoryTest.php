<?php

namespace Tests\Feature;

use App\Http\Controllers\PlayerDirectoryController;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Annuaire des joueurs : barre de filtres, périmètre par compte et actions
 * réellement disponibles (plus de liens morts ni d'icônes sans libellé).
 */
class PlayerDirectoryTest extends TestCase
{
    use DatabaseTransactions;

    private int $clubA;

    private int $clubB;

    protected function setUp(): void
    {
        parent::setUp();
        Route::middleware(['web', 'auth'])->get('/_test/modules/players', [PlayerDirectoryController::class, 'index'])->name('modules.players.index');
        foreach (['players.show' => '/_test/players/{player}', 'players.edit' => '/_test/players/{player}/edit', 'player-registration.create' => '/_test/register',
            'player-licenses.request.create' => '/_test/players/{player}/license', 'players.health-records' => '/_test/players/{player}/health'] as $name => $uri) {
            if (!Route::has($name)) {
                Route::get($uri, fn () => 'ok')->name($name);
            }
        }
        app('router')->getRoutes()->refreshNameLookups();

        $this->clubA = (int) DB::table('clubs')->insertGetId(['name' => 'Annuaire Club A', 'created_at' => now(), 'updated_at' => now()]);
        $this->clubB = (int) DB::table('clubs')->insertGetId(['name' => 'Annuaire Club B', 'created_at' => now(), 'updated_at' => now()]);
        foreach ([
            ['Zinedine', 'Annuairetest', 'MID', 'France', $this->clubA],
            ['Yassine', 'Annuairetest', 'GK', 'Tunisia', $this->clubA],
            ['Karim', 'Annuairetest', 'LCB', 'Tunisia', $this->clubB],
            ['Omar', 'Annuairetest', 'CF', 'Saudi Arabia', $this->clubB],
        ] as [$first, $last, $position, $nationality, $club]) {
            DB::table('players')->insert(['name' => "$first $last", 'first_name' => $first, 'last_name' => $last, 'position' => $position,
                'nationality' => $nationality, 'club_id' => $club, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    private function user(string $role, array $extra = []): User
    {
        return User::factory()->create(array_merge(['role' => $role, 'status' => 'active', 'tenant_id' => 1], $extra));
    }

    private function names($response): array
    {
        return $response->viewData('players')->getCollection()->pluck('first_name')->sort()->values()->all();
    }

    public function test_search_and_filters_combine(): void
    {
        $this->actingAs($this->user('system_admin'));

        $this->assertSame(['Karim', 'Omar', 'Yassine', 'Zinedine'], $this->names($this->get('/_test/modules/players?q=annuairetest')->assertOk()));
        $this->assertSame(['Karim', 'Omar'], $this->names($this->get('/_test/modules/players?q=annuairetest&club_id=' . $this->clubB)));
        $this->assertSame(['Karim'], $this->names($this->get('/_test/modules/players?q=annuairetest&line=DEF')), 'poste détaillé LCB rattaché aux défenseurs');
        $this->assertSame(['Omar'], $this->names($this->get('/_test/modules/players?q=annuairetest&line=FWD')));
        $this->assertSame(['Karim', 'Yassine'], $this->names($this->get('/_test/modules/players?q=annuairetest&nationality=Tunisia')));

        $player = DB::table('players')->where('first_name', 'Yassine')->where('last_name', 'Annuairetest')->value('id');
        // La dernière licence compte : une ancienne licence expirée puis une licence active.
        DB::table('player_licenses')->insert(['player_id' => $player, 'status' => 'expired', 'created_at' => now()->subYear(), 'updated_at' => now()->subYear()]);
        DB::table('player_licenses')->insert(['player_id' => $player, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        $response = $this->get('/_test/modules/players?q=annuairetest&license=active');
        $this->assertSame(['Yassine'], $this->names($response));
        $this->assertSame([], $this->names($this->get('/_test/modules/players?q=annuairetest&license=expired')), 'licence expirée remplacée par une active');
        $counts = $response->viewData('licenseCounts');
        $this->assertSame(4, $counts['all']);
        $this->assertSame(1, $counts['active']);
        $this->assertSame(3, $counts['none']);
        $response->assertSee('Retirer le filtre')->assertSee('Réinitialiser');
    }

    public function test_club_account_only_sees_its_club(): void
    {
        $this->actingAs($this->user('club_admin', ['club_id' => $this->clubA]));
        $response = $this->get('/_test/modules/players?q=annuairetest')->assertOk();

        $this->assertSame(['Yassine', 'Zinedine'], $this->names($response));
        $this->assertSame([$this->clubA], $response->viewData('clubs')->pluck('id')->all(), 'le filtre club ne propose que son club');
    }

    public function test_actions_are_real_labelled_and_role_aware(): void
    {
        $this->actingAs($this->user('club_admin', ['club_id' => $this->clubA]));
        $response = $this->get('/_test/modules/players?q=zinedine')->assertOk();
        $actions = collect($response->viewData('actions'))->first();
        $labels = array_column($actions, 'label');

        $this->assertContains('Modifier la fiche', $labels);
        $this->assertContains('Demander une licence', $labels);
        $this->assertNotContains('Dossier médical', $labels, 'compte non médical : pas d\'accès au dossier');
        foreach ($actions as $action) {
            $this->assertNotSame('#', $action['url']);
        }
        // (le lien « #» restant vient des notifications du layout, pas de la liste)
        $response->assertDontSee('🛂')->assertDontSee('✏️')->assertSee('Fiche')->assertSee('Autres actions pour Zinedine');

        $this->actingAs($this->user('club_medical', ['club_id' => $this->clubA]));
        $labels = array_column(collect($this->get('/_test/modules/players?q=zinedine')->viewData('actions'))->first(), 'label');
        $this->assertContains('Dossier médical', $labels);
        $this->assertNotContains('Modifier la fiche', $labels);
    }
}
