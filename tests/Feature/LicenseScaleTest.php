<?php

namespace Tests\Feature;

use App\Http\Controllers\Licensing\LicenseScaleController;
use App\Http\Controllers\PlayerLicenseWorkflowController;
use App\Models\Player;
use App\Models\PlayerLicense;
use App\Models\User;
use App\Services\Licensing\LicenseScale;
use App\Services\Licensing\PcmaRequirement;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Barème des licences paramétrable par la fédération, structuré comme
 * l'enregistrement FIFA Connect : genre, discipline, puis catégories d'âge
 * (niveaux autorisés, tarifs, PCMA, pièces) ; tarifs des officiels ; saison.
 */
class LicenseScaleTest extends TestCase
{
    use DatabaseTransactions;

    private int $associationId;

    private int $clubId;

    protected function setUp(): void
    {
        parent::setUp();
        Route::middleware(['web', 'auth'])->group(function () {
            Route::get('/_t/scale', [LicenseScaleController::class, 'index'])->name('licenses.scale');
            Route::post('/_t/scale', [LicenseScaleController::class, 'update'])->name('licenses.scale.update');
            Route::get('/_t/licenses', [PlayerLicenseWorkflowController::class, 'index'])->name('modules.licenses.index');
            Route::get('/_t/licenses/players/{player}/request', [PlayerLicenseWorkflowController::class, 'create'])->name('player-licenses.request.create');
            Route::post('/_t/licenses/players/{player}/request', [PlayerLicenseWorkflowController::class, 'store'])->name('player-licenses.request.store');
            Route::get('/_t/licenses/requests/{license}', [PlayerLicenseWorkflowController::class, 'show'])->name('player-licenses.show');
            Route::get('/_t/approval', fn () => 'ok')->name('licenses.validation');
            Route::get('/_t/approval/{license}', fn () => 'ok')->name('licenses.review');
        });
        app('router')->getRoutes()->refreshNameLookups();

        $this->associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération Barème Test', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $this->clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club Barème Test', 'association_id' => $this->associationId, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function player(?string $dob, ?string $gender = 'male'): Player
    {
        $id = DB::table('players')->insertGetId(['name' => 'Joueur Barème', 'first_name' => 'Joueur', 'last_name' => 'Barème', 'date_of_birth' => $dob, 'gender' => $gender,
            'club_id' => $this->clubId, 'association_id' => $this->associationId, 'created_at' => now(), 'updated_at' => now()]);

        return Player::query()->findOrFail($id);
    }

    private function user(string $role, array $extra = []): User
    {
        return User::factory()->create(array_merge(['role' => $role, 'status' => 'active', 'tenant_id' => 1], $extra));
    }

    private function federation(): User
    {
        return $this->user('association_admin', ['association_id' => $this->associationId]);
    }

    private function club(): User
    {
        return $this->user('club_admin', ['club_id' => $this->clubId]);
    }

    private function season(): string
    {
        $scale = app(LicenseScale::class);

        return $scale->season($scale->settings($this->associationId))['label'];
    }

    /** Barème par défaut d'un genre et d'une discipline, envoyé comme le formulaire. */
    private function scalePayload(string $gender, string $discipline, array $overrides = [], array $settings = []): array
    {
        $categories = collect(config('licensing.default_scale.categories'))->map(fn ($c) => $c + ['fees' => []])->values()->all();
        foreach ($overrides as $index => $values) {
            $categories[$index] = array_merge($categories[$index], $values);
        }

        return array_merge(['association_id' => $this->associationId, 'gender' => $gender, 'discipline' => $discipline, 'currency' => 'TND',
            'season_start_month' => 7, 'season_start_day' => 1, 'reference_month' => 1, 'reference_day' => 1,
            'official_fees' => ['TeamOfficial' => '40', 'OrganisationOfficial' => '25'], 'categories' => $categories], $settings);
    }

    public function test_scale_is_set_per_gender_and_discipline_and_snapshotted_on_the_request(): void
    {
        $federation = $this->federation();
        $this->actingAs($federation)->get('/_t/scale')->assertOk()->assertSee('Hommes')->assertSee('Femmes')->assertSee('Football (à 11)')->assertSee('Futsal')->assertSee('Beach soccer')
            ->assertSee("ne figurent pas dans l'enregistrement FIFA Connect", false);

        // Tarifs différents : femmes futsal senior amateur 20, hommes football senior amateur 50.
        $this->actingAs($federation)->post('/_t/scale', $this->scalePayload('female', 'Futsal', [4 => ['fees' => ['amateur' => '20']]]))->assertSessionHasNoErrors();
        $this->actingAs($federation)->post('/_t/scale', $this->scalePayload('male', 'Football', [4 => ['fees' => ['amateur' => '50', 'pro' => '400']]]))->assertSessionHasNoErrors();

        $woman = $this->player(now()->subYears(25)->toDateString(), 'female');
        $man = $this->player(now()->subYears(25)->toDateString(), 'male');
        $scale = app(LicenseScale::class);
        $this->assertSame(20.0, $scale->playerRules($woman, 'Futsal', 'amateur')['fee']);
        $this->assertNull($scale->playerRules($woman, 'Football', 'amateur')['fee'], 'femmes football : barème par défaut, sans tarif');
        $this->assertSame(50.0, $scale->playerRules($man, 'Football', 'amateur')['fee']);

        $this->actingAs($this->club())->post("/_t/licenses/players/{$man->id}/request", [
            'discipline' => 'Football', 'level' => 'amateur', 'request_reason' => 'first', 'season' => $this->season(),
            'documents' => collect(['identity', 'photo', 'medical'])->mapWithKeys(fn ($t) => [$t => UploadedFile::fake()->create("{$t}.pdf", 20, 'application/pdf')])->all(),
        ])->assertSessionHasNoErrors()->assertRedirect();
        $license = PlayerLicense::query()->where('player_id', $man->id)->firstOrFail();
        $this->assertSame(['Player', 'Football', 'amateur', 'Registration', 'male', 'SENIOR'], [$license->registration_type, $license->discipline, $license->level, $license->registration_nature, $license->gender, $license->age_category]);
        $this->assertEquals(50, $license->fee_amount);
        $this->assertSame('TND', $license->fee_currency);
    }

    public function test_level_not_allowed_in_the_category_and_gender_required(): void
    {
        $young = $this->player(now()->subYears(13)->toDateString());
        $this->actingAs($this->club())->get("/_t/licenses/players/{$young->id}/request")->assertOk()->assertSee('Licence de joueur');
        $this->actingAs($this->club())->post("/_t/licenses/players/{$young->id}/request", [
            'discipline' => 'Football', 'level' => 'pro', 'request_reason' => 'first', 'season' => $this->season(),
        ])->assertSessionHas('error', fn ($m) => str_contains($m, 'non autorisé en catégorie U-15'));

        $unknown = $this->player(now()->subYears(25)->toDateString(), null);
        $this->actingAs($this->club())->post("/_t/licenses/players/{$unknown->id}/request", [
            'discipline' => 'Football', 'level' => 'amateur', 'request_reason' => 'first', 'season' => $this->season(),
        ])->assertSessionHasErrors('gender');
    }

    public function test_seasons_reference_date_and_pcma_by_category(): void
    {
        $scale = app(LicenseScale::class);
        $settings = $scale->settings($this->associationId);
        $season = $scale->season($settings, now()->setDate(2026, 10, 2));
        $this->assertSame('2026-2027', $season['label']);
        $this->assertSame('2027-06-30', $season['end']->toDateString(), 'une licence vaut au plus une saison (juillet → juin)');
        $this->assertSame('2027-01-01', $scale->referenceDate($settings, $season)->toDateString(), 'âge au 1er janvier compris dans la saison');

        $player = $this->player('2012-06-15'); // 14 ans au 01/01/2027 : U-15
        $this->assertSame('U15', $scale->playerRules($player, 'Football', 'amateur', 'Registration', $season)['category']);
        $player = $this->player('2011-12-15'); // 15 ans au 01/01/2027 : U-17
        $this->assertSame('U17', $scale->playerRules($player, 'Football', 'amateur', 'Registration', $season)['category']);

        // La fédération exige le PCMA pour tous les joueurs U-21 en futsal masculin.
        $this->actingAs($this->federation())->post('/_t/scale', $this->scalePayload('male', 'Futsal', [3 => ['pcma_rule' => 'all']]))->assertSessionHasNoErrors();
        $twenty = $this->player(now()->subYears(20)->subMonths(2)->toDateString());
        $this->assertTrue(app(PcmaRequirement::class)->requirement($twenty, 'Futsal', 'amateur')['required']);
        $this->assertFalse(app(PcmaRequirement::class)->requirement($twenty, 'Football', 'amateur')['required'], 'football : règle par défaut (PCMA pour les pros)');
    }

    public function test_scale_validation_and_access(): void
    {
        $this->actingAs($this->federation())->post('/_t/scale', $this->scalePayload('male', 'Football', [0 => ['max_age' => null]]))->assertSessionHasErrors('categories');
        $this->actingAs($this->federation())->post('/_t/scale', $this->scalePayload('male', 'Football', [1 => ['code' => 'U15']]))->assertSessionHasErrors('categories');
        $this->actingAs($this->federation())->post('/_t/scale', $this->scalePayload('male', 'Rugby'))->assertSessionHasErrors('discipline');

        $this->actingAs($this->club())->get('/_t/scale')->assertForbidden();
        $this->actingAs($this->club())->post('/_t/scale', $this->scalePayload('male', 'Football'))->assertForbidden();
    }
}
