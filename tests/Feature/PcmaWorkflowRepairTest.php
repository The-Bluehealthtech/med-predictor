<?php
namespace Tests\Feature;
use App\Models\{PCMA, User};
use App\Http\Controllers\{PCMAController, PcmaDocumentController, PcmaDraftController, PcmaStatusController};
use App\Services\MedicalRecordAccess;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Http, Route, Schema, Storage};
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

final class PcmaWorkflowRepairTest extends TestCase
{
    private string $previous;
    protected function setUp(): void
    {
        parent::setUp();
        // Charger aussi les traductions du worktree testé, pas celles du dépôt principal.
        $this->app->instance('translation.loader', new \Illuminate\Translation\FileLoader(
            $this->app['files'], dirname(__DIR__, 2).'/resources/lang'));
        $this->app->forgetInstance('translator');
        app('view')->getFinder()->setPaths([dirname(__DIR__, 2).'/resources/views']);
        Route::middleware('api')->prefix('api')->group(dirname(__DIR__, 2).'/routes/api.php');
        Route::middleware('web')->group(dirname(__DIR__, 2).'/routes/web.php');
        Route::getRoutes()->refreshNameLookups();
        $this->previous = DB::getDefaultConnection();
        config()->set('database.connections.pcma_workflow', ['driver'=>'sqlite','database'=>':memory:','prefix'=>'']);
        DB::setDefaultConnection('pcma_workflow');
        Schema::create('users', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('role'); $t->unsignedBigInteger('club_id')->nullable();
            $t->unsignedBigInteger('association_id')->nullable(); $t->string('fifa_connect_id')->nullable();
        });
        Schema::create('clubs', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->unsignedBigInteger('association_id')->nullable();
        });
        Schema::create('players', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('first_name'); $t->string('last_name');
            $t->unsignedBigInteger('club_id')->nullable(); $t->string('fifa_connect_id')->nullable();
        });
        Schema::create('athletes', function (Blueprint $t) {
            $t->id(); $t->string('name')->nullable(); $t->unsignedBigInteger('team_id')->nullable();
            $t->unsignedBigInteger('player_id')->nullable();
        });
        Schema::create('teams', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('club_id')->nullable();
        });
        Schema::create('notifications', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->string('type'); $t->morphs('notifiable');
            $t->text('data'); $t->timestamp('read_at')->nullable(); $t->timestamps();
        });
        Schema::create('pcmas', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('player_id')->nullable(); $t->unsignedBigInteger('athlete_id')->nullable();
            $t->unsignedBigInteger('assessor_id')->nullable(); $t->string('type')->nullable();
            $t->string('status')->default('pending'); $t->date('assessment_date')->nullable();
            foreach (['result_json','medical_history','physical_examination','cardiovascular_investigations',
                'final_statement','scat_assessment','anatomical_annotations','notes','fifa_id',
                'signature_data','signed_by','license_number','signature_image','ecg_file','mri_file',
                'xray_file','ct_scan_file','ultrasound_file'] as $f) $t->text($f)->nullable();
            $t->boolean('is_signed')->default(false); $t->boolean('fifa_compliant')->default(false);
            $t->timestamp('signed_at')->nullable(); $t->timestamp('completed_at')->nullable(); $t->timestamps();
        });
        DB::table('clubs')->insert([['id'=>1,'name'=>'Fixture A','association_id'=>1],
            ['id'=>2,'name'=>'Fixture B','association_id'=>2]]);
        DB::table('players')->insert([['id'=>10,'name'=>'Fixture A','first_name'=>'Fixture',
            'last_name'=>'A','club_id'=>1], ['id'=>20,'name'=>'Fixture B','first_name'=>'Fixture',
            'last_name'=>'B','club_id'=>2]]);
        DB::table('users')->insert(['id'=>1,'name'=>'Fixture Doctor','role'=>'club_medical','club_id'=>1]);
        $this->actingAs(User::findOrFail(1)->forceFill(['tenant_id'=>1]));
        Http::preventStrayRequests();
        Storage::fake('local'); Storage::fake('public');
    }
    protected function tearDown(): void
    {
        DB::purge('pcma_workflow'); DB::setDefaultConnection($this->previous);
        parent::tearDown();
    }
    private function input(array $extra = []): array
    {
        return array_replace(['player_id'=>10,'type'=>'bpma','assessor_id'=>1,
            'assessment_date'=>'2026-09-30','status'=>'pending','heart_rate'=>60,
            'draft_token'=>'12345678-1234-4234-8234-123456789012'], $extra);
    }
    private function request(array $data, string $url='/api/pcma/auto-save'): Request
    {
        $r = Request::create($url, 'POST', $data, [], [], ['HTTP_ACCEPT'=>'application/json']);
        $r->setUserResolver(fn()=>auth()->user()); return $r;
    }
    private function record(array $extra=[]): PCMA
    {
        return PCMA::create(array_replace(['player_id'=>10,'assessor_id'=>1,'type'=>'bpma',
            'assessment_date'=>'2026-09-30','status'=>'pending',
            'result_json'=>['vital_signs'=>['heart_rate'=>60]]], $extra));
    }
    public function test_draft_persists_updates_and_finalizes_the_same_record_without_fifa(): void
    {
        $controller = app(PcmaDraftController::class);
        $first = $controller->save($this->request($this->input()))->getData(true);
        $second = $controller->save($this->request($this->input(['heart_rate'=>0])))->getData(true);
        self::assertSame($first['pcma_id'], $second['pcma_id']);
        self::assertSame(1, PCMA::count());
        self::assertSame(0, PCMA::find($first['pcma_id'])->result_json['vital_signs']['heart_rate']);
        $final = app(PCMAController::class)->store($this->request($this->input([
            'pcma_id'=>$first['pcma_id'], 'final_statement'=>['overall_decision'=>'CONDITIONAL']
        ]), '/pcma'));
        self::assertSame(200, $final->getStatusCode());
        self::assertSame($first['pcma_id'], $final->getData(true)['pcma_id']);
        self::assertSame(1, PCMA::count());
        self::assertFalse(PCMA::first()->is_signed);
        self::assertFalse(PCMA::first()->fifa_compliant);
    }
    public function test_edit_cannot_forge_a_medical_signature(): void
    {
        $pcma = $this->record();
        app(PCMAController::class)->update($this->request($this->input([
            'is_signed'=>true, 'signed_by'=>'Forged', 'license_number'=>'Forged',
            'fifa_compliant'=>true,
            'result_json'=>json_encode(['is_signed'=>true, 'signed_by'=>'Forged'])
        ])), $pcma);
        $saved = $pcma->fresh();
        self::assertFalse($saved->is_signed);
        self::assertFalse($saved->fifa_compliant);
        self::assertNull($saved->signed_by);
        self::assertNull($saved->license_number);
        self::assertArrayNotHasKey('is_signed', $saved->result_json);
        self::assertArrayNotHasKey('signed_by', $saved->result_json);
    }
    public function test_draft_rejects_other_club_and_different_assessor(): void
    {
        foreach ([['player_id'=>20], ['assessor_id'=>2]] as $extra) {
            if (isset($extra['assessor_id'])) DB::table('users')->insert([
                'id'=>2,'name'=>'Other','role'=>'club_medical','club_id'=>1]);
            try { app(PcmaDraftController::class)->save($this->request($this->input($extra)));
                self::fail('Accès interdit attendu.');
            } catch (HttpException $e) { self::assertSame(403, $e->getStatusCode()); }
            catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                self::assertSame(\App\Models\Player::class, $e->getModel());
            }
        }
        self::assertSame(0, PCMA::count());
    }
    public function test_direct_update_cannot_bypass_the_required_conclusion(): void
    {
        $pcma=$this->record();
        try {
            $pcma->update(['status'=>'completed']);
            self::fail('Conclusion requise sur tous les parcours.');
        } catch (HttpException $e) { self::assertSame(422, $e->getStatusCode()); }
        self::assertSame('pending', $pcma->fresh()->status);
    }
    public function test_signed_record_cannot_be_updated_deleted_or_auto_saved(): void
    {
        $pcma=$this->record(['is_signed'=>true,'signed_at'=>now()]);
        foreach (['update','delete','draft'] as $operation) {
            try {
                if ($operation==='update') $pcma->update(['notes'=>'Changed']);
                elseif ($operation==='delete') $pcma->delete();
                else app(PcmaDraftController::class)->save($this->request(
                    $this->input(['pcma_id'=>$pcma->id])));
                self::fail('Immutabilité attendue.');
            } catch (HttpException $e) { self::assertSame(409,$e->getStatusCode()); }
        }
        self::assertSame(1,PCMA::count()); self::assertNull($pcma->fresh()->notes);
    }
    public function test_signed_lists_and_record_access_are_scoped(): void
    {
        $own=$this->record(['is_signed'=>true,'signed_at'=>now()]);
        $other=$this->record(['player_id'=>20,'is_signed'=>true,'signed_at'=>now()]);
        self::assertSame([$own->id],array_column(app(PCMAController::class)->signed()->getData(true)['pcmas'],'id'));
        self::assertSame([$own->id],app(MedicalRecordAccess::class)
            ->scope(auth()->user(),PCMA::query())->pluck('id')->all());
        try { app(MedicalRecordAccess::class)->record(auth()->user(),$other); self::fail('403 attendu');
        } catch (HttpException $e) { self::assertSame(403,$e->getStatusCode()); }
    }
    public function test_pdf_preview_and_saved_export_are_real_pdf_without_fake_defaults(): void
    {
        $pdf=app(PcmaDocumentController::class)->generatePdf($this->request($this->input()));
        self::assertSame(200,$pdf->getStatusCode());
        self::assertStringStartsWith('%PDF-',$pdf->getContent());
        self::assertSame(0,PCMA::count());
        $pcma=$this->record();
        $this->app->instance('request',$this->request([]));
        $saved=app(PcmaDocumentController::class)->export($pcma);
        self::assertStringStartsWith('%PDF-',$saved->getContent());
        $html=view('pcma.pdf',['pcma'=>$pcma,'formData'=>['allergies'=>null,'notes'=>'<script>x</script>'],
            'isDraft'=>false,'generatedAt'=>now()])->render();
        self::assertStringContainsString('&lt;script&gt;',$html);
        self::assertStringNotContainsString('Aucune',$html);
    }
    public function test_status_requires_conclusion_and_get_does_not_mutate(): void
    {
        $pcma=$this->record();
        try { app(PcmaStatusController::class)->complete($this->request([]),$pcma);
            self::fail('Conclusion requise.');
        } catch (HttpException $e) { self::assertSame(422,$e->getStatusCode()); }
        self::assertSame('pending',$pcma->fresh()->status);
        $pcma->update(['final_statement'=>['overall_decision'=>'FIT']]);
        self::assertSame(200,app(PcmaStatusController::class)
            ->complete($this->request([]),$pcma)->getStatusCode());
        self::assertSame('completed',$pcma->fresh()->status);
        self::assertSame(['POST'],Route::getRoutes()->getByName('pcma.complete')->methods());
    }
    public function test_actual_http_draft_auth_and_signed_access(): void
    {
        $this->postJson('/api/pcma/auto-save', $this->input())->assertOk()
            ->assertJson(['success'=>true]);
        self::assertSame(1,PCMA::count());
        $this->actingAs((new User(['role'=>'player']))->forceFill(['tenant_id'=>1]));
        $this->postJson('/api/pcma/auto-save', $this->input())->assertForbidden();
        $this->getJson('/api/signed-pcmas')->assertForbidden();
        auth()->forgetUser();
        $this->postJson('/api/pcma/auto-save', $this->input())->assertUnauthorized();
    }
    public function test_private_file_download_enforces_record_access(): void
    {
        $pcma=$this->record(['ecg_file'=>'medical_imaging/fixture.pdf']);
        Storage::disk('local')->put('medical_imaging/fixture.pdf', '%PDF-fixture');
        $this->get('/pcma/'.$pcma->id.'/files/ecg_file')->assertOk();
        $other=$this->record(['player_id'=>20,'ecg_file'=>'medical_imaging/fixture.pdf']);
        $this->get('/pcma/'.$other->id.'/files/ecg_file')->assertForbidden();
    }
    public function test_ai_endpoints_validate_missing_inputs_without_500(): void
    {
        foreach (['ai-analyze-ecg','ai-analyze-mri','ai-analyze-complete',
            'whisper-transcribe','ocr-extract','prefill-from-transcript','fetch-fhir-data'] as $action) {
            $this->postJson('/api/v1/pcmas/'.$action,[])->assertStatus(422);
        }
        Http::assertNothingSent();
    }
    public function test_route_inventory_has_no_missing_pcma_controller_actions(): void
    {
        $count=0;
        foreach (Route::getRoutes() as $route) {
            if (!str_contains($route->uri(),'pcma')) continue;
            $action=$route->getActionName();
            if (!str_contains($action,'@')) continue;
            [$class,$method]=explode('@',$action,2);
            self::assertTrue(method_exists($class,$method),$route->uri().' => '.$action);
            $count++;
        }
        self::assertGreaterThan(25,$count);
        $signed=Route::getRoutes()->match(Request::create('/api/v1/pcmas/signed'));
        self::assertStringEndsWith('@getSignedPCMAs',$signed->getActionName());
    }
}
