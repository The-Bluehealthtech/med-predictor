<?php

namespace Tests\Feature;

use App\Http\Controllers\TransferController;
use App\Models\Transfer;
use App\Models\TransferDocument;
use App\Models\User;
use App\Services\ApiConnectorState;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TmsTransferWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    private int $associationId;
    private int $originId;
    private int $destinationId;
    private int $playerId;
    private Transfer $transfer;
    private User $registrar;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasColumn('transfer_documents', 'storage_disk')) {
            (require base_path('database/migrations/2026_10_03_227100_add_storage_integrity_to_transfer_documents.php'))->up();
        }
        if (!Schema::hasColumn('transfers', 'tms_transfer_id')) {
            (require base_path('database/migrations/2026_10_03_227200_add_tms_workflow_to_transfers.php'))->up();
        }

        Route::middleware(['web','auth'])->group(function () {
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
            'name'=>'Association TMS Test','country'=>'Tunisie','fifa_connect_id'=>'MA-TST',
            'created_at'=>now(),'updated_at'=>now(),
        ]);
        $this->originId = $this->club('Club TMS origine','CLUB-TMS-ORIGIN');
        $this->destinationId = $this->club('Club TMS destination','CLUB-TMS-DEST');

        $this->playerId = (int) DB::table('players')->insertGetId([
            'name'=>'Joueur TMS','first_name'=>'Joueur','last_name'=>'TMS',
            'club_id'=>$this->originId,'association_id'=>$this->associationId,
            'fifa_player_id'=>'PLAYER-TMS-1','is_transfer_eligible'=>true,
            'created_at'=>now(),'updated_at'=>now(),
        ]);

        $this->registrar = User::factory()->create([
            'role'=>'association_registrar',
            'association_id'=>$this->associationId,
            'status'=>'active','tenant_id'=>1,
        ]);

        $this->transfer = Transfer::query()->create([
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
            'currency'=>'EUR','is_international'=>true,
        ]);
    }

    private function club(string $name, string $fifaId): int
    {
        return (int) DB::table('clubs')->insertGetId([
            'name'=>$name,'association_id'=>$this->associationId,
            'fifa_club_id'=>$fifaId,'can_conduct_transfers'=>true,
            'created_at'=>now(),'updated_at'=>now(),
        ]);
    }

    private function approveRequiredDocuments(): void
    {
        foreach (['passport','contract'] as $type) {
            TransferDocument::query()->create([
                'transfer_id'=>$this->transfer->id,
                'uploaded_by'=>$this->registrar->id,
                'document_type'=>$type,
                'document_name'=>$type,
                'file_path'=>"transfers/{$this->transfer->id}/{$type}.pdf",
                'storage_disk'=>'local',
                'file_name'=>$type.'.pdf',
                'mime_type'=>'application/pdf',
                'file_size'=>8,
                'sha256'=>hash('sha256',$type),
                'validation_status'=>'approved',
                'validated_by'=>$this->registrar->id,
                'validated_at'=>now(),
            ]);
        }
    }

    public function test_fit_prepares_tms_dossier_but_does_not_submit_directly(): void
    {
        $this->approveRequiredDocuments();

        $response = $this->actingAs($this->registrar)
            ->postJson(route('transfers.prepare-tms',$this->transfer))
            ->assertOk()
            ->assertJsonPath('tms_sync_status','ready');

        $this->transfer->refresh();
        $this->assertSame('ready',$this->transfer->tms_sync_status);
        $this->assertNotNull($this->transfer->tms_payload_sha256);
        $this->assertSame('PLAYER-TMS-1',$this->transfer->tms_snapshot['player']['fifa_id']);
        $this->assertSame('CLUB-TMS-ORIGIN',$this->transfer->tms_snapshot['releasing_club']['fifa_id']);

        $this->actingAs($this->registrar)
            ->postJson(route('transfers.submit-to-fifa',$this->transfer))
            ->assertStatus(410)
            ->assertJsonPath('code','direct_tms_submission_disabled');
    }

    public function test_tms_readiness_blocks_missing_identifiers_or_documents(): void
    {
        DB::table('players')->where('id',$this->playerId)->update(['fifa_player_id'=>'FIT-PLAYER-LOCAL']);

        $this->actingAs($this->registrar)
            ->postJson(route('transfers.prepare-tms',$this->transfer))
            ->assertStatus(409);

        $this->transfer->refresh();
        $this->assertSame('not_ready',$this->transfer->tms_sync_status);
    }

    public function test_only_federation_can_mark_ready_and_link_tms_reference(): void
    {
        $this->approveRequiredDocuments();
        $club = User::factory()->create([
            'role'=>'club_admin','club_id'=>$this->originId,'status'=>'active','tenant_id'=>1,
        ]);

        $this->actingAs($club)
            ->postJson(route('transfers.prepare-tms',$this->transfer))
            ->assertForbidden();

        $this->actingAs($this->registrar)
            ->postJson(route('transfers.prepare-tms',$this->transfer))
            ->assertOk();

        $this->actingAs($this->registrar)
            ->postJson(route('transfers.link-tms',$this->transfer),['tms_transfer_id'=>'TMS-12345'])
            ->assertOk()
            ->assertJsonPath('tms_transfer_id','TMS-12345');

        $this->assertSame('linked',$this->transfer->fresh()->tms_sync_status);
    }

    public function test_sync_recovers_whitelisted_tms_and_itc_data(): void
    {
        $this->approveRequiredDocuments();
        $this->actingAs($this->registrar)->postJson(route('transfers.prepare-tms',$this->transfer))->assertOk();
        $this->actingAs($this->registrar)->postJson(route('transfers.link-tms',$this->transfer),[
            'tms_transfer_id'=>'TMS-999',
        ])->assertOk();

        config([
            'services.fifa_tms.bridge_url'=>'https://tms-bridge.test',
            'services.fifa_tms.bridge_token'=>'bridge-secret',
            'services.fifa_tms.mock_mode'=>false,
        ]);
        app(ApiConnectorState::class)->setEnabled('fifa_tms',true,$this->registrar->id);

        Http::fake([
            'https://tms-bridge.test/transfers/TMS-999' => Http::response([
                'tms_transfer_id'=>'TMS-999',
                'status'=>'completed',
                'itc_status'=>'approved',
                'itc_id'=>'ITC-77',
                'ignored_secret'=>'must-not-persist',
            ],200),
        ]);

        $this->actingAs($this->registrar)
            ->postJson(route('transfers.sync-tms',$this->transfer))
            ->assertOk()
            ->assertJsonPath('data.status','completed');

        $fresh = $this->transfer->fresh();
        $this->assertSame('synced',$fresh->tms_sync_status);
        $this->assertSame('completed',$fresh->tms_remote_status);
        $this->assertSame('approved',$fresh->itc_status);
        $this->assertSame('ITC-77',$fresh->fifa_itc_id);
        $this->assertArrayNotHasKey('ignored_secret',$fresh->tms_last_response);
    }

    public function test_sync_is_fail_closed_until_official_bridge_is_configured_and_enabled(): void
    {
        $this->approveRequiredDocuments();
        $this->actingAs($this->registrar)->postJson(route('transfers.prepare-tms',$this->transfer))->assertOk();
        $this->actingAs($this->registrar)->postJson(route('transfers.link-tms',$this->transfer),[
            'tms_transfer_id'=>'TMS-LOCKED',
        ])->assertOk();

        config([
            'services.fifa_tms.bridge_url'=>null,
            'services.fifa_tms.bridge_token'=>null,
        ]);
        Http::fake();

        $this->actingAs($this->registrar)
            ->postJson(route('transfers.sync-tms',$this->transfer))
            ->assertStatus(503);

        Http::assertNothingSent();
    }
}
