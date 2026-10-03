<?php
namespace Tests\Feature;
use App\Models\{User,Player,HealthRecord,ImagingStudy,ImagingInstance,ImagingReport};
use App\Services\{AgeVerificationService,MedicalImagingDicom};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema,Route,Http};
use Tests\TestCase;

class MedicalImagingWorkflowTest extends TestCase {
    private string $layoutDirectory;
    public function createApplication() {
        $app=require dirname(__DIR__,2).'/bootstrap/app.php';
        $app->booting(function()use($app){$app['config']->set('cache.default','array');$app['config']->set('database.default','sqlite');$app['config']->set('database.connections.sqlite.database',':memory:');});
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap(); return $app;
    }
    protected function setUp():void {
        parent::setUp();
        Schema::create('users',function(Blueprint $t){$t->id();$t->string('name');$t->string('email');$t->string('password')->nullable();$t->string('role');$t->unsignedBigInteger('tenant_id')->nullable();$t->unsignedBigInteger('club_id')->nullable();$t->unsignedBigInteger('association_id')->nullable();$t->timestamps();});
        Schema::create('clubs',function(Blueprint $t){$t->id();$t->string('name');$t->unsignedBigInteger('association_id')->nullable();$t->timestamps();});
        Schema::create('players',function(Blueprint $t){$t->id();$t->string('name');$t->date('date_of_birth')->nullable();$t->unsignedBigInteger('club_id')->nullable();$t->timestamps();});
        Schema::create('health_records',function(Blueprint $t){$t->id();$t->unsignedBigInteger('player_id');$t->unsignedBigInteger('user_id');$t->date('record_date');$t->timestamps();});
        (require base_path('database/migrations/2026_10_02_000001_create_medical_imaging_workspace.php'))->up();
        (require base_path('database/migrations/2025_09_05_164708_create_system_settings_table.php'))->up();
        (require base_path('database/migrations/2026_10_03_180000_create_document_signature_requests.php'))->up();
        DB::table('clubs')->insert(['id'=>1,'name'=>'Fixture Club']);
        DB::table('players')->insert(['id'=>1,'name'=>'Fixture Player','date_of_birth'=>'2010-02-01','club_id'=>1]);
        DB::table('health_records')->insert(['id'=>873,'player_id'=>1,'user_id'=>1,'record_date'=>'2026-10-01']);
        DB::table('users')->insert(['id'=>1,'name'=>'Fixture Doctor','email'=>'fixture@example.test','role'=>'doctor','club_id'=>1,'tenant_id'=>1]);
        $this->actingAs(User::findOrFail(1));
        Route::middleware(['web','auth'])->group(base_path('routes/medical-imaging.php'));
        foreach(['health-records.show','health-records.modules.show'] as $name) if(!Route::has($name)) Route::get('/fixture/'.str_replace('.','/',$name).'/{healthRecord?}',fn()=>'')->name($name);
        Route::get('/login',fn()=>'')->name('login'); Route::getRoutes()->refreshNameLookups();
        $this->layoutDirectory=sys_get_temp_dir().'/fit-imaging-test-'.bin2hex(random_bytes(6));mkdir($this->layoutDirectory.'/layouts',0700,true);
        file_put_contents($this->layoutDirectory.'/layouts/app.blade.php',"@yield('content')");app('view')->getFinder()->prependLocation($this->layoutDirectory);
    }
    protected function tearDown():void { unlink($this->layoutDirectory.'/layouts/app.blade.php');rmdir($this->layoutDirectory.'/layouts');rmdir($this->layoutDirectory);parent::tearDown(); }
    private function study(string $purpose='general'):ImagingStudy {
        return ImagingStudy::create(['health_record_id'=>873,'player_id'=>1,'exam_date'=>'2026-10-01','modality'=>'MR','purpose'=>$purpose,'body_region'=>'Poignet','source'=>'Fixture Centre','created_by'=>1,'study_uid'=>app(MedicalImagingDicom::class)->uid(),'identity_checked'=>true,'identity_note'=>'Fixture identity verified','source_identity'=>['id'=>'FIT-1','name'=>'Fixture Player','birth_date'=>'20100201','sex'=>'M']]);
    }
    private function image(ImagingStudy $study):ImagingInstance {
        return $study->instances()->create(['original_name'=>'fixture.dcm','mime_type'=>'application/dicom','sha256'=>hash('sha256','fixture'),'series_uid'=>app(MedicalImagingDicom::class)->uid(),'sop_uid'=>app(MedicalImagingDicom::class)->uid(),'sop_class_uid'=>'1.2.840.10008.5.1.4.1.1.4','metadata'=>['rows'=>10,'columns'=>20,'frames'=>2,'pixel_spacing'=>[.5,.5]],'content'=>base64_encode('fixture'),'uploaded_by'=>1]);
    }
    private function save(ImagingStudy $study,ImagingInstance $image,array $extra=[]):ImagingReport {
        $this->post(route('medical-imaging.report.save',[873,$study]),array_replace(['quality'=>'interpretable','technique'=>'Fixture MR protocol','findings'=>'Fixture observation','conclusion'=>'Fixture medical conclusion','reference_images'=>json_encode([['instance_id'=>$image->id,'frame'=>1,'points'=>[1,1,11,1]]])],$extra))->assertRedirect()->assertSessionHasNoErrors();
        return $study->reports()->firstOrFail();
    }
    public function test_new_exams_and_draft_and_validated_screens_render():void {
        $this->get(route('medical-imaging.index',873))->assertOk()->assertSee('Nouvel examen');
        $study=$this->study('age_u17');$image=$this->image($study);
        $screen=$this->get(route('medical-imaging.show',[873,$study]));$screen->assertOk()->assertSee('Grade radiologique retenu')->assertSee('Mesurer');
        if (getenv('FIT_IMAGING_CAPTURE_UI')) file_put_contents('/tmp/fit-imaging-screen.html',$screen->getContent());
        $report=$this->save($study,$image,['age'=>['population'=>'male','grade'=>6,'consent'=>1]]);
        $this->assertEquals(5,$report->reference_images[0]['length_mm']);
        $this->post(route('medical-imaging.report.validate',[873,$study,$report]),['confirm'=>1,'edit_revision'=>1])->assertRedirect()->assertSessionHasNoErrors();
        $this->get(route('medical-imaging.show',[873,$study]))->assertOk()->assertSee('Télécharger DICOM SR')->assertSee('Connexion PACS non configurée');
        $assessment=app(AgeVerificationService::class)->assess(Player::find(1));$labels=implode(' ',array_column($assessment['flags'],'label'));
        $this->assertStringContainsString('fusion complète',$labels);$this->assertStringContainsString('aucun âge réel',$labels);
        $this->assertNotEquals('stored',$report->fresh()->pacs_status);
    }
    public function test_digital_signature_is_only_available_for_validated_report():void {
        $study=$this->study();$image=$this->image($study);$report=$this->save($study,$image);
        $url=route('medical-imaging.report.digital-signature',[873,$study,$report]);
        $this->post($url,['provider'=>'adobe_sign'])->assertStatus(409);

        $this->post(route('medical-imaging.report.validate',[873,$study,$report]),['confirm'=>1,'edit_revision'=>1])->assertSessionHasNoErrors();
        $screen=$this->get(route('medical-imaging.show',[873,$study]))->assertOk();
        $screen->assertSee('Signature numérique du PDF')->assertSee('Aucun fournisseur de signature n’est activé');
        $this->post($url,['provider'=>'adobe_sign'])->assertSessionHas('error');
    }

    public function test_cross_club_and_cross_study_access_is_denied():void {
        $study=$this->study();$image=$this->image($study);$other=$this->study();
        $this->get(route('medical-imaging.image',[873,$other,$image]))->assertNotFound();
        DB::table('users')->where('id',1)->update(['club_id'=>2]);$this->actingAs(User::find(1));
        $this->get(route('medical-imaging.show',[873,$study]))->assertForbidden();
        $this->post(route('medical-imaging.report.save',[873,$study]),[])->assertForbidden();
    }
    public function test_validation_requires_identity_complete_report_and_medical_role():void {
        $study=$this->study();$image=$this->image($study);$report=$this->save($study,$image);
        $study->update(['identity_checked'=>false]);
        $this->post(route('medical-imaging.report.validate',[873,$study,$report]),['confirm'=>1,'edit_revision'=>1])->assertSessionHasErrors('identity');
        $this->assertEquals('draft',$report->fresh()->status);
        DB::table('users')->where('id',1)->update(['role'=>'system_admin']);$this->actingAs(User::find(1));
        $this->post(route('medical-imaging.report.validate',[873,$study,$report]),['confirm'=>1,'edit_revision'=>1])->assertForbidden();
    }
    public function test_female_and_uninterpretable_u17_cannot_receive_male_grade():void {
        foreach([['population'=>'female','grade'=>6,'consent'=>1],['population'=>'male','grade'=>6,'consent'=>1]] as $i=>$age){
            $study=$this->study('age_u17');$image=$this->image($study);$report=$this->save($study,$image,['age'=>$age,'quality'=>$i===0?'interpretable':'uninterpretable']);
            $this->post(route('medical-imaging.report.validate',[873,$study,$report]),['confirm'=>1,'edit_revision'=>1])->assertSessionHasErrors('grade');
        }
    }
    public function test_validated_report_is_immutable_and_amendment_keeps_predecessor():void {
        $study=$this->study();$image=$this->image($study);$report=$this->save($study,$image);
        $this->post(route('medical-imaging.report.validate',[873,$study,$report]),['confirm'=>1,'edit_revision'=>1])->assertSessionHasNoErrors();
        $this->post(route('medical-imaging.report.save',[873,$study]),['report_id'=>$report->id,'edit_revision'=>1,'quality'=>'interpretable','conclusion'=>'Changed'])->assertStatus(409);
        $this->save($study,$image,['conclusion'=>'Amended fixture']);
        $this->assertEquals(2,$study->reports()->count());$this->assertEquals('Fixture medical conclusion',$report->fresh()->conclusion);
    }
    public function test_foreign_reference_and_out_of_range_coordinates_are_rejected():void {
        $study=$this->study();$other=$this->study();$image=$this->image($other);
        $this->post(route('medical-imaging.report.save',[873,$study]),['quality'=>'interpretable','reference_images'=>json_encode([['instance_id'=>$image->id,'frame'=>0]])])->assertStatus(422);
        $image=$this->image($study);
        $this->post(route('medical-imaging.report.save',[873,$study]),['quality'=>'interpretable','reference_images'=>json_encode([['instance_id'=>$image->id,'frame'=>0,'points'=>[1,1,999,2]]])])->assertStatus(422);
    }
    public function test_documentary_discrepancies_are_sourced_and_do_not_estimate_age():void {
        $study=$this->study('age_u17');$image=$this->image($study);
        $report=$this->save($study,$image,['age'=>['population'=>'male','grade'=>4,'consent'=>1,'evidence'=>[['source'=>'Fixture civil record','reference'=>'DOC-123','birth_date'=>'2008-02-01']]]]);
        $this->post(route('medical-imaging.report.validate',[873,$study,$report]),['confirm'=>1,'edit_revision'=>1])->assertSessionHasNoErrors();
        $result=app(AgeVerificationService::class)->assess(Player::find(1));
        $labels=implode(' ',array_column($result['flags'],'label'));$sources=implode(' ',array_column($result['flags'],'source'));
        $this->assertStringContainsString('Fixture civil record',$labels);$this->assertStringContainsString('DOC-123',$sources);$this->assertArrayNotHasKey('estimated_age',$result);
    }
    public function test_stale_draft_cannot_overwrite_and_draft_cannot_export():void {
        $study=$this->study();$image=$this->image($study);$report=$this->save($study,$image);
        $this->get(route('medical-imaging.report.dicom',[873,$study,$report]))->assertStatus(409);
        $this->post(route('medical-imaging.report.save',[873,$study]),['report_id'=>$report->id,'edit_revision'=>0,'quality'=>'interpretable'])->assertStatus(409);
    }
    public function test_image_content_is_encrypted_and_never_serialized():void {
        $study=$this->study();$image=$this->image($study);
        $this->assertNotEquals(base64_encode('fixture'),DB::table('fit_imaging_instances')->where('id',$image->id)->value('content'));
        $this->assertArrayNotHasKey('content',$image->toArray());
    }
    private function realImage(ImagingStudy $study): ImagingInstance {
        config(['medical_imaging.python'=>getenv('MEDICAL_IMAGING_TEST_PYTHON')?:'python3']);
        $probe=new \Symfony\Component\Process\Process([config('medical_imaging.python'),'-c','import pydicom, numpy, PIL']);$probe->run();
        if(!$probe->isSuccessful()) $this->markTestSkipped('Install the documented DICOM worker dependencies to run pixel/SR integration tests.');
        $worker=app(MedicalImagingDicom::class);
        // Use a known valid synthetic PNG produced by the worker test generator.
        $process=new \Symfony\Component\Process\Process([config('medical_imaging.python'),'-c',"import io,base64;from PIL import Image;b=io.BytesIO();Image.new('RGB',(4,4),(30,60,90)).save(b,format='PNG');print(base64.b64encode(b.getvalue()).decode())"]);$process->mustRun();$png=trim($process->getOutput());
        $result=$worker->run('inspect',['mime'=>'image/png','content'=>$png,'study_uid'=>$study->study_uid,'series_uid'=>$worker->uid(),'sop_uid'=>$worker->uid(),'exam_date'=>'20261001','patient'=>$study->source_identity]);$md=$result['metadata'];
        return $study->instances()->create(['original_name'=>'fixture.png','mime_type'=>'application/dicom','sha256'=>hash('sha256',$result['content']),'series_uid'=>$md['series_uid'],'sop_uid'=>$md['sop_uid'],'sop_class_uid'=>$md['sop_class_uid'],'metadata'=>$md,'content'=>$result['content'],'uploaded_by'=>1]);
    }
    public function test_real_worker_frame_sr_pdf_and_pacs_receipt():void {
        $study=$this->study();$image=$this->realImage($study);
        $this->get(route('medical-imaging.frame',[873,$study,$image]))->assertOk()->assertHeader('Content-Type','image/png')->assertHeader('Cache-Control','no-store, private');
        $this->get(route('medical-imaging.image',[873,$study,$image]))->assertOk()->assertHeader('Content-Type','application/dicom');
        $report=$this->save($study,$image,['reference_images'=>json_encode([['instance_id'=>$image->id,'frame'=>0]])]);
        $this->post(route('medical-imaging.report.validate',[873,$study,$report]),['confirm'=>1,'edit_revision'=>1])->assertSessionHasNoErrors();
        $export=$this->get(route('medical-imaging.report.dicom',[873,$study,$report]));$export->assertOk()->assertHeader('Content-Type','application/dicom');$this->assertEquals('DICM',substr($export->getContent(),128,4));
        $this->get(route('medical-imaging.report.pdf',[873,$study,$report]))->assertOk()->assertHeader('Content-Type','application/pdf');
        config(['medical_imaging.pacs_url'=>'https://pacs.example.test/dicom-web']);
        Http::fake(['*'=>Http::response(['00081199'=>['vr'=>'SQ','Value'=>array_map(fn($uid)=>['00081155'=>['vr'=>'UI','Value'=>[$uid]]],[$image->sop_uid,$report->sop_uid])]],200)]);
        $this->post(route('medical-imaging.report.pacs',[873,$study,$report]),['confirm_pacs'=>1])->assertSessionHas('success');$this->assertEquals('stored',$report->fresh()->pacs_status);
        Http::swap(new \Illuminate\Http\Client\Factory());
        Http::fake(['*'=>Http::response(['00081199'=>['vr'=>'SQ','Value'=>[['00081155'=>['vr'=>'UI','Value'=>[$report->sop_uid]]]]]],200)]);
        $this->post(route('medical-imaging.report.pacs',[873,$study,$report]),['confirm_pacs'=>1])->assertSessionHas('error');$this->assertEquals('failed',$report->fresh()->pacs_status);
    }
    public function test_pacs_is_disabled_without_configuration_and_sends_nothing():void {
        Http::fake();$study=$this->study();$image=$this->image($study);$report=$this->save($study,$image);
        $report->update(['status'=>'validated','validated_at'=>now(),'validated_by'=>1]);
        $this->post(route('medical-imaging.report.pacs',[873,$study,$report]),['confirm_pacs'=>1])->assertStatus(503);Http::assertNothingSent();
    }
    public function test_uploaded_dicom_keeps_source_identity_and_u17_rejects_secondary_capture():void {
        $source=$this->study();$image=$this->realImage($source);$target=$this->study();
        $path=tempnam(sys_get_temp_dir(),'fit-dicom-upload-');file_put_contents($path,base64_decode($image->content));
        $makeFile=fn()=>new \Illuminate\Http\UploadedFile($path,'source.dcm','application/dicom',null,true);
        // Release the fixture UID to exercise an actual upload into another examination.
        $image->delete();
        try {
            $this->post(route('medical-imaging.upload',[873,$target]),['images'=>[$makeFile()]])->assertSessionHasErrors('images');
            $sourceUid=$source->study_uid;$source->update(['study_uid'=>app(MedicalImagingDicom::class)->uid()]);
            $this->post(route('medical-imaging.upload',[873,$target]),['images'=>[$makeFile()]])->assertSessionHasNoErrors()->assertRedirect();
            $this->assertFalse($target->fresh()->identity_checked);
            $this->assertEquals($sourceUid,$target->fresh()->study_uid);
            $import=$target->instances()->firstOrFail();$this->assertEquals($image->content,$import->content);
            $u17=$this->study('age_u17');
            $this->post(route('medical-imaging.upload',[873,$u17]),['images'=>[$makeFile()]])->assertSessionHasErrors('images');
            $this->assertEquals(0,$u17->instances()->count());
        } finally {unlink($path);}
    }
    public function test_validated_clinical_snapshot_and_observer_survive_profile_changes():void {
        $study=$this->study();$image=$this->image($study);$report=$this->save($study,$image);
        $this->post(route('medical-imaging.report.validate',[873,$study,$report]),['confirm'=>1,'edit_revision'=>1])->assertSessionHasNoErrors();
        DB::table('players')->where('id',1)->update(['name'=>'Changed player','date_of_birth'=>'2008-01-01']);
        DB::table('users')->where('id',1)->update(['name'=>'Changed doctor']);
        $payload=app(\App\Services\ImagingReportExporter::class)->payload($report->fresh());
        $this->assertEquals('20100201',$payload['patient']['birth_date']);$this->assertEquals('Fixture Doctor',$payload['validator']);
        $this->assertEquals('2010-02-01',$report->fresh()->patient_snapshot['birth_date']);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $report->fresh()->update(['conclusion'=>'Overwrite validated report']);
    }

    public function test_large_pacs_batch_is_rejected_before_loading_pixels_or_sending():void {
        Http::fake();config(['medical_imaging.pacs_url'=>'https://pacs.example.test/dicom-web']);
        $study=$this->study();$image=$this->image($study);$image->update(['metadata'=>$image->metadata+['content_bytes'=>200*1024*1024]]);
        $report=$this->save($study,$image);$this->post(route('medical-imaging.report.validate',[873,$study,$report]),['confirm'=>1,'edit_revision'=>1])->assertSessionHasNoErrors();
        $this->post(route('medical-imaging.report.pacs',[873,$study,$report]),['confirm_pacs'=>1])->assertSessionHasErrors('pacs');Http::assertNothingSent();
    }

}
