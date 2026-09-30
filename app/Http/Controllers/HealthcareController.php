<?php
namespace App\Http\Controllers;
use App\Models\{HealthRecord, MedicalPrediction};
use App\Services\MedicalRecordAccess;
use Illuminate\Http\Request;

final class HealthcareController extends Controller
{
    // Un seul périmètre médical pour liste, détails, modifications et exports.
    public function query()
    {
        $access=app(MedicalRecordAccess::class); $user=auth()->user();
        $access->authorizeRole($user);
        return HealthRecord::query()->whereHas('player',fn($p)=>$access->scopePlayers($user,$p));
    }
    public function index()
    {
        $healthRecords=$this->query()->with(['player','user','predictions'])->orderByDesc('record_date')->paginate(20);
        return view('modules.healthcare.index',compact('healthRecords'));
    }
    public function show($record)
    {
        return app(HealthRecordController::class)->show($this->query()->findOrFail($record));
    }
    public function edit($record)
    {
        return app(HealthRecordController::class)->edit($this->query()->findOrFail($record));
    }
    public function update(Request $request,$record)
    {
        return app(HealthRecordController::class)->update($request,$this->query()->findOrFail($record));
    }
    public function destroy($record)
    {
        return app(HealthRecordController::class)->destroy($this->query()->findOrFail($record));
    }
    public function predictions()
    {
        // Historique conservé, explicitement non validé ; aucun score médical inventé.
        $ids=$this->query()->select('health_records.id');
        $predictions=MedicalPrediction::whereIn('health_record_id',$ids)
            ->with(['player','healthRecord'])->orderByDesc('prediction_date')->paginate(20);
        return view('modules.healthcare.predictions',compact('predictions'));
    }
    public function export(Request $request)
    {
        $this->query(); // Autoriser avant de proposer ou de lancer le téléchargement.
        if(!$request->boolean('download')) return view('modules.healthcare.export');
        return response()->streamDownload(function(){
            $stream=fopen('php://output','w');
            $available=\Illuminate\Support\Facades\Schema::getColumnListing('health_records');
            $fields=array_values(array_filter((new HealthRecord)->getFillable(),fn($f)=>in_array($f,$available,true)&&!in_array($f,['risk_score','prediction_confidence'],true)&&!str_ends_with($f,'_path')));
            $fields=array_values(array_unique(array_merge(['player_id','record_date','status','diagnosis'],$fields)));
            fputcsv($stream,array_merge(['record_id'],$fields),',','"','');
            $this->query()->orderBy('health_records.id')->chunkById(200,function($records)use($stream,$fields){
                foreach($records as $record){
                    $row=[$record->id];
                    foreach($fields as $field){
                        $value=$record->$field;
                        $row[]=is_array($value)?json_encode($value,JSON_UNESCAPED_UNICODE):($value instanceof \DateTimeInterface?$value->format('c'):$value);
                    }
                    // Neutraliser les formules de tableur ; conserver la valeur clinique en base.
                    $row=array_map(fn($v)=>is_string($v)&&preg_match('/^[\\s]*[=+@-]/u',$v)?"'".$v:$v,$row);
                    fputcsv($stream,$row,',','"','');
                }
            });
            fclose($stream);
        },'healthcare-records.csv',['Content-Type'=>'text/csv; charset=UTF-8','Cache-Control'=>'private, no-store']);
    }
}
