<?php

namespace Tests\Feature;

use App\Models\Transfer;
use App\Models\TransferPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TransferPaymentWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    private int $associationId;
    private int $originId;
    private int $destinationId;
    private Transfer $transfer;
    private User $registrar;

    protected function setUp(): void
    {
        parent::setUp();
        if (!Schema::hasColumn('transfer_payments','proof_status')) {
            (require base_path('database/migrations/2026_10_03_227300_add_proof_workflow_to_transfer_payments.php'))->up();
        }
        \Illuminate\Support\Facades\Route::middleware(['web','auth'])->group(function () {
            \Illuminate\Support\Facades\Route::post('/_t/transfers/{transfer}/payments', [\App\Http\Controllers\TransferPaymentController::class,'store'])->name('transfers.payments.store');
            \Illuminate\Support\Facades\Route::post('/_t/transfers/{transfer}/payments/{payment}/proof', [\App\Http\Controllers\TransferPaymentController::class,'uploadProof'])->name('transfers.payments.proof');
            \Illuminate\Support\Facades\Route::get('/_t/transfers/{transfer}/payments/{payment}/proof', [\App\Http\Controllers\TransferPaymentController::class,'downloadProof'])->name('transfers.payments.proof.download');
            \Illuminate\Support\Facades\Route::post('/_t/transfers/{transfer}/payments/{payment}/proof/decision', [\App\Http\Controllers\TransferPaymentController::class,'proofDecision'])->name('transfers.payments.proof.decision');
        });
        app('router')->getRoutes()->refreshNameLookups();

        Storage::fake('local');
        config(['services.transfers.document_disk'=>'local']);

        $this->associationId=(int)DB::table('associations')->insertGetId([
            'name'=>'Association Payment Test','country'=>'Tunisie','created_at'=>now(),'updated_at'=>now(),
        ]);
        $this->originId=$this->club('Origin');
        $this->destinationId=$this->club('Destination');
        $playerId=(int)DB::table('players')->insertGetId([
            'name'=>'Payment Player','club_id'=>$this->originId,'association_id'=>$this->associationId,
            'created_at'=>now(),'updated_at'=>now(),
        ]);
        $this->registrar=User::factory()->create([
            'role'=>'association_registrar','association_id'=>$this->associationId,'status'=>'active','tenant_id'=>1,
        ]);
        $this->transfer=Transfer::query()->create([
            'player_id'=>$playerId,'club_origin_id'=>$this->originId,'club_destination_id'=>$this->destinationId,
            'transfer_type'=>'permanent','transfer_status'=>'draft','itc_status'=>'not_requested',
            'transfer_window_start'=>now()->subDay(),'transfer_window_end'=>now()->addMonth(),
            'transfer_date'=>now(),'contract_start_date'=>now()->addDay(),'transfer_fee'=>100000,'currency'=>'EUR',
            'is_international'=>false,
        ]);
    }

    private function club(string $name): int
    {
        return (int)DB::table('clubs')->insertGetId([
            'name'=>$name,'association_id'=>$this->associationId,'created_at'=>now(),'updated_at'=>now(),
        ]);
    }

    private function createPayment(): TransferPayment
    {
        $this->actingAs($this->registrar)->post(route('transfers.payments.store',$this->transfer),[
            'payer_id'=>$this->destinationId,'payee_id'=>$this->originId,'payment_type'=>'transfer_fee',
            'amount'=>50000,'currency'=>'EUR','payment_method'=>'bank_transfer','due_date'=>now()->addMonth()->toDateString(),
        ])->assertRedirect();

        return TransferPayment::query()->where('transfer_id',$this->transfer->id)->firstOrFail();
    }

    public function test_federation_creates_payment_schedule(): void
    {
        $payment=$this->createPayment();
        $this->assertSame('pending',$payment->payment_status);
        $this->assertSame('missing',$payment->proof_status);
    }

    public function test_payer_club_uploads_proof_and_federation_validates_it(): void
    {
        $payment=$this->createPayment();
        $club=User::factory()->create(['role'=>'club_admin','club_id'=>$this->destinationId,'status'=>'active','tenant_id'=>1]);

        $this->actingAs($club)->post(route('transfers.payments.proof',[$this->transfer,$payment]),[
            'file'=>UploadedFile::fake()->createWithContent('proof.pdf','proof-bytes'),
            'payment_date'=>now()->toDateString(),'transaction_id'=>'TX-1',
        ])->assertRedirect();

        $payment->refresh();
        $this->assertSame('pending',$payment->proof_status);
        $this->assertSame(hash('sha256','proof-bytes'),$payment->proof_sha256);
        Storage::disk('local')->assertExists($payment->proof_file_path);

        $this->actingAs($this->registrar)->post(route('transfers.payments.proof.decision',[$this->transfer,$payment]),[
            'decision'=>'approve',
        ])->assertRedirect();

        $this->assertSame('approved',$payment->fresh()->proof_status);
    }

    public function test_non_payer_club_cannot_upload_payment_proof(): void
    {
        $payment=$this->createPayment();
        $club=User::factory()->create(['role'=>'club_admin','club_id'=>$this->originId,'status'=>'active','tenant_id'=>1]);

        $this->actingAs($club)->post(route('transfers.payments.proof',[$this->transfer,$payment]),[
            'file'=>UploadedFile::fake()->create('proof.pdf',5,'application/pdf'),
            'payment_date'=>now()->toDateString(),
        ])->assertForbidden();
    }
}
