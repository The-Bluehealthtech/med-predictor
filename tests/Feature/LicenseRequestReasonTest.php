<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\PlayerLicense;
use App\Models\User;
use App\Services\Licensing\LicenseScale;
use App\Services\Licensing\LicenseWorkflow;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Motif de la demande et traduction FIFA Connect : première licence,
 * renouvellement, transfert (clôture du club précédent), prêt (nature Loan),
 * retour de prêt (clôture du prêt), changement de niveau.
 */
class LicenseRequestReasonTest extends TestCase
{
    use DatabaseTransactions;

    private int $associationId;

    private int $clubA;

    private int $clubB;

    private LicenseWorkflow $workflow;

    protected function setUp(): void
    {
        parent::setUp();
        Route::get('/_t/approval/{license}', fn () => 'ok')->name('licenses.review');
        Route::get('/_t/licenses', fn () => 'ok')->name('modules.licenses.index');
        app('router')->getRoutes()->refreshNameLookups();

        $this->associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération Motifs', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $this->clubA = (int) DB::table('clubs')->insertGetId(['name' => 'Club A', 'association_id' => $this->associationId, 'created_at' => now(), 'updated_at' => now()]);
        $this->clubB = (int) DB::table('clubs')->insertGetId(['name' => 'Club B', 'association_id' => $this->associationId, 'created_at' => now(), 'updated_at' => now()]);
        $this->workflow = app(LicenseWorkflow::class);
    }

    /** Joueur de 16 ans (U-17) : amateur sans PCMA, professionnel autorisé. */
    private function player(int $clubId): Player
    {
        $id = DB::table('players')->insertGetId(['name' => 'Motif Joueur', 'first_name' => 'Motif', 'last_name' => 'Joueur', 'gender' => 'male',
            'date_of_birth' => now()->subYears(16)->toDateString(), 'club_id' => $clubId, 'association_id' => $this->associationId, 'created_at' => now(), 'updated_at' => now()]);

        return Player::query()->findOrFail($id);
    }

    private function season(): string
    {
        $scale = app(LicenseScale::class);

        return $scale->season($scale->settings($this->associationId))['label'];
    }

    private function licence(Player $player, int $clubId, array $values = []): PlayerLicense
    {
        return PlayerLicense::query()->create(array_merge(['player_id' => $player->id, 'club_id' => $clubId, 'registration_type' => 'Player', 'discipline' => 'Football',
            'level' => 'amateur', 'registration_nature' => 'Registration', 'season' => $this->season(), 'status' => 'active',
            'contract_start_date' => now()->subMonths(2)->toDateString(), 'expiry_date' => now()->addMonths(3)->toDateString()], $values));
    }

    private function submit(Player $player, string $reason, string $level = 'amateur'): PlayerLicense
    {
        $club = User::factory()->create(['role' => 'club_admin', 'club_id' => $player->club_id, 'status' => 'active', 'tenant_id' => 1]);
        $files = collect(['identity', 'photo', 'medical', 'parental', 'contract', 'loan_agreement'])->mapWithKeys(fn ($t) => [$t => UploadedFile::fake()->create("{$t}.pdf", 10, 'application/pdf')])->all();

        return $this->workflow->submitPlayer($player, $club, ['discipline' => 'Football', 'level' => $level, 'request_reason' => $reason, 'season' => $this->season()], $files);
    }

    private function approve(PlayerLicense $license): void
    {
        $federation = User::factory()->create(['role' => 'association_admin', 'association_id' => $this->associationId, 'status' => 'active', 'tenant_id' => 1]);
        $this->workflow->decide($license->fresh(), $federation, 'approve');
    }

    public function test_first_licence_then_renewal(): void
    {
        $player = $this->player($this->clubA);
        $reasons = $this->workflow->reasonsFor($player, 'Football', 'amateur');
        $this->assertTrue($reasons['first']['allowed']);
        $this->assertFalse($reasons['renewal']['allowed']);
        $this->assertFalse($reasons['transfer']['allowed']);

        $this->licence($player, $this->clubA, ['status' => 'expired', 'season' => '2020-2021']);
        $reasons = $this->workflow->reasonsFor($player, 'Football', 'amateur');
        $this->assertFalse($reasons['first']['allowed'], 'déjà enregistré');
        $this->assertTrue($reasons['renewal']['allowed']);

        $license = $this->submit($player, 'renewal');
        $this->assertSame(['renewal', 'Registration'], [$license->request_reason, $license->registration_nature]);
        $this->expectException(InvalidArgumentException::class);
        $this->submit($this->player($this->clubA)->fresh(), 'renewal'); // aucun historique : motif refusé
    }

    public function test_transfer_closes_the_previous_club_registration_on_approval(): void
    {
        $player = $this->player($this->clubB); // arrivé au club B
        $previous = $this->licence($player, $this->clubA);

        $license = $this->submit($player, 'transfer');
        $this->assertSame($previous->id, $license->previous_license_id);
        $this->approve($license);

        $previous->refresh();
        $this->assertSame('expired', $previous->status);
        $this->assertSame('inactive', LicenseWorkflow::fifaStatus($previous));
        $this->assertSame($license->fresh()->contract_start_date->copy()->subDay()->toDateString(), $previous->expiry_date->toDateString(), 'RegistrationValidTo : veille du nouvel enregistrement');
        $this->assertTrue($previous->events()->where('action', 'closed')->exists());
        $this->assertSame('active', LicenseWorkflow::fifaStatus($license->fresh()));
    }

    public function test_loan_is_a_loan_registration_and_keeps_the_primary_club(): void
    {
        $player = $this->player($this->clubB);
        $primary = $this->licence($player, $this->clubA);

        $license = $this->submit($player, 'loan');
        $this->assertSame('Loan', $license->registration_nature);
        $this->assertContains('loan_agreement', $this->workflow->requiredDocuments($license));
        $this->approve($license);
        $this->assertSame('active', $primary->fresh()->status, 'le club principal reste titulaire pendant le prêt');

        // Retour au club principal : le prêt est clôturé.
        DB::table('players')->where('id', $player->id)->update(['club_id' => $this->clubA]);
        $player = $player->fresh();
        $this->assertTrue($this->workflow->reasonsFor($player, 'Football', 'amateur')['loan_return']['allowed']);
        DB::table('player_licenses')->where('id', $primary->id)->update(['status' => 'expired']); // licence principale de la saison passée
        $return = $this->submit($player, 'loan_return');
        $this->approve($return);
        $this->assertSame('expired', $license->fresh()->status);
    }

    public function test_level_change_closes_the_other_level_in_the_same_club(): void
    {
        $player = $this->player($this->clubA);
        $pro = $this->licence($player, $this->clubA, ['level' => 'pro']);

        $reasons = $this->workflow->reasonsFor($player, 'Football', 'amateur');
        $this->assertTrue($reasons['level_change']['allowed']);
        $this->assertFalse($reasons['renewal']['allowed']);

        $license = $this->submit($player, 'level_change', 'amateur');
        $this->approve($license);
        $this->assertSame('expired', $pro->fresh()->status);
        $this->assertSame('active', $license->fresh()->status);
    }
}
