<?php
namespace App\Http\Controllers;
use App\Models\{HealthRecord,ImagingStudy,ImagingInstance,ImagingReport};
use App\Services\{MedicalRecordAccess,MedicalImagingDicom,ImagingReportExporter,AgeVerificationService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Schema,Http};
use Illuminate\Validation\ValidationException;

final class MedicalImagingController extends Controller {
    private function authorizeRecord(HealthRecord $record, ?ImagingStudy $study=null): void {
        app(MedicalRecordAccess::class)->authorize(auth()->user(),$record->player,null);
        if ($study) abort_unless((int)$study->player_id===(int)$record->player_id,404);
    }
    private function available(): void { abort_unless(Schema::hasTable('fit_imaging_reports'),503,'Le module d’imagerie attend sa migration de base de données.'); }
    public function index(HealthRecord $healthRecord) {
        $this->authorizeRecord($healthRecord); $this->available(); $healthRecord->load('player.club');
        $studies=ImagingStudy::where('player_id',$healthRecord->player_id)->withCount('instances')->with('reports')->orderByDesc('exam_date')->paginate(15);
        $ageVigilance=app(AgeVerificationService::class)->assess($healthRecord->player);
        return view('health-records.imaging.index',compact('healthRecord','studies','ageVigilance'));
    }
    public function create(Request $request, HealthRecord $healthRecord) {
        $this->authorizeRecord($healthRecord); $this->available();
        $data=$request->validate(['exam_date'=>'required|date|before_or_equal:today','modality'=>'required|in:MR,CT,CR,DX,US,NM,OT',
            'purpose'=>'required|in:general,age_u17','body_region'=>'required|string|max:120','source'=>'required|string|max:180','indication'=>'nullable|string|max:6000']);
        if ($data['purpose']==='age_u17') {
            $data['modality']='MR'; $data['body_region']='Poignet / radius distal';
        }
        $study=ImagingStudy::create($data+['health_record_id'=>$healthRecord->id,'player_id'=>$healthRecord->player_id,'created_by'=>auth()->id(),'study_uid'=>app(MedicalImagingDicom::class)->uid()]);
        return redirect()->route('medical-imaging.show',[$healthRecord,$study])->with('success','Examen créé. Ajoutez les images puis vérifiez leur identité.');
    }
    public function show(Request $request,HealthRecord $healthRecord,ImagingStudy $study) {
        $this->authorizeRecord($healthRecord,$study); $study->load(['instances'=>fn($q)=>$q->metadataOnly(),'reports.validator','player']);
        $report=$study->reports->first(); $revision=$request->boolean('revise') && $report?->status==='validated';
        $documentSignatureProviders=collect(app(\App\Services\Documents\DocumentSignatureService::class)->allStatuses());
        $documentSignatureRequests=collect();
        if ($report?->status==='validated' && Schema::hasTable('document_signature_requests')) {
            $documentSignatureRequests=\App\Models\DocumentSignatureRequest::query()->where('workflow','imaging_report.final_document')->latest('id')->get()
                ->filter(fn($item)=>(int)data_get($item->metadata,'document.report_id')===(int)$report->id)->values();
        }
        $canSignReport=$report?->status==='validated' && auth()->user()->hasAnyRole(['doctor','team_doctor','club_medical','association_medical']);
        return view('health-records.imaging.show',compact('healthRecord','study','report','revision','documentSignatureProviders','documentSignatureRequests','canSignReport'));
    }
    public function upload(Request $request,HealthRecord $healthRecord,ImagingStudy $study) {
        $this->authorizeRecord($healthRecord,$study);
        $request->validate(['images'=>'required|array|min:1|max:'.config('medical_imaging.max_files'),'images.*'=>'required|file|max:'.config('medical_imaging.max_file_kb')]);
        $files=$request->file('images');
        if (array_sum(array_map(fn($f)=>$f->getSize(),$files))>1024*config('medical_imaging.max_batch_kb')) throw ValidationException::withMessages(['images'=>'Maximum 100 Mo par import.']);
        $worker=app(MedicalImagingDicom::class); $prepared=[]; $preparedBytes=0;
        foreach ($files as $file) {
            $mime=$file->getMimeType(); $extension=strtolower($file->getClientOriginalExtension());
            if (!in_array($mime,['image/jpeg','image/png'],true) && !in_array($extension,['dcm','dicom'],true)) throw ValidationException::withMessages(['images'=>'Utilisez des fichiers DICOM (.dcm), JPEG ou PNG.']);
            $mime=in_array($mime,['image/jpeg','image/png'],true)?$mime:'application/dicom';
            $result=$worker->run('inspect',['mime'=>$mime,'content'=>base64_encode(file_get_contents($file->getRealPath())),
                'study_uid'=>$study->study_uid,'series_uid'=>$worker->uid(),'sop_uid'=>$worker->uid(),'exam_date'=>$study->exam_date->format('Ymd'),
                'patient'=>['id'=>'FIT-'.$study->player_id,'name'=>$study->player->full_name??$study->player->name,'birth_date'=>$study->player->date_of_birth?->format('Ymd')??'','sex'=>'']]);
            $preparedBytes+=(int)$result['metadata']['content_bytes'];
            if ($preparedBytes>1024*config('medical_imaging.max_batch_kb')) throw ValidationException::withMessages(['images'=>'Les objets DICOM après conversion dépassent 100 Mo. Réduisez cet import.']);
            $prepared[]=['original_name'=>mb_substr(basename($file->getClientOriginalName()),0,200),'metadata'=>$result['metadata'],'content'=>$result['content']];
        }
        DB::transaction(function() use($study,$prepared) {
            $locked=ImagingStudy::whereKey($study->id)->lockForUpdate()->firstOrFail();
            abort_if($locked->reports()->where('status','validated')->exists(),409,'L’examen contient un rapport validé. Créez un autre examen pour importer de nouvelles images.');
            $hasImages=$locked->instances()->exists();
            if ($locked->instances()->count()+count($prepared)>500) throw ValidationException::withMessages(['images'=>'Maximum 500 objets par examen.']);
            foreach ($prepared as $item) {
                $md=$item['metadata'];
                if ($locked->purpose==='age_u17' && $md['modality']!=='MR') throw ValidationException::withMessages(['images'=>'Le contrôle U-17 nécessite une série IRM DICOM.']);
                if (!$hasImages) {
                    if (ImagingStudy::where('study_uid',$md['study_uid'])->where('id','!=',$locked->id)->exists()) throw ValidationException::withMessages(['images'=>'Cette étude DICOM existe déjà dans FIT. Ouvrez l’examen existant pour poursuivre la lecture.']);
                    if (!$md['synthetic_source']) $locked->modality=$md['modality'];
                    $locked->study_uid=$md['study_uid']; $locked->source_identity=$md['patient']; $hasImages=true;
                }
                if ($locked->study_uid!==$md['study_uid'] || $locked->source_identity!==$md['patient']) throw ValidationException::withMessages(['images'=>'Ces images appartiennent à des études ou identités différentes. Importez chaque étude séparément.']);
                $sha=hash('sha256',base64_decode($item['content'],true));
                if ($locked->instances()->where('sha256',$sha)->exists()) continue;
                if (ImagingInstance::where('sop_uid',$md['sop_uid'])->exists()) throw ValidationException::withMessages(['images'=>'Cet identifiant d’image est déjà utilisé dans FIT.']);
                $locked->instances()->create($item+['mime_type'=>'application/dicom','sha256'=>$sha,'series_uid'=>$md['series_uid'],'sop_uid'=>$md['sop_uid'],'sop_class_uid'=>$md['sop_class_uid'],'uploaded_by'=>auth()->id()]);
            }
            $locked->identity_checked=false; $locked->identity_checked_by=null; $locked->identity_checked_at=null; $locked->save();
        });
        return back()->with('success','Images importées. Vérifiez les informations d’identité affichées avant de valider le compte rendu.');
    }
    private function instance(HealthRecord $record,ImagingStudy $study,ImagingInstance $instance): void {
        $this->authorizeRecord($record,$study); abort_unless((int)$instance->study_id===(int)$study->id,404);
    }
    private function privateResponse(string $bytes,string $mime) {
        return response($bytes)->header('Content-Type',$mime)->header('Cache-Control','private, no-store')->header('X-Content-Type-Options','nosniff');
    }
    public function frame(Request $request,HealthRecord $healthRecord,ImagingStudy $study,ImagingInstance $instance) {
        $this->instance($healthRecord,$study,$instance);
        $data=$request->validate(['frame'=>'sometimes|integer|min:0','center'=>'nullable|numeric|between:-100000,100000','width'=>'nullable|numeric|between:1,200000']);
        abort_if(($data['frame']??0)>=($instance->metadata['frames']??1),422);
        try { $result=app(MedicalImagingDicom::class)->run('render',$data+['content'=>$instance->content]); }
        catch (ValidationException $e) { return response()->json(['message'=>'Cette image ne peut pas être affichée par le décodeur installé. Ouvrez le fichier source sur une station adaptée.'],422); }
        return $this->privateResponse(base64_decode($result['content'],true),'image/png');
    }
    public function image(HealthRecord $healthRecord,ImagingStudy $study,ImagingInstance $instance) {
        $this->instance($healthRecord,$study,$instance);
        return $this->privateResponse(base64_decode($instance->content,true),'application/dicom')->header('Content-Disposition','attachment; filename="image-'.$instance->id.'.dcm"');
    }
    public function identity(Request $request,HealthRecord $healthRecord,ImagingStudy $study) {
        $this->authorizeRecord($healthRecord,$study);
        $data=$request->validate(['identity_checked'=>'accepted','identity_note'=>'required|string|min:5|max:2000']);
        DB::transaction(function() use($study,$data) {
            $locked=ImagingStudy::whereKey($study->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->instances()->exists(),422,'Importez les images avant la vérification.');
            $locked->update(['identity_checked'=>true,'identity_note'=>$data['identity_note'],'identity_checked_by'=>auth()->id(),'identity_checked_at'=>now()]);
        });
        return back()->with('success','Vérification d’identité enregistrée avec sa justification.');
    }
    private function reportData(Request $request,ImagingStudy $study): array {
        $data=$request->validate(['report_id'=>'nullable|integer','edit_revision'=>'nullable|integer',
            'technique'=>'nullable|string|max:6000','quality'=>'required|in:interpretable,limited,uninterpretable',
            'findings'=>'nullable|string|max:20000','conclusion'=>'nullable|string|max:12000',
            'reference_images'=>'nullable|json|max:40000','age'=>'nullable|array',
            'age.population'=>'nullable|in:male,female,unspecified','age.grade'=>'nullable|integer|between:1,6',
            'age.second_grade'=>'nullable|integer|between:1,6','age.second_reader'=>'nullable|string|max:180','age.second_source'=>'nullable|string|max:500',
            'age.competition'=>'nullable|string|max:180','age.regulation'=>'nullable|string|max:500','age.cutoff_birth_date'=>'nullable|date_format:Y-m-d',
            'age.context'=>'nullable|string|max:3000','age.disagreement_note'=>'nullable|string|max:2000','age.consent'=>'nullable|boolean',
            'age.evidence'=>'nullable|array|max:10','age.evidence.*.source'=>'nullable|string|max:180','age.evidence.*.reference'=>'nullable|string|max:500','age.evidence.*.birth_date'=>'nullable|date_format:Y-m-d']);
        $refs=json_decode($data['reference_images']??'[]',true);
        \Validator::make(['refs'=>$refs],['refs'=>'array|max:100','refs.*.instance_id'=>'required|integer','refs.*.frame'=>'required|integer|min:0',
            'refs.*.points'=>'nullable|array|size:4','refs.*.points.*'=>'required|numeric'])->validate();
        $images=$study->instances()->metadataOnly()->get(); $clean=[];
        foreach($refs as $ref) {
            $image=$images->firstWhere('id',(int)$ref['instance_id']);
            abort_unless($image && (int)$ref['frame']<($image->metadata['frames']??1),422,'Image de référence incorrecte.');
            $entry=['instance_id'=>$image->id,'frame'=>(int)$ref['frame']];
            if (!empty($ref['points'])) {
                $points=array_map('floatval',$ref['points']); $md=$image->metadata;
                foreach ($points as $i=>$value) abort_unless(is_finite($value) && $value>=1 && $value<=($i%2===0?$md['columns']:$md['rows']),422,'Coordonnées hors image.');
                $entry['points']=$points;
                $spacing=$md['frame_pixel_spacing'][(int)$ref['frame']]??$md['pixel_spacing']??[];
                if (count($spacing)===2 && $spacing[0]>0 && $spacing[1]>0) $entry['length_mm']=sqrt(pow(($points[2]-$points[0])*$spacing[1],2)+pow(($points[3]-$points[1])*$spacing[0],2));
            }
            $clean[]=$entry;
        }
        $age=$study->purpose==='age_u17'?($data['age']??[]):null;
        if ($age!==null) {
            $age['declared_birth_date']=$study->player->date_of_birth?->format('Y-m-d');
            $age['evidence']=array_values(array_filter($age['evidence']??[],fn($e)=>!empty($e['source'])||!empty($e['birth_date'])||!empty($e['reference'])));
            foreach($age['evidence'] as $e) \Validator::make($e,['source'=>'required','reference'=>'required','birth_date'=>'required'])->validate();
        }
        return ['patient_snapshot'=>['id'=>$study->player_id,'name'=>$study->player->full_name??$study->player->name,'birth_date'=>$study->player->date_of_birth?->format('Y-m-d')],'technique'=>$data['technique']??null,'quality'=>$data['quality'],'findings'=>$data['findings']??null,
            'conclusion'=>$data['conclusion']??null,'reference_images'=>$clean,'age_review'=>$age];
    }
    public function saveReport(Request $request,HealthRecord $healthRecord,ImagingStudy $study) {
        $this->authorizeRecord($healthRecord,$study); $data=$this->reportData($request,$study);
        DB::transaction(function()use($request,$study,$data) {
            $locked=ImagingStudy::whereKey($study->id)->lockForUpdate()->firstOrFail();
            if ($request->filled('report_id')) {
                $report=$locked->reports()->whereKey($request->integer('report_id'))->lockForUpdate()->firstOrFail();
                abort_unless($report->status==='draft',409,'Créez une nouvelle version du rapport validé.');
                abort_unless((int)$request->input('edit_revision')===(int)$report->edit_revision,409,'Ce brouillon a été modifié. Rechargez avant de continuer.');
                $report->update($data+['edit_revision'=>$report->edit_revision+1]);
            } else {
                abort_if($locked->reports()->where('status','draft')->exists(),409,'Un brouillon existe déjà. Rechargez cet examen.');
                $locked->reports()->create($data+['version'=>($locked->reports()->max('version')??0)+1,'authored_by'=>auth()->id(),'sop_uid'=>app(MedicalImagingDicom::class)->uid()]);
            }
        });
        return redirect()->route('medical-imaging.show',[$healthRecord,$study])->with('success','Brouillon enregistré. Relisez-le avant la validation médicale.');
    }
    public function validateReport(Request $request,HealthRecord $healthRecord,ImagingStudy $study,ImagingReport $report) {
        $this->authorizeRecord($healthRecord,$study); abort_unless((int)$report->study_id===(int)$study->id,404);
        abort_unless(auth()->user()->hasAnyRole(['doctor','team_doctor','club_medical','association_medical']),403,'La validation est réservée aux rôles médecins.');
        $request->validate(['confirm'=>'accepted','edit_revision'=>'required|integer']);
        DB::transaction(function()use($request,$study,$report) {
            $lockedStudy=ImagingStudy::whereKey($study->id)->lockForUpdate()->firstOrFail();
            $locked=ImagingReport::whereKey($report->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status==='draft' && (int)$request->input('edit_revision')===(int)$locked->edit_revision,409,'Le compte rendu a changé. Rechargez-le.');
            $errors=[];
            if (!$lockedStudy->identity_checked) $errors['identity']='Vérifiez et documentez l’identité des images.';
            if (!$locked->technique || !$locked->findings || !$locked->conclusion) $errors['report']='Complétez la technique, les observations et la conclusion.';
            if (!$locked->reference_images) $errors['references']='Sélectionnez au moins une image de référence dans le lecteur.';
            if ($lockedStudy->purpose==='age_u17') {
                $age=$locked->age_review??[];
                if (empty($age['consent'])) $errors['consent']='Documentez l’information et le consentement pour ce contrôle.';
                if (empty($age['population'])) $errors['population']='Renseignez la population concernée.';
                if (($age['population']??'')!=='male' && (!empty($age['grade']) || !empty($age['second_grade']))) $errors['grade']='Le protocole masculin ne peut pas être appliqué à cette population.';
                if ($locked->quality==='uninterpretable' && (!empty($age['grade']) || !empty($age['second_grade']))) $errors['grade']='Une IRM non interprétable ne peut pas recevoir de grade.';
                if (($age['population']??'')==='male' && $locked->quality==='interpretable' && empty($age['grade'])) $errors['grade']='Renseignez le grade radiologique.';
                if (!empty($age['second_grade']) && (empty($age['second_reader']) || empty($age['second_source']))) $errors['second_reader']='Identifiez le second lecteur et la référence de sa lecture.';
                if (!empty($age['second_grade']) && $age['second_grade']!=$age['grade'] && empty($age['disagreement_note'])) $errors['disagreement']='Documentez la gestion du désaccord de lecture.';
                if (!empty($age['cutoff_birth_date']) && (empty($age['competition']) || empty($age['regulation']))) $errors['regulation']='Indiquez la compétition et la version du règlement.';
            }
            if ($errors) throw ValidationException::withMessages($errors);
            $locked->update(['status'=>'validated','validated_by'=>auth()->id(),'validator_name'=>auth()->user()->name,'validated_at'=>now()]);
        });
        return back()->with('success','Compte rendu validé et conservé. Les exports sont disponibles.');
    }
    private function reportAccess(HealthRecord $record,ImagingStudy $study,ImagingReport $report): void {
        $this->authorizeRecord($record,$study); abort_unless((int)$report->study_id===(int)$study->id,404); abort_unless($report->status==='validated',409,'Compte rendu non validé.');
    }
    public function export(HealthRecord $healthRecord,ImagingStudy $study,ImagingReport $report) {
        $this->reportAccess($healthRecord,$study,$report);
        return $this->privateResponse(app(ImagingReportExporter::class)->dicom($report),'application/dicom')->header('Content-Disposition','attachment; filename="FIT-imaging-'.$study->id.'-v'.$report->version.'.dcm"');
    }
    public function pdf(HealthRecord $healthRecord,ImagingStudy $study,ImagingReport $report) {
        $this->reportAccess($healthRecord,$study,$report);
        $study->load('player'); $report->load('validator');
        return app('dompdf.wrapper')->loadView('health-records.imaging.report-pdf',compact('study','report'))->download('FIT-imaging-'.$study->id.'-v'.$report->version.'.pdf')->header('Cache-Control','private, no-store');
    }
    public function pacs(Request $request,HealthRecord $healthRecord,ImagingStudy $study,ImagingReport $report) {
        $this->reportAccess($healthRecord,$study,$report); $request->validate(['confirm_pacs'=>'accepted']);
        $url=config('medical_imaging.pacs_url');
        abort_unless($url && parse_url($url,PHP_URL_SCHEME)==='https',503,'Connexion PACS HTTPS non configurée.');
        // Only the deployment-configured endpoint is accepted; never a request-supplied URL.
        $boundary='fit-'.bin2hex(random_bytes(12)); $body=''; $images=$study->instances()->metadataOnly()->whereIn('id',array_column($report->reference_images,'instance_id'))->get();
        $limit=1024*config('medical_imaging.max_batch_kb');
        if ($images->sum(fn($image)=>(int)($image->metadata['content_bytes']??0))>$limit) throw ValidationException::withMessages(['pacs'=>'Les images de référence dépassent 100 Mo. Réduisez les références dans une nouvelle version ou transférez les sources depuis votre station PACS.']);
        foreach ($images as $image) {
            $bytes=base64_decode(ImagingInstance::findOrFail($image->id)->content,true);
            if (strlen($body)+strlen($bytes)+65536>$limit) throw ValidationException::withMessages(['pacs'=>'Transmission limitée à 100 Mo. Réduisez les références dans une nouvelle version.']);
            $body.="--$boundary\r\nContent-Type: application/dicom\r\n\r\n".$bytes."\r\n";
        }
        unset($bytes);
        $body.="--$boundary\r\nContent-Type: application/dicom\r\n\r\n".app(ImagingReportExporter::class)->dicom($report)."\r\n--$boundary--\r\n";
        $client=Http::timeout(90)->withOptions(['allow_redirects'=>false])->accept('application/dicom+json');
        if(config('medical_imaging.pacs_token')) $client=$client->withToken(config('medical_imaging.pacs_token'));
        try {
            $response=$client->withBody($body,'multipart/related; type="application/dicom"; boundary='.$boundary)->post(rtrim($url,'/').'/studies');
            $payload=$response->json(); $stored=array_map(fn($x)=>$x['00081155']['Value'][0]??'', $payload['00081199']['Value']??[]);
            $expected=$images->pluck('sop_uid')->push($report->sop_uid)->all();
            $ok=$response->successful() && empty($payload['00081198']['Value']) && !array_diff($expected,$stored);
        } catch (\Throwable $e) { $ok=false; }
        $report->update(['pacs_status'=>$ok?'stored':'failed','pacs_sent_at'=>$ok?now():null]);
        return back()->with($ok?'success':'error',$ok?'Le PACS a confirmé la réception des images référencées et du rapport.':'Réception PACS non confirmée pour tous les objets. Le rapport est conservé dans FIT ; vérifiez la connexion.');
    }
}
