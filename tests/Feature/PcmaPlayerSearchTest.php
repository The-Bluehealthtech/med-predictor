<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Recherche de joueur du PCMA (saisie vocale) : authentifiée, limitée aux joueurs du médecin,
 * identité minimale. L'ancienne route publique /api/athletes/search n'existe plus, et le
 * formulaire ne contient plus de joueur de démonstration ni de test lancé au chargement.
 */
class PcmaPlayerSearchTest extends TestCase
{
    use DatabaseTransactions;

    public function test_search_is_scoped_to_the_doctor_players_and_returns_minimal_identity(): void
    {
        $assoc = DB::table('associations')->insertGetId(['name' => 'Fédération Recherche', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $club = DB::table('clubs')->insertGetId(['name' => 'Club A', 'association_id' => $assoc, 'created_at' => now(), 'updated_at' => now()]);
        $other = DB::table('clubs')->insertGetId(['name' => 'Club B', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('players')->insert([
            ['name' => 'Karim Mansour', 'first_name' => 'Karim', 'last_name' => 'Mansour', 'fifa_connect_id' => 'KAR123A', 'club_id' => $club, 'association_id' => $assoc, 'date_of_birth' => '2000-01-01', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Karim Autre', 'first_name' => 'Karim', 'last_name' => 'Autre', 'fifa_connect_id' => 'OTH123A', 'club_id' => $other, 'association_id' => null, 'date_of_birth' => '2001-01-01', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $doctor = User::factory()->create(['role' => 'club_medical', 'club_id' => $club, 'status' => 'active', 'tenant_id' => 1]);

        $this->getJson(route('pcma.players.search', ['fifa_id' => 'KAR123A']))->assertUnauthorized();

        $found = $this->actingAs($doctor)->getJson(route('pcma.players.search', ['fifa_id' => 'kar123a']))->assertOk()->json();
        $this->assertTrue($found['success']);
        $this->assertSame(['age', 'club', 'fifa_connect_id', 'id', 'name', 'nationality', 'position'], collect($found['player'])->keys()->sort()->values()->all());
        $this->assertSame(['Karim Mansour', 'Club A'], [$found['player']['name'], $found['player']['club']]);

        $this->assertFalse($this->actingAs($doctor)->getJson(route('pcma.players.search', ['fifa_id' => 'OTH123A']))->json('success'), 'joueur d’un autre club');
        $this->assertSame('Karim Mansour', $this->actingAs($doctor)->getJson(route('pcma.players.search', ['name' => 'karim']))->json('player.name'));
    }

    public function test_public_athlete_search_route_is_gone_and_no_demo_player_remains(): void
    {
        Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));
        app('router')->getRoutes()->refreshNameLookups();
        $this->assertNull(collect(app('router')->getRoutes())->first(fn ($r) => $r->uri() === 'api/athletes/search'));

        $view = file_get_contents(resource_path('views/pcma/create.blade.php'));
        foreach (['Ali Jebali', '/api/athletes/search', 'testFifaConnectCommand', 'window.testFifaFields'] as $removed) {
            $this->assertStringNotContainsString($removed, $view);
        }
    }
}
