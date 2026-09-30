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
        config()->set('pcma_icd11', require dirname(__DIR__,2).'/config/pcma_icd11.php');
        $this->app->useDatabasePath(dirname(__DIR__,2).'/database');
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
    public function test_verified_icd11_selection_is_saved_without_diagnosis_or_signature(): void
    {
        $this->postJson('/api/pcma/auto-save',$this->input(['cardiovascular_icd11'=>'INVENTED']))->assertStatus(422);
        self::assertSame(0,PCMA::count());
        $saved=$this->postJson('/api/pcma/auto-save',$this->input(['cardiovascular_icd11'=>'BA00']))
            ->assertOk()->json('pcma_id');
        $pcma=PCMA::findOrFail($saved);
        $entry=$pcma->result_json['medical_history']['cardiovascular_icd11'];
        self::assertSame('BA00',$entry['code']);
        self::assertSame('2025-01',$entry['release']);
        self::assertSame('Essential hypertension',$entry['label_en']);
        self::assertFalse($pcma->is_signed);
        self::assertNull($pcma->final_statement);
        $html=view('pcma.partials.cardiovascular-history',compact('pcma'))->render();
        self::assertStringContainsString('BA00',$html);
        $this->postJson('/api/pcma/auto-save',$this->input(['pcma_id'=>$saved,'cardiovascular_icd11'=>'']))->assertOk();
        self::assertNull($pcma->fresh()->result_json['medical_history']['cardiovascular_icd11']);
    }
    private function importMedicationCatalogue(): \App\Services\MedicationCatalogue
    {
        $migration=require dirname(__DIR__,2).'/database/migrations/2026_09_30_110000_create_medication_catalogue_table.php';
        $migration->up();
        $catalogue=app(\App\Services\MedicationCatalogue::class);
        self::assertSame(3516, $catalogue->import());
        Http::fake([
            'rxnav.nlm.nih.gov/REST/drugs.json*'=>Http::response(['drugGroup'=>['conceptGroup'=>[
                ['conceptProperties'=>[['rxcui'=>'12345','name'=>'Fixture RxNorm medicine','tty'=>'SCD']]]
            ]]]),
            'rxnav.nlm.nih.gov/REST/rxcui/12345/properties.json'=>Http::response(['properties'=>[
                'rxcui'=>'12345','name'=>'Fixture RxNorm medicine','tty'=>'SCD']]),
            'rxnav.nlm.nih.gov/REST/version.json'=>Http::response(['version'=>'TEST-ONLY']),
        ]);
        return $catalogue;
    }
    public function test_supplied_catalogue_import_search_and_access(): void
    {
        $catalogue=$this->importMedicationCatalogue();
        self::assertSame(3516,$catalogue->import());
        self::assertSame(3516,DB::table('medication_catalogue')->count());
        $products=$catalogue->search('paracetamol');
        self::assertNotEmpty($products);
        foreach($products as $product) {
            self::assertSame('RxNorm',$product['source']);
        }
        $this->getJson('/api/pcma/medications?q=paracetamol')->assertOk()->assertJson(['success'=>true]);
        $this->getJson('/api/pcma/medications?q=a')->assertStatus(422);
        self::assertSame([],$catalogue->search('%%'));
        $this->actingAs((new User(['role'=>'player']))->forceFill(['tenant_id'=>1]));
        $this->getJson('/api/pcma/medications?q=paracetamol')->assertForbidden();
    }
    public function test_selected_medicine_roundtrip_uses_catalogue_identity_and_can_be_removed(): void
    {
        $catalogue=$this->importMedicationCatalogue();
        $product=$catalogue->search('paracetamol')[0];
        $selection=[['id'=>$product['id'],'presentation_id'=>null,
            'name'=>'<script>Forged</script>','atc'=>'INVENTED','dose'=>null,'route'=>null,'frequency'=>null]];
        $first=app(PcmaDraftController::class)->save($this->request($this->input([
            'medication_selection'=>json_encode($selection),'medications'=>'Note technique'
        ])))->getData(true);
        $pcma=PCMA::findOrFail($first['pcma_id']);
        $saved=$pcma->result_json['medical_history']['medication_products'];
        self::assertSame($product['name'],$saved[0]['name']);
        self::assertNull($saved[0]['atc']); self::assertNull($saved[0]['dose']);
        self::assertSame('Note technique',$pcma->result_json['medical_history']['medications']);
        $html=view('pcma.partials.medication-summary',compact('pcma'))->render();
        self::assertStringNotContainsString('<script>Forged',$html);
        self::assertStringContainsString(e($product['name']),$html);
        self::assertCount(count($product['presentations']),$catalogue->forEditing($saved)[0]['presentations']);
        $edit=view('pcma.partials.medications',compact('pcma'))->render();
        self::assertStringContainsString('name="medication_selection"',$edit);
        app(PcmaDraftController::class)->save($this->request($this->input([
            'pcma_id'=>$pcma->id,'medication_selection'=>'[]'])));
        self::assertSame([],$pcma->fresh()->result_json['medical_history']['medication_products']);
        self::assertSame(60,$pcma->fresh()->result_json['vital_signs']['heart_rate']);
        $api=$catalogue->applySelection(['medication_selection'=>json_encode($selection)],[]);
        self::assertSame($product['name'],$api['result_json']['medical_history']['medication_products'][0]['name']);
    }
    public function test_catalogue_rejects_unknown_medicines_and_presentations(): void
    {
        $catalogue=$this->importMedicationCatalogue();$product=$catalogue->search('paracetamol')[0];
        foreach([[['id'=>'not-in-catalogue']],[['id'=>$product['id'],'presentation_id'=>'unknown']]] as $items) {
            try{$catalogue->selections(json_encode($items));self::fail('Référence inconnue rejetée.');}
            catch(HttpException $e){self::assertSame(422,$e->getStatusCode());}
        }
    }
    public function test_pcma_write_is_read_by_the_portal_on_the_same_connection(): void
    {
        $response=app(PCMAController::class)->store($this->request($this->input([
            'final_statement'=>['overall_decision'=>'CONDITIONAL'],
            'result_json'=>json_encode(['pcma_score'=>0, 'cardiovascular_fitness'=>null])
        ]), '/pcma'))->getData(true);
        $record=PCMA::findOrFail($response['pcma_id']);
        self::assertSame(DB::connection()->getPdo(), $record->getConnection()->getPdo());
        // Même requête et même projection que PlayerPortalDataService.
        $row=DB::table('pcmas')->where('player_id',10)
            ->orderByDesc('assessment_date')->orderByDesc('id')->first();
        $portal=app(\App\Services\PlayerPcmaData::class)->fromRecord($row);
        self::assertSame($record->id, $portal->pcma_id);
        self::assertSame(10, $portal->player_id);
        self::assertSame('CONDITIONAL', $portal->medical_decision);
        self::assertFalse($portal->is_signed);
        self::assertSame('pending', $portal->pcma_status);
        self::assertSame(0, $portal->pcma_score);
        self::assertNull($portal->cardiovascular_fitness);
        self::assertNull($portal->next_assessment_date);
        self::assertNull(DB::table('pcmas')->where('player_id',20)->first());
    }
    public function test_portal_projects_signed_decisions_and_only_explicit_dates(): void
    {
        $projector=app(\App\Services\PlayerPcmaData::class);
        foreach (['FIT'=>'cleared','NOT_FIT'=>'not_cleared','CONDITIONAL'=>'conditional'] as $decision=>$status) {
            $row=(object)['id'=>1,'player_id'=>10,'status'=>'completed','is_signed'=>true,
                'final_statement'=>json_encode(['overall_decision'=>$decision]),
                'result_json'=>json_encode(['next_assessment_date'=>'2027-03-10'])];
            $data=$projector->fromRecord($row);
            self::assertSame($status, $data->pcma_status);
            self::assertSame('2027-03-10', $data->next_assessment_date);
        }
        $row->final_statement=null; $row->result_json=null; $row->status='approved';
        $data=$projector->fromRecord($row);
        self::assertNull($data->medical_decision);
        self::assertNull($data->pcma_status);
        self::assertNull($data->next_assessment_date);
        $row->final_statement=['cleared_for_competition'=>true,'not_cleared'=>true];
        self::assertNull($projector->fromRecord($row)->medical_decision);
        $row->final_statement=['cleared_with_restrictions'=>true];
        self::assertSame('CONDITIONAL', $projector->fromRecord($row)->medical_decision);
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
    private function fakeWho(): void
    {
        config()->set('services.icd11', ['client_id'=>'test-only','client_secret'=>'test-only',
            'base_url'=>'https://id.who.int','release'=>'2026-01','timeout'=>2,'cache_ttl'=>60]);
        \Illuminate\Support\Facades\Cache::flush();
        // Réponses simulées du contrat OMS : aucune observation de joueur.
        Http::fake([
            'https://icdaccessmanagement.who.int/connect/token'=>Http::response(['access_token'=>'test-token','expires_in'=>3600]),
            'https://id.who.int/*/search*'=>Http::response(['destinationEntities'=>[
                ['id'=>'http://id.who.int/icd/entity/761947693','theCode'=>'BA00','title'=>'<em>Hypertension</em>','isLeaf'=>false],
                ['id'=>'https://evil.example/1','theCode'=>'FAKE','title'=>'Rejected'],
                ['id'=>'http://id.who.int/icd/entity/2','title'=>'No code'],
            ]]),
            'https://id.who.int/icd/release/11/*/mms/761947693'=>Http::response([
                '@id'=>'http://id.who.int/icd/release/11/2026-01/mms/761947693',
                'code'=>'BA00','title'=>['@value'=>'Hypertension essentielle']]),
        ]);
    }
    public function test_who_search_checks_access_and_returns_plain_official_labels(): void
    {
        $this->fakeWho();
        $this->getJson('/api/pcma/icd11/search?q=hypertension&language=fr')->assertOk()
            ->assertJsonCount(1,'items')->assertJsonPath('items.0.code','BA00')
            ->assertJsonPath('items.0.label','Hypertension');
        Http::assertSent(fn($r)=>str_contains($r->url(),'/mms/search')
            && $r->hasHeader('API-Version','v2') && $r->hasHeader('Accept-Language','fr'));
        $this->getJson('/api/pcma/icd11/search?q=x&language=fr')->assertStatus(422);
        $this->actingAs((new User(['role'=>'player']))->forceFill(['tenant_id'=>1]));
        $this->getJson('/api/pcma/icd11/search?q=hypertension&language=fr')->assertForbidden();
    }
    public function test_who_codes_roundtrip_all_history_sections_without_client_labels(): void
    {
        $this->fakeWho();
        $choice=json_encode([['id'=>'761947693','release'=>'2026-01','language'=>'fr','code'=>'FORGED','label'=>'FORGED']]);
        $fields=[];
        foreach(['cardiovascular','surgical','allergies'] as $section)$fields[$section.'_icd11_selection']=$choice;
        $id=$this->postJson('/api/pcma/auto-save',$this->input($fields+['allergies'=>'Notes conservées']))
            ->assertOk()->json('pcma_id');
        $pcma=PCMA::findOrFail($id);
        foreach(['cardiovascular','surgical','allergies'] as $section){
            $entry=$pcma->result_json['medical_history'][$section.'_icd11_codes'][0];
            self::assertSame('BA00',$entry['code']); self::assertSame('Hypertension essentielle',$entry['label']);
            $html=view('pcma.partials.icd11-history',['pcma'=>$pcma,'section'=>$section,
                'textField'=>$section==='cardiovascular'?'cardiovascular_history':($section==='surgical'?'surgical_history':'allergies'),
                'label'=>'pcma.allergies_label'])->render();
            self::assertStringContainsString('BA00',$html);
        }
        self::assertFalse($pcma->is_signed); self::assertNull($pcma->final_statement);
        $this->postJson('/api/pcma/auto-save',$this->input(['pcma_id'=>$id,'allergies_icd11_selection'=>'[]']))->assertOk();
        self::assertSame([],$pcma->fresh()->result_json['medical_history']['allergies_icd11_codes']);
        self::assertSame('Notes conservées',$pcma->fresh()->result_json['medical_history']['allergies']);
        self::assertSame(60,$pcma->fresh()->result_json['vital_signs']['heart_rate']);
        self::assertCount(1,$pcma->fresh()->result_json['medical_history']['surgical_icd11_codes']);
    }
    public function test_who_unavailable_credentials_and_provider_failures_are_503(): void
    {
        config()->set('services.icd11',['base_url'=>'https://id.who.int','release'=>'2026-01']);
        $this->getJson('/api/pcma/icd11/search?q=allergie&language=fr')->assertStatus(503);
        Http::assertNothingSent();
        $this->fakeWho();
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::preventStrayRequests();
        Http::fake(['https://icdaccessmanagement.who.int/connect/token'=>Http::response(['access_token'=>'test-token']),
            'https://id.who.int/*'=>Http::response('unavailable',503)]);
        $this->getJson('/api/pcma/icd11/search?q=allergie&language=en')->assertStatus(503);
    }
    public function test_who_rejects_arbitrary_entity_urls_without_network_request(): void
    {
        $choice=json_encode([['id'=>'https://evil.example/1','release'=>'2026-01','language'=>'fr']]);
        $this->postJson('/api/pcma/auto-save',$this->input(['allergies_icd11_selection'=>$choice]))->assertStatus(422);
        self::assertSame(0,PCMA::count()); Http::assertNothingSent();
    }
    public function test_who_refreshes_token_once_after_401(): void
    {
        $this->fakeWho();
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::preventStrayRequests();
        Http::fake([
            'https://icdaccessmanagement.who.int/connect/token'=>Http::sequence()
                ->push(['access_token'=>'old','expires_in'=>3600])->push(['access_token'=>'new','expires_in'=>3600]),
            'https://id.who.int/*'=>Http::sequence()->push([],401)->push(['destinationEntities'=>[]]),
        ]);
        $this->getJson('/api/pcma/icd11/search?q=allergie&language=en')->assertOk()->assertJsonPath('items',[]);
        Http::assertSentCount(4);
    }
    public function test_who_timeout_malformed_response_and_unknown_entity_have_clear_errors(): void
    {
        $this->fakeWho();
        Http::swap(new \Illuminate\Http\Client\Factory()); Http::preventStrayRequests();
        Http::fake(['https://icdaccessmanagement.who.int/connect/token'=>Http::response(['access_token'=>'test-token']),
            'https://id.who.int/*'=>function(){throw new \Illuminate\Http\Client\ConnectionException('Simulated timeout');}]);
        $this->getJson('/api/pcma/icd11/search?q=allergie&language=fr')->assertStatus(503);
        Http::swap(new \Illuminate\Http\Client\Factory()); Http::preventStrayRequests();
        Http::fake(['https://id.who.int/*'=>Http::response(['unexpected'=>true])]);
        $this->getJson('/api/pcma/icd11/search?q=allergie&language=fr')->assertStatus(503);
        Http::swap(new \Illuminate\Http\Client\Factory()); Http::preventStrayRequests();
        Http::fake(['https://id.who.int/*'=>Http::response([],404)]);
        $choice=json_encode([['id'=>'999999999','release'=>'2026-01','language'=>'fr']]);
        $this->postJson('/api/pcma/auto-save',$this->input(['allergies_icd11_selection'=>$choice]))->assertStatus(422);
        self::assertSame(0,PCMA::count());
    }
    private function healthcareSchema(): void
    {
        Schema::create('health_records',function(Blueprint $t){
            $t->id();
            foreach((new \App\Models\HealthRecord)->getFillable() as $field)$t->text($field)->nullable();
            $t->timestamps();
        });
        Schema::create('medical_predictions',function(Blueprint $t){
            $t->id();
            foreach((new \App\Models\MedicalPrediction)->getFillable() as $field)$t->text($field)->nullable();
            $t->timestamps();
        });
        \Illuminate\Support\Facades\Event::fake([\App\Events\HealthRecordCreated::class]);
    }
    private function healthRecord(int $player=10): \App\Models\HealthRecord
    {
        return \App\Models\HealthRecord::create(['player_id'=>$player,'user_id'=>1,
            'status'=>'active','record_date'=>'2026-09-30','diagnosis'=>'Fixture clinical note']);
    }
    public function test_healthcare_lists_real_records_and_blocks_cross_club_operations(): void
    {
        $this->healthcareSchema();$own=$this->healthRecord();$other=$this->healthRecord(20);
        $this->get('/modules/healthcare')->assertOk()->assertSee('Fixture A')->assertDontSee('Fixture B')->assertDontSee('Patient Example');
        $this->get('/healthcare/records/'.$own->id)->assertOk()->assertSee('Fixture clinical note')->assertDontSee('45%');
        $this->get('/healthcare/records/'.$other->id)->assertNotFound();
        $this->get('/healthcare/records/'.$other->id.'/edit')->assertNotFound();
        $this->putJson('/healthcare/records/'.$other->id,['record_date'=>'2026-09-30'])->assertNotFound();
        $this->deleteJson('/healthcare/records/'.$other->id)->assertNotFound();
        $this->getJson('/health-records/'.$other->id)->assertForbidden();
        $this->actingAs((new User(['role'=>'player']))->forceFill(['tenant_id'=>1]));
        $this->getJson('/modules/healthcare')->assertForbidden();
        $this->getJson('/healthcare/predictions')->assertForbidden();
        $this->getJson('/healthcare/export?download=1')->assertForbidden();
    }
    public function test_healthcare_update_and_delete_persist_and_do_not_reassign_player(): void
    {
        $this->healthcareSchema();$record=$this->healthRecord();
        $this->put('/healthcare/records/'.$record->id,['record_date'=>'2026-09-29',
            'diagnosis'=>'Updated fixture','allergies'=>"First\nSecond",'aut_notes'=>'Fixture AUT note','dental_records'=>'["Fixture dental observation"]'])->assertRedirect();
        self::assertSame('Updated fixture',$record->fresh()->diagnosis);
        self::assertSame('Fixture AUT note',$record->fresh()->aut_notes);
        self::assertSame(['Fixture dental observation'],$record->fresh()->dental_records);
        self::assertSame(['First','Second'],$record->fresh()->allergies);
        self::assertSame('2026-09-29',$record->fresh()->record_date->format('Y-m-d'));
        $this->putJson('/healthcare/records/'.$record->id,['player_id'=>20,'record_date'=>'2026-09-30'])->assertStatus(422);
        self::assertSame(10,(int)$record->fresh()->player_id);
        $this->delete('/healthcare/records/'.$record->id)->assertRedirect();
        self::assertFalse(\App\Models\HealthRecord::whereKey($record->id)->exists());
    }
    public function test_healthcare_predictions_and_export_render_and_csv_is_scoped(): void
    {
        $this->healthcareSchema();$this->healthRecord();$this->healthRecord(20);
        $this->get('/healthcare/predictions')->assertOk()->assertSee(__('healthcare_repair.unvalidated'));
        $this->get('/healthcare/export')->assertOk()->assertSee('download=1');
        $response=$this->get('/healthcare/export?download=1')->assertOk();
        $csv=$response->streamedContent();
        self::assertStringContainsString('record_id,player_id',$csv);
        self::assertStringContainsString(',10,',$csv);
        self::assertStringNotContainsString(',20,',$csv);
        $record=\App\Models\HealthRecord::first();$record->update(['diagnosis'=>'=FORMULA']);
        $csv=$this->get('/healthcare/export?download=1')->assertOk()->streamedContent();
        self::assertStringContainsString("'=FORMULA",$csv);
    }
    public function test_healthcare_creation_without_fifa_does_not_generate_fake_prediction(): void
    {
        $this->healthcareSchema();
        $data=['player_id'=>10,'visit_date'=>'2026-09-30','record_date'=>'2026-09-30',
            'doctor_name'=>'Fixture Doctor','visit_type'=>'consultation','allergies'=>'[]'];
        $this->post('/health-records',$data)->assertRedirect();
        $record=\App\Models\HealthRecord::firstOrFail();
        self::assertSame(10,(int)$record->player_id);
        self::assertSame(0,\App\Models\MedicalPrediction::count());
        $this->postJson('/health-records/'.$record->id.'/generate-prediction')->assertStatus(503);
        self::assertSame(0,\App\Models\MedicalPrediction::count());
        $data['player_id']=20;
        $this->postJson('/health-records',$data)->assertNotFound();
        self::assertSame(1,\App\Models\HealthRecord::count());
    }
    public function test_healthcare_canonical_edit_and_null_date_render_without_error(): void
    {
        $this->withoutExceptionHandling();
        $this->healthcareSchema();$record=$this->healthRecord();
        $record->update(['allergies'=>['Fixture allergy'],'medications'=>['Fixture medication'],'record_date'=>null]);
        $this->get('/modules/healthcare')->assertOk();
        $this->get('/health-records/'.$record->id)->assertOk();
        $this->get('/healthcare/records/'.$record->id.'/edit')->assertOk()->assertSee('Fixture allergy');
    }
    public function test_healthcare_hl7_export_is_private_and_medically_scoped(): void
    {
        $this->healthcareSchema();
        $input=['player_id'=>10,'record_date'=>'2026-09-30',
            'analysis_data'=>['type'=>'Fixture','analysis'=>['note'=>'Fixture content <script>unsafe</script>']]];
        $response=$this->postJson('/health-records/generate-hl7-cda',$input)->assertOk();
        $id=$response->json('report_id');
        Storage::disk('local')->assertExists('hl7_reports/'.$id.'.xml');
        Storage::disk('public')->assertMissing('hl7_reports/'.$id.'.xml');
        $this->get('/health-records/download-hl7-cda/'.$id)->assertOk()->assertHeader('Content-Type','application/xml');
        $this->get('/health-records/view-hl7-cda/'.$id)->assertOk()->assertDontSee('<script>unsafe</script>',false);
        $this->actingAs(User::findOrFail(1)->forceFill(['club_id'=>2,'tenant_id'=>1]));
        $this->getJson('/health-records/download-hl7-cda/'.$id)->assertForbidden();
        $this->getJson('/health-records/view-hl7-cda/'.$id)->assertForbidden();
    }
    public function test_healthcare_create_list_edit_and_export_render_in_both_languages(): void
    {
        $this->healthcareSchema();$record=$this->healthRecord();
        foreach(['fr','en'] as $lang){
            $this->get('/modules/healthcare?lang='.$lang)->assertOk();
            $this->get('/health-records?lang='.$lang)->assertOk();
            $this->get('/health-records/create?player_id=10&lang='.$lang)->assertOk();
            $this->get('/healthcare/records/'.$record->id.'/edit?lang='.$lang)->assertOk();
            $this->get('/healthcare/predictions?lang='.$lang)->assertOk();
            $this->get('/healthcare/export?lang='.$lang)->assertOk();
        }
        $this->getJson('/health-records/create?player_id=20')->assertNotFound();
    }
    public function test_healthcare_record_with_structured_medications_and_codes_renders(): void
    {
        $this->withoutExceptionHandling();
        $this->healthcareSchema();$record=$this->healthRecord();
        $record->update([
            'allergies'=>[['code'=>'fixture-code','label'=>'Fixture allergy']],
            'medications'=>[['name'=>'Fixture medication','dose'=>'Fixture dose']],
            'icd_10_codes'=>[['code'=>'fixture-code','label'=>'Fixture label']],
            'snomed_ct_codes'=>['fixture-string',null],
            'loinc_codes'=>[['code'=>'fixture-loinc']],
        ]);
        $this->record();
        $this->get('/health-records/'.$record->id)->assertOk()->assertSee('Fixture medication');
        $this->get('/healthcare/records/'.$record->id)->assertOk()->assertSee('Fixture medication')->assertSee('health-record-page')->assertSee('id="medical-tab"',false);
    }
    public function test_medical_module_scope_dates_links_and_translations(): void
    {
        $this->healthcareSchema();
        $own=$this->healthRecord();
        $own->update(['record_date'=>null]);
        $foreign=$this->healthRecord(20);
        $foreign->update(['diagnosis'=>'Foreign confidential fixture']);
        $this->record();
        $this->record(['player_id'=>20]);
        $page=$this->get('/modules/medical?lang=fr')->assertOk();
        $page->assertSee('Module médical')->assertSee('Dossiers PCMA');
        $page->assertViewHas('stats',fn($s)=>$s===['records'=>1,'pcmas'=>1,'pending'=>1]);
        $page->assertDontSee('Foreign confidential fixture');
        $this->get('/modules/medical?lang=en')->assertOk()->assertSee('Medical module');
        $this->get('/modules/medical/athlete/10')->assertOk()->assertSee('Fixture clinical note');
        $this->get('/modules/medical/athlete/20')->assertNotFound();
        $this->get('/modules/medical/athlete/999')->assertNotFound();
        $this->get('/modules/medical/athlete/10/edit')->assertRedirect(route('health-records.edit',$own));
        $this->get('/modules/medical?q=B')->assertOk()->assertViewHas('players',fn($p)=>$p->total()===0);
        $this->get('/health-records?player_id=20')->assertNotFound();
    }
    public function test_medical_module_refuses_non_medical_roles(): void
    {
        $this->healthcareSchema();
        auth()->user()->forceFill(['role'=>'player','club_id'=>null]);
        $this->get('/modules/medical')->assertForbidden();
        $this->get('/modules/medical/athlete/20')->assertForbidden();
        $this->get('/medical-predictions/create')->assertForbidden();
    }
    public function test_medical_predictions_never_claim_a_fake_write(): void
    {
        $this->healthcareSchema();
        $this->postJson('/medical-predictions',['player_id'=>10])->assertStatus(503);
        $this->assertDatabaseCount('medical_predictions',0);
        $this->postJson('/medical-predictions',['player_id'=>20])->assertNotFound();
        $this->get('/medical-predictions/create')->assertOk()->assertSee(__('healthcare_repair.unvalidated'));
        $this->get('/medical-predictions/dashboard')->assertOk();
        $this->get('/medical-predictions/999')->assertNotFound();
        $this->get('/medical-predictions')->assertOk();
    }
    public function test_medical_history_is_scoped_and_player_text_is_escaped(): void
    {
        $this->healthcareSchema();
        $own=$this->healthRecord(); $other=$this->healthRecord(20);
        $foreign=\App\Models\MedicalPrediction::create(['player_id'=>20,'health_record_id'=>$other->id,
            'prediction_type'=>'FOREIGN-FIXTURE','status'=>'verified']);
        $item=\App\Models\MedicalPrediction::create(['player_id'=>10,'health_record_id'=>$own->id,
            'prediction_type'=>'OWN-FIXTURE','status'=>'active']);
        $this->get('/medical-predictions')->assertOk()->assertSee('OWN-FIXTURE')->assertDontSee('FOREIGN-FIXTURE');
        $this->get('/medical-predictions/'.$foreign->id)->assertNotFound();
        $this->get('/medical-predictions/'.$foreign->id.'/edit')->assertNotFound();
        $this->putJson('/medical-predictions/'.$foreign->id,[])->assertNotFound();
        $this->deleteJson('/medical-predictions/'.$foreign->id)->assertNotFound();
        $this->get('/medical-predictions/'.$item->id)->assertOk()->assertSee('OWN-FIXTURE');
        $this->putJson('/medical-predictions/'.$item->id,[])->assertStatus(503);
        $this->deleteJson('/medical-predictions/'.$item->id)->assertStatus(503);
        $this->assertDatabaseCount('medical_predictions',2);
        DB::table('players')->where('id',10)->update(['first_name'=>'<script>alert(1)</script>']);
        $this->get('/modules/medical')->assertOk()->assertDontSee('<script>alert(1)</script>',false);
        $this->get('/modules/medical/athlete/10')->assertOk()->assertDontSee('<script>alert(1)</script>',false);
    }
    private function autSchema(): void
    {
        $this->healthcareSchema();
        // Tester la migration depuis le schéma historique, sans duplicata de colonne.
        Schema::table('health_records',fn(Blueprint $t)=>$t->dropColumn('icd11_diagnoses'));
        $old=require dirname(__DIR__,2).'/database/migrations/2024_01_15_000006_create_tue_requests_table.php';
        $old->up();
        $migration=require dirname(__DIR__,2).'/database/migrations/2026_10_01_000001_add_icd11_and_aut_to_health_records.php';
        $migration->up();
        config()->set('medical_aut',require dirname(__DIR__,2).'/config/medical_aut.php');
    }
    public function test_health_record_who_codes_are_verified_preserved_and_clearable(): void
    {
        $this->healthcareSchema();$record=$this->healthRecord();$this->fakeWho();
        $this->getJson('/api/health-records/icd11/search?q=hypertension&language=fr')
            ->assertOk()->assertJsonPath('items.0.code','BA00');
        $choice=json_encode([['id'=>'761947693','release'=>'2026-01','language'=>'fr']]);
        $this->put('/health-records/'.$record->id,['record_date'=>'2026-09-30',
            'icd11_selection'=>$choice])->assertRedirect();
        self::assertSame('BA00',$record->fresh()->icd11_diagnoses[0]['code']);
        self::assertSame('Hypertension essentielle',$record->fresh()->icd11_diagnoses[0]['label']);
        $this->get('/health-records/'.$record->id)->assertOk()->assertSee('BA00');
        $this->get('/health-records/'.$record->id.'/edit')->assertOk()->assertSee('medical-icd11.js');
        $this->put('/health-records/'.$record->id,['record_date'=>'2026-09-30','diagnosis'=>'Independent text'])->assertRedirect();
        self::assertCount(1,$record->fresh()->icd11_diagnoses);
        $this->putJson('/health-records/'.$record->id,['record_date'=>'2026-09-30',
            'icd11_selection'=>'[{"id":"1","release":"2026-01","language":"fr","label":"FORGED"}]'])->assertStatus(422);
        $this->put('/health-records/'.$record->id,['record_date'=>'2026-09-30','icd11_selection'=>'[]'])->assertRedirect();
        self::assertSame([],$record->fresh()->icd11_diagnoses);
        $foreign=$this->healthRecord(20);
        $this->putJson('/health-records/'.$foreign->id,['record_date'=>'2026-09-30','icd11_selection'=>$choice])->assertForbidden();
    }
    public function test_aut_form_source_and_drafts_use_primary_player_without_fifa_id(): void
    {
        $this->autSchema();$record=$this->healthRecord();
        $this->get('/health-records/'.$record->id.'/aut/create?lang=fr')->assertOk()
            ->assertSee('1. Informations')->assertSee('7. Déclaration')->assertSee('Je certifie')
            ->assertSee('Déclaration de confidentialité');
        $source=$this->get('/health-records/'.$record->id.'/aut/source')->assertOk();
        self::assertSame('81e6a40e372ae3fec7886901c0befe43afcb1e7749815acaa4e041109bf73dec',
            hash_file('sha256',$source->baseResponse->getFile()->getPathname()));
        $this->post('/health-records/'.$record->id.'/aut',['form'=>['surname'=>'Fixture',
            'substance_1'=>'Fixture substance','diagnosis'=>'Fixture medical reason',
            'retroactive'=>'no'],'status'=>'approved','player_id'=>20])->assertRedirect();
        $item=\App\Models\TUERequest::firstOrFail();
        self::assertSame(10,(int)$item->player_id);self::assertSame($record->id,(int)$item->health_record_id);
        self::assertSame('pending',$item->status);self::assertNull($item->athlete_id);
        self::assertNull($item->approved_date);self::assertNull($item->approved_by);
        self::assertSame('FIFA-2024-annexe-2-fr',$item->aut_form_data['version']);
        $this->get('/health-records/'.$record->id.'/aut')->assertOk()->assertSee('Fixture substance');
        $this->put('/health-records/'.$record->id.'/aut/'.$item->id,
            ['form'=>['diagnosis'=>'Updated fixture','substance_1'=>'Updated substance']])->assertRedirect();
        self::assertSame('Updated fixture',$item->fresh()->reason);
        $this->get('/health-records/'.$record->id.'/aut/'.$item->id.'/edit?lang=en')
            ->assertOk()->assertSee('Therapeutic Use Exemption')->assertSee('7. Player declaration');
    }
    public function test_aut_documents_are_encrypted_and_cross_club_operations_refused(): void
    {
        $this->autSchema();$record=$this->healthRecord();$foreign=$this->healthRecord(20);
        $file=\Illuminate\Http\UploadedFile::fake()->createWithContent('fixture.pdf',"%PDF-1.4\nFixture private document\n%%EOF");
        $this->post('/health-records/'.$record->id.'/aut',['form'=>['surname'=>'Fixture'],'documents'=>[$file]])->assertRedirect();
        $item=\App\Models\TUERequest::firstOrFail();
        $raw=DB::table('medical_aut_documents')->value('content');
        self::assertStringNotContainsString('Fixture private document',$raw);
        $this->get('/health-records/'.$record->id.'/aut/'.$item->id.'/documents/0')
            ->assertOk()->assertSee('Fixture private document')->assertHeader('Cache-Control','no-store, private');
        $this->get('/health-records/'.$foreign->id.'/aut')->assertNotFound();
        $this->postJson('/health-records/'.$foreign->id.'/aut',['form'=>['surname'=>'Foreign']])->assertNotFound();
        $this->get('/health-records/'.$foreign->id.'/aut/'.$item->id.'/documents/0')->assertNotFound();
        $this->get('/health-records/'.$record->id.'/aut/'.$item->id.'/documents/99')->assertNotFound();
        $this->postJson('/health-records/'.$record->id.'/aut',['form'=>['invented'=>'x']])->assertStatus(422);
        $this->putJson('/health-records/'.$record->id.'/aut/'.$item->id,['form'=>['retroactive'=>'invented']])->assertStatus(422);
        self::assertSame(1,\App\Models\TUERequest::count());
        auth()->user()->forceFill(['role'=>'player']);
        $this->get('/health-records/'.$record->id.'/aut')->assertForbidden();
        $this->getJson('/api/health-records/icd11/search?q=hypertension&language=fr')->assertForbidden();
    }
    public function test_health_record_creation_saves_who_metadata_and_failures_do_not_write(): void
    {
        $this->healthcareSchema();$this->fakeWho();
        $choice=json_encode([['id'=>'761947693','release'=>'2026-01','language'=>'fr']]);
        $this->post('/health-records',['player_id'=>10,'visit_date'=>'2026-09-30',
            'doctor_name'=>'Fixture doctor','visit_type'=>'consultation','record_date'=>'2026-09-30',
            'icd11_selection'=>$choice])->assertRedirect();
        $record=\App\Models\HealthRecord::firstOrFail();
        self::assertSame('WHO ICD-11 API',$record->icd11_diagnoses[0]['source']);
        self::assertSame('2026-01',$record->icd11_diagnoses[0]['release']);
        $this->get('/health-records/create?player_id=10')->assertOk()->assertSee('medical-icd11.js');
        Http::swap(new \Illuminate\Http\Client\Factory());Http::preventStrayRequests();
        Http::fake(['https://id.who.int/*'=>Http::response([],503)]);
        $this->putJson('/health-records/'.$record->id,['record_date'=>'2026-09-30',
            'diagnosis'=>'Should not be stored','icd11_selection'=>'[{"id":"123","release":"2026-01","language":"fr"}]'])->assertStatus(503);
        self::assertNotSame('Should not be stored',$record->fresh()->diagnosis);
        self::assertSame('BA00',$record->fresh()->icd11_diagnoses[0]['code']);
    }
    public function test_aut_external_decisions_cannot_be_overwritten_and_invalid_upload_is_rejected(): void
    {
        $this->autSchema();$record=$this->healthRecord();
        $item=\App\Models\TUERequest::create(['player_id'=>10,'health_record_id'=>$record->id,
            'physician_id'=>1,'request_date'=>'2026-09-30','status'=>'approved']);
        $this->get('/health-records/'.$record->id.'/aut/'.$item->id.'/edit')->assertStatus(409);
        $this->putJson('/health-records/'.$record->id.'/aut/'.$item->id,['form'=>['diagnosis'=>'Overwrite']])->assertStatus(409);
        $bad=\Illuminate\Http\UploadedFile::fake()->createWithContent('fixture.html','<script>alert(1)</script>');
        $this->post('/health-records/'.$record->id.'/aut',['form'=>['surname'=>'Fixture'],'documents'=>[$bad]])
            ->assertSessionHasErrors('documents.0');
        self::assertSame(1,\App\Models\TUERequest::count());
        self::assertSame(0,\App\Models\MedicalAutDocument::count());
        $this->get('/health-records/'.$record->id.'/aut?lang=fr')->assertOk()->assertSee('Approbation historique');
    }
    public function test_aut_migration_adopts_legacy_table_and_render_applies_new_schema(): void
    {
        $this->autSchema();
        // La migration historique ne recrée pas la table et ne détruit aucune ligne.
        DB::table('tue_requests')->insert(['physician_id'=>1,'request_date'=>'2026-09-30','status'=>'pending']);
        $old=require dirname(__DIR__,2).'/database/migrations/2024_01_15_000006_create_tue_requests_table.php';
        $old->up();
        self::assertSame(1,DB::table('tue_requests')->count());
        self::assertTrue(Schema::hasColumn('health_records','icd11_diagnoses'));
        self::assertTrue(Schema::hasColumn('tue_requests','player_id'));
        self::assertTrue(Schema::hasTable('medical_aut_documents'));
        $command=new \ReflectionClass(\App\Console\Commands\DeployFit::class);
        self::assertContains('database/migrations/2026_10_01_000001_add_icd11_and_aut_to_health_records.php',
            $command->getConstant('MIGRATIONS'));
    }
    public function test_schema_only_does_not_run_imports_or_snapshots(): void
    {
        $command=$this->getMockBuilder(\App\Console\Commands\DeployFit::class)
            ->onlyMethods(['call'])->getMock();
        $command->setLaravel($this->app);
        $command->expects(self::once())->method('call')->with('migrate',self::callback(
            fn($a)=>$a['--force']===true && in_array('database/migrations/2026_10_01_000001_add_icd11_and_aut_to_health_records.php',$a['--path'],true)
        ))->willReturn(0);
        $result=$command->run(new \Symfony\Component\Console\Input\ArrayInput(['--schema-only'=>true]),
            new \Symfony\Component\Console\Output\BufferedOutput());
        self::assertSame(0,$result);
    }
    public function test_aut_schema_retry_preserves_existing_rows(): void
    {
        $this->autSchema();
        DB::table('tue_requests')->insert(['physician_id'=>1,'request_date'=>'2026-09-30','status'=>'pending']);
        $migration=require dirname(__DIR__,2).'/database/migrations/2026_10_01_000001_add_icd11_and_aut_to_health_records.php';
        $migration->up();
        self::assertSame(1,DB::table('tue_requests')->count());
        self::assertTrue(Schema::hasColumn('health_records','icd11_diagnoses'));
    }
    public function test_rxnorm_provider_failure_does_not_write_pcma(): void
    {
        $this->importMedicationCatalogue();
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::preventStrayRequests();
        Http::fake(['rxnav.nlm.nih.gov/*'=>Http::response([],503)]);
        $this->postJson('/api/pcma/auto-save',$this->input([
            'medication_selection'=>'[{"id":"12345"}]']))->assertStatus(503);
        self::assertSame(0,PCMA::count());
    }
    public function test_rxnorm_and_legacy_records_keep_separate_sources(): void
    {
        $catalogue=$this->importMedicationCatalogue();
        $row=DB::table('medication_catalogue')->first();
        $product=json_decode($row->payload,true);
        $old=['id'=>$product['id'],'name'=>$product['name'],'substances'=>$product['substances'],
            'source'=>'csv4Emd_Fr_2609A.zip','version'=>'2609A'];
        $saved=$catalogue->selections(json_encode([['id'=>$old['id']]]),[$old]);
        self::assertSame($old['source'],$saved[0]['source']);
        self::assertSame($old['name'],$saved[0]['name']);
        $rx=$catalogue->selections('[{"id":"12345","name":"FORGED"}]');
        self::assertSame('Fixture RxNorm medicine',$rx[0]['name']);
        self::assertSame('12345',$rx[0]['rxcui']);
        self::assertSame('TEST-ONLY',$rx[0]['version']);
    }
    public function test_health_record_rxnorm_selection_roundtrip_and_free_text(): void
    {
        $this->importMedicationCatalogue();
        $this->healthcareSchema();
        $record=$this->healthRecord();
        $this->put('/health-records/'.$record->id,[
            'player_id'=>10,'rxnorm_selection'=>'[{"id":"12345"}]',
            'medications'=>json_encode(['Note technique']),'diagnosis'=>'Fixture','record_date'=>'2026-09-30'
        ])->assertRedirect();
        $meds=$record->fresh()->medications;
        self::assertSame('Note technique',$meds[0]);
        self::assertSame('RxNorm',$meds[1]['source']);
        self::assertSame('12345',$meds[1]['rxcui']);
        $this->get('/health-records/'.$record->id.'/edit')->assertOk()->assertSee('rxnorm_selection',false);
    }
    public function test_aut_reference_preserves_version_and_exceptions_without_matching_drugs(): void
    {
        $this->autSchema();$record=$this->healthRecord();
        $service=app(\App\Services\AutSubstanceReference::class);
        $data=$service->data();
        self::assertSame('2025',$data['version']);
        self::assertCount(233,$data['entries']);
        self::assertFalse(collect($data['entries'])->contains(fn($e)=>str_contains($e['label'],'caféine')));
        self::assertStringContainsString('ne sont pas considérées comme des substances interdites',json_encode($data,JSON_UNESCAPED_UNICODE));
        $this->get('/health-records/'.$record->id.'/aut/create')->assertOk()->assertSee('aut-substances',false);
        $this->post('/health-records/'.$record->id.'/aut',['form'=>['substance_1'=>'salbutamol']])->assertRedirect();
        $item=\App\Models\TUERequest::first();
        self::assertSame(176,$item->aut_form_data['substance_reference']['substance_1']['row']);
        self::assertSame('2025',$item->aut_form_data['substance_reference']['substance_1']['version']);
        self::assertSame([],$service->provenance(['substance_1'=>'Fixture RxNorm medicine']));
    }

    public function test_rxnorm_empty_concept_and_invalid_json_are_rejected(): void
    {
        $this->importMedicationCatalogue();
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::preventStrayRequests();
        Http::fake(['rxnav.nlm.nih.gov/*'=>Http::response(['properties'=>null])]);
        $this->postJson('/api/pcma/auto-save',$this->input([
            'medication_selection'=>'[{"id":"12345"}]']))->assertStatus(422);
        $this->postJson('/api/pcma/auto-save',$this->input([
            'medication_selection'=>'{bad-json']))->assertStatus(422);
        self::assertSame(0,PCMA::count());
    }
    public function test_health_record_api_cannot_forge_rxnorm_label(): void
    {
        $this->importMedicationCatalogue();$this->healthcareSchema();$record=$this->healthRecord();
        $this->putJson('/health-records/'.$record->id,['record_date'=>'2026-09-30',
            'medications'=>[['id'=>'12345','source'=>'RxNorm','name'=>'FORGED']]])->assertRedirect();
        self::assertSame('Fixture RxNorm medicine',$record->fresh()->medications[0]['name']);
    }
}
