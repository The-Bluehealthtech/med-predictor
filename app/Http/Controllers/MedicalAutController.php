<?php
namespace App\Http\Controllers;
use App\Models\{HealthRecord, TUERequest, MedicalAutDocument};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
final class MedicalAutController extends Controller
{
    private function record($id): HealthRecord
    {
        return app(HealthcareController::class)->query()->with('player.club')->findOrFail($id);
    }
    private function item(HealthRecord $record,$id): TUERequest
    {
        return TUERequest::where('health_record_id',$record->id)->where('player_id',$record->player_id)->findOrFail($id);
    }
    public function choose()
    {
        $records=app(HealthcareController::class)->query()->with('player.club')
            ->orderByDesc('record_date')->orderByDesc('id')->paginate(25);
        return view('health-records.aut-choose',compact('records'));
    }
    public function index($record)
    {
        $healthRecord=$this->record($record);
        $requests=TUERequest::where('health_record_id',$healthRecord->id)->where('player_id',$healthRecord->player_id)->latest('id')->get();
        return view('health-records.aut-index',compact('healthRecord','requests'));
    }
    public function create($record)
    {
        $healthRecord=$this->record($record); $item=null;
        $sourceText=$this->sourceText();
        return view('health-records.aut-form',compact('healthRecord','item','sourceText'));
    }
    public function edit($record,$aut)
    {
        $healthRecord=$this->record($record);$item=$this->item($healthRecord,$aut);
        $sourceText=$this->sourceText();
        // Une décision externe historique ne doit pas être modifiée par ce formulaire.
        abort_unless($item->status==='pending',409,__('medical_aut.locked'));
        return view('health-records.aut-form',compact('healthRecord','item','sourceText'));
    }
    public function store(Request $request,$record)
    {
        return $this->save($request,$this->record($record));
    }
    public function update(Request $request,$record,$aut)
    {
        $healthRecord=$this->record($record);$item=$this->item($healthRecord,$aut);
        abort_unless($item->status==='pending',409,__('medical_aut.locked'));
        return $this->save($request,$healthRecord,$item);
    }
    public function validateDraft(Request $request,string $formKey='form',string $documentKey='documents'): array
    {
        $fields=collect(config('medical_aut.sections'))->flatMap(fn($s)=>$s['fields']);
        $keys=$fields->map(fn($f)=>$f[0])->all();
        $rules=[$formKey=>'required|array:'.implode(',',$keys),
            $documentKey=>'nullable|array|max:10',$documentKey.'.*'=>'file|mimes:pdf,png,jpg,jpeg|max:'.config('medical_aut.max_file_kb')];
        foreach($fields as $f){
            $type=$f[3]??'text';
            $rules[$formKey.'.'.$f[0]]=match($type){
                'date'=>'nullable|date','email'=>'nullable|email|max:255',
                'select'=>'nullable|in:'.implode(',',array_keys($f[4])),
                default=>'nullable|string|max:20000',
            };
        }
        $data=$request->validate($rules);
        return ['form'=>$data[$formKey],'documents_key'=>$documentKey];
    }
    public function persistDraft(Request $request,HealthRecord $record,array $data,?TUERequest $item=null): TUERequest
    {
        app(\App\Services\MedicalRecordAccess::class)->authorize(auth()->user(),$record->player,null);
        if($item)abort_unless($item->status==='pending' && (int)$item->health_record_id===(int)$record->id,409,__('medical_aut.locked'));
        return DB::transaction(function()use($request,$record,$item,$data){
            $item=$item??new TUERequest;
            $metadata=$item->aut_form_data??[];
            $metadata['source']=config('medical_aut.source');
            $metadata['version']=config('medical_aut.form_version');
            $metadata['source_sha256']=config('medical_aut.source_sha256');
            $metadata['fields']=$data['form'];
            $metadata['substance_reference']=app(\App\Services\AutSubstanceReference::class)->provenance($data['form']);
            $metadata['updated_by']=auth()->id();
            // Aucun consentement, signature ou dépôt externe déduit des champs saisis.
            $item->fill(['player_id'=>$record->player_id,'health_record_id'=>$record->id,
                'medication'=>$data['form']['substance_1']??null,'reason'=>$data['form']['diagnosis']??null,
                'aut_form_data'=>$metadata]);
            if(!$item->exists)$item->fill(['status'=>'pending','physician_id'=>auth()->id(),'request_date'=>today()]);
            $item->save();$documents=$item->supporting_documents??[];
            foreach($request->file($data['documents_key'],[]) as $file){
                $bytes=$file->get();
                $document=MedicalAutDocument::create(['tue_request_id'=>$item->id,
                    'original_name'=>mb_substr($file->getClientOriginalName(),0,255),
                    'mime_type'=>$file->getMimeType(),'sha256'=>hash('sha256',$bytes),
                    'content'=>base64_encode($bytes)]);
                $documents[]=['document_id'=>$document->id,'name'=>$document->original_name,'uploaded_by'=>auth()->id()];
            }
            $item->supporting_documents=$documents;$item->save();return $item;
        });

    }
    private function save(Request $request,HealthRecord $record,?TUERequest $item=null)
    {
        $this->persistDraft($request,$record,$this->validateDraft($request),$item);
        return redirect()->route('medical-aut.index',$record->id)->with('success',__('medical_aut.saved'));
    }
    public function document($record,$aut,$index)
    {
        $healthRecord=$this->record($record);$item=$this->item($healthRecord,$aut);
        abort_unless(ctype_digit((string)$index),404);
        $file=$item->supporting_documents[(int)$index]??null;
        abort_unless(is_array($file)&&isset($file['document_id']),404);
        $document=MedicalAutDocument::where('tue_request_id',$item->id)->findOrFail($file['document_id']);
        try {$bytes=base64_decode($document->content,true);}
        catch(\Illuminate\Contracts\Encryption\DecryptException $e){abort(503,__('medical_aut.storage_error'));}
        abort_unless(is_string($bytes)&&hash_equals($document->sha256,hash('sha256',$bytes)),503,__('medical_aut.storage_error'));
        $ext=match($document->mime_type){'application/pdf'=>'pdf','image/png'=>'png','image/jpeg'=>'jpg',default=>'bin'};
        return response($bytes,200,['Content-Type'=>'application/octet-stream',
            'Content-Disposition'=>'attachment; filename="aut-document-'.((int)$index+1).'.'.$ext.'"',
            'Cache-Control'=>'private, no-store','X-Content-Type-Options'=>'nosniff']);
    }
    private function sourceText(): array
    {
        return json_decode(file_get_contents(config('medical_aut.source_directory').'/fifa-aut-fr-2024-text.json'),true);
    }

    public function blankSource(Request $request)
    {
        app(\App\Services\MedicalRecordAccess::class)->authorizeRole($request->user());
        return response()->download(config('medical_aut.source_directory').'/fifa-aut-fr-2024.pdf','FIFA-AUT-FR-2024.pdf',
            ['Cache-Control'=>'private, no-store','X-Content-Type-Options'=>'nosniff']);
    }
    public function source($record)
    {
        $this->record($record);
        return response()->download(config('medical_aut.source_directory').'/fifa-aut-fr-2024.pdf','FIFA-AUT-FR-2024.pdf',
            ['Cache-Control'=>'private, no-store','X-Content-Type-Options'=>'nosniff']);
    }
}
