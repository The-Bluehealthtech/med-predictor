<?php

namespace Tests\Feature;

use App\Http\Controllers\TransferController;
use App\Models\Transfer;
use App\Models\User;
use App\Services\FifaTransferService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TransferAccessHardeningTest extends TestCase
{
    use DatabaseTransactions;

    private int $associationId;
    private int $originId;
    private int $destinationId;
    private int $otherClubId;
    private int $playerId;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasColumn('transfers', 'tms_transfer_id')) {
            (require base_path('database/migrations/2026_10_03_227200_add_tms_workflow_to_transfers.php'))->up();
        }

        Route::middleware(['web','auth'])->group(function () {
            Route::get('/_t/transfers', [TransferController::class,'index'])->name('transfers.index');
            Route::get('/_t/transfers/create', [TransferController::class,'create'])->name('transfers.create');
            Route::post('/_t/transfers', [TransferController::class,'store'])->name('transfers.store');
            Route::get('/_t/transfers/{transfer}', [TransferController::class,'show'])->name('transfers.show');
            Route::post('/_t/transfers/{transfer}/prepare-tms', [TransferController::class,'prepareForTms'])->name('transfers.prepare-tms');
            Route::post('/_t/transfers/{transfer}/link-tms', [TransferController::class,'linkTmsReference'])->name('transfers.link-tms');
            Route::post('/_t/transfers/{transfer}/sync-tms', [TransferController::class,'syncFromTms'])->name('transfers.sync-tms');
            Route::post('/_t/transfers/{transfer}/submit-fifa', [TransferController::class,'submitToFifa'])->name('transfers.submit-to-fifa');
            Route::post('/_t/transfers/{transfer}/check-itc', [TransferController::class,'checkItcStatus'])->name('transfers.check-itc');
            Route::post('/_t/transfers/{transfer}/documents', [\App\Http\Controllers\TransferDocumentController::class,'store'])->name('transfers.documents.store');
            Route::get('/_t/transfers/{transfer}/documents/{document}', [\App\Http\Controllers\TransferDocumentController::class,'download'])->name('transfers.documents.download');
            Route::post('/_t/transfers/{transfer}/documents/{document}/decision', [\App\Http\Controllers\TransferDocumentController::class,'decision'])->name('transfers.documents.decision');
            Route::get('/_t/passports/transfer/{player}', fn () => response('passport'))->name('passports.transfer.show');
        });
        app('router')->getRoutes()->refreshNameLookups();

        $this->associationId = (int) DB::table('associations')->insertGetId([
            'name'=>'Association Transfer Test','country'=>'Tunisie','created_at'=>now(),'updated_at'=>now(),
        ]);
        $this->originId = $this->club('Club origine', $this->associationId);
        $this->destinationId = $this->club('Club destination', $this->associationId);
        $this->otherClubId = $this->club('Club hors dossier', $this->associationId);
        $this->playerId = (int) DB::table('players')->insertGetId([
            'name'=>'Joueur Transfert','first_name'=>'Joueur','last_name'=>'Transfert',
            'club_id'=>$this->originId,'association_id'=>$this->associationId,
            'is_transfer_eligible'=>true,'created_at'=>now(),'updated_at'=>now(),
        ]);
    }

    private function club(string $name, int $associationId): int
    {
        return (int) DB::table('clubs')->insertGetId([
            'name'=>$name,'association_id'=>$associationId,'can_conduct_transfers'=>true,
            'created_at'=>now(),'updated_at'=>now(),
        ]);
    }

    private function payload(): array
    {
        return [
            'player_id'=>$this->playerId,
            'club_origin_id'=>$this->originId,
            'club_destination_id'=>$this->destinationId,
            'transfer_type'=>'permanent',
            'transfer_date'=>now()->toDateString(),
            'contract_start_date'=>now()->addDay()->toDateString(),
            'currency'=>'EUR',
        ];
    }

    public function test_unrelated_club_and_medical_role_cannot_create_transfer(): void
    {
        $unrelated = User::factory()->create(['role'=>'club_admin','club_id'=>$this->otherClubId,'status'=>'active','tenant_id'=>1]);
        $this->actingAs($unrelated)->postJson('/_t/transfers',$this->payload())->assertForbidden();

        $medical = User::factory()->create(['role'=>'club_medical','club_id'=>$this->originId,'status'=>'active','tenant_id'=>1]);
        $this->actingAs($medical)->postJson('/_t/transfers',$this->payload())->assertForbidden();

        $this->assertDatabaseMissing('transfers', ['player_id'=>$this->playerId]);
    }

    public function test_origin_must_match_players_current_club(): void
    {
        $operator = User::factory()->create(['role'=>'club_admin','club_id'=>$this->destinationId,'status'=>'active','tenant_id'=>1]);
        $payload = $this->payload();
        $payload['club_origin_id'] = $this->otherClubId;

        $this->actingAs($operator)->postJson('/_t/transfers',$payload)
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_transfer_web_workflow_renders_canonical_index_and_detail(): void
    {
        $operator = User::factory()->create(['role'=>'club_admin','club_id'=>$this->originId,'status'=>'active','tenant_id'=>1]);
        $transfer = Transfer::query()->create([
            'player_id'=>$this->playerId,
            'club_origin_id'=>$this->originId,
            'club_destination_id'=>$this->destinationId,
            'transfer_type'=>'permanent',
            'transfer_status'=>'draft',
            'itc_status'=>'not_requested',
            'transfer_window_start'=>now()->subDay()->toDateString(),
            'transfer_window_end'=>now()->addDay()->toDateString(),
            'transfer_date'=>now()->toDateString(),
            'contract_start_date'=>now()->addDay()->toDateString(),
            'currency'=>'EUR',
            'is_international'=>false,
            'created_by'=>$operator->id,
        ]);

        $this->actingAs($operator)->get('/_t/transfers')->assertOk()->assertSee('Ouvrir le dossier');
        $this->actingAs($operator)->get('/_t/transfers/'.$transfer->id)->assertOk()
            ->assertSee('Dossier de transfert')
            ->assertSee('FIT prépare le dossier ; FIFA TMS exécute le transfert');
    }

    public function test_fifa_transfer_service_is_fail_closed_without_real_credentials(): void
    {
        config(['fifa.api_url'=>null,'fifa.api_key'=>null,'fifa.api_secret'=>null]);
        Http::fake();
        $service = app(FifaTransferService::class);

        $this->assertFalse($service->isConfigured());
        $result = $service->createTransfer(new Transfer());

        $this->assertFalse($result['success']);
        $this->assertSame('not_configured', $result['code']);
        Http::assertNothingSent();
    }

    public function test_dummy_fifa_credentials_are_never_considered_configured(): void
    {
        config(['fifa.api_url'=>'https://example.test','fifa.api_key'=>'dummy_key','fifa.api_secret'=>'dummy_secret']);
        $this->assertFalse(app(FifaTransferService::class)->isConfigured());
    }
}
