<?php

namespace Tests\Feature;

use App\Http\Controllers\TransferDocumentController;
use App\Models\Transfer;
use App\Models\TransferDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TransferDocumentWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    private int $associationId;
    private int $originId;
    private int $destinationId;
    private int $otherClubId;
    private int $playerId;
    private Transfer $transfer;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasColumn('transfer_documents', 'storage_disk')) {
            (require base_path('database/migrations/2026_10_03_227100_add_storage_integrity_to_transfer_documents.php'))->up();
        }

        Route::middleware(['web','auth'])->group(function () {
            Route::post('/_t/transfers/{transfer}/documents', [TransferDocumentController::class,'store'])->name('transfers.documents.store');
            Route::get('/_t/transfers/{transfer}/documents/{document}', [TransferDocumentController::class,'download'])->name('transfers.documents.download');
            Route::post('/_t/transfers/{transfer}/documents/{document}/decision', [TransferDocumentController::class,'decision'])->name('transfers.documents.decision');
        });
        app('router')->getRoutes()->refreshNameLookups();

        Storage::fake('local');
        config(['services.transfers.document_disk'=>'local']);

        $this->associationId = (int) DB::table('associations')->insertGetId([
            'name'=>'Association Documents Test','country'=>'Tunisie','created_at'=>now(),'updated_at'=>now(),
        ]);
        $this->originId = $this->club('Club origine');
        $this->destinationId = $this->club('Club destination');
        $this->otherClubId = $this->club('Club tiers');

        $this->playerId = (int) DB::table('players')->insertGetId([
            'name'=>'Joueur Documents','first_name'=>'Joueur','last_name'=>'Documents',
            'club_id'=>$this->originId,'association_id'=>$this->associationId,
            'is_transfer_eligible'=>true,'created_at'=>now(),'updated_at'=>now(),
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
            'currency'=>'EUR',
            'is_international'=>false,
        ]);
    }

    private function club(string $name): int
    {
        return (int) DB::table('clubs')->insertGetId([
            'name'=>$name,'association_id'=>$this->associationId,'can_conduct_transfers'=>true,
            'created_at'=>now(),'updated_at'=>now(),
        ]);
    }

    private function user(string $role, array $extra=[]): User
    {
        return User::factory()->create(array_merge(['role'=>$role,'status'=>'active','tenant_id'=>1],$extra));
    }

    public function test_participating_club_uploads_private_document_with_integrity_hash(): void
    {
        $club = $this->user('club_admin',['club_id'=>$this->originId]);
        $file = UploadedFile::fake()->createWithContent('passport.pdf','%PDF-private-transfer');

        $this->actingAs($club)->post(route('transfers.documents.store',$this->transfer),[
            'document_type'=>'passport','file'=>$file,
        ])->assertRedirect()->assertSessionHas('success');

        $document = TransferDocument::query()->where('transfer_id',$this->transfer->id)->firstOrFail();
        $this->assertSame('pending',$document->validation_status);
        $this->assertSame('local',$document->storage_disk);
        $this->assertSame(hash('sha256','%PDF-private-transfer'),$document->sha256);
        Storage::disk('local')->assertExists($document->file_path);
    }

    public function test_unrelated_club_and_medical_role_cannot_upload(): void
    {
        $payload = ['document_type'=>'contract','file'=>UploadedFile::fake()->create('contract.pdf',8,'application/pdf')];

        $this->actingAs($this->user('club_admin',['club_id'=>$this->otherClubId]))
            ->post(route('transfers.documents.store',$this->transfer),$payload)->assertForbidden();

        $this->actingAs($this->user('club_medical',['club_id'=>$this->originId]))
            ->post(route('transfers.documents.store',$this->transfer),$payload)->assertForbidden();

        $this->assertDatabaseCount('transfer_documents',0);
    }

    public function test_federation_validates_document_and_club_cannot_decide(): void
    {
        $club = $this->user('club_admin',['club_id'=>$this->originId]);
        $this->actingAs($club)->post(route('transfers.documents.store',$this->transfer),[
            'document_type'=>'contract','file'=>UploadedFile::fake()->create('contract.pdf',8,'application/pdf'),
        ])->assertRedirect();
        $document = TransferDocument::query()->firstOrFail();

        $this->actingAs($club)->post(route('transfers.documents.decision',[$this->transfer,$document]),[
            'decision'=>'approve',
        ])->assertForbidden();

        $federation = $this->user('association_registrar',['association_id'=>$this->associationId]);
        $this->actingAs($federation)->post(route('transfers.documents.decision',[$this->transfer,$document]),[
            'decision'=>'approve',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertSame('approved',$document->fresh()->validation_status);
    }

    public function test_replacement_expires_previous_approved_version(): void
    {
        $club = $this->user('club_admin',['club_id'=>$this->originId]);
        $federation = $this->user('association_admin',['association_id'=>$this->associationId]);

        $this->actingAs($club)->post(route('transfers.documents.store',$this->transfer),[
            'document_type'=>'passport','file'=>UploadedFile::fake()->createWithContent('passport-v1.pdf','v1'),
        ])->assertRedirect();
        $first = TransferDocument::query()->firstOrFail();

        $this->actingAs($federation)->post(route('transfers.documents.decision',[$this->transfer,$first]),[
            'decision'=>'approve',
        ])->assertRedirect();

        $this->actingAs($club)->post(route('transfers.documents.store',$this->transfer),[
            'document_type'=>'passport','file'=>UploadedFile::fake()->createWithContent('passport-v2.pdf','v2'),
        ])->assertRedirect();

        $first->refresh();
        $second = TransferDocument::query()->latest('id')->firstOrFail();
        $this->assertSame('expired',$first->validation_status);
        $this->assertSame('pending',$second->validation_status);
        $this->assertNotSame($first->sha256,$second->sha256);
    }

    public function test_download_is_private_and_scoped(): void
    {
        $club = $this->user('club_manager',['club_id'=>$this->originId]);
        $this->actingAs($club)->post(route('transfers.documents.store',$this->transfer),[
            'document_type'=>'contract','file'=>UploadedFile::fake()->createWithContent('contract.pdf','contract-bytes'),
        ])->assertRedirect();
        $document = TransferDocument::query()->firstOrFail();

        $this->actingAs($club)->get(route('transfers.documents.download',[$this->transfer,$document]))
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->actingAs($this->user('club_admin',['club_id'=>$this->otherClubId]))
            ->get(route('transfers.documents.download',[$this->transfer,$document]))
            ->assertForbidden();
    }
}
