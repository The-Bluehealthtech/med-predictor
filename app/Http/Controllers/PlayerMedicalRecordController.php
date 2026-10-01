<?php
namespace App\Http\Controllers;
use App\Models\{HealthRecord,HealthRecordDocument};
use App\Services\{HealthRecordSections,MedicalRecordAccess};
use Illuminate\Support\Facades\Schema;
final class PlayerMedicalRecordController extends Controller
{
    private function record(int $id): HealthRecord
    {
        $user=auth()->user(); abort_unless($user,401);
        // Le compte joueur est lié au player_id canonique, jamais à une identité fournie par le client.
        if($user->isPlayer()) {
            abort_unless($user->player_id,403);
            return HealthRecord::withoutGlobalScopes()->where('player_id',$user->player_id)->findOrFail($id);
        }
        $access=app(MedicalRecordAccess::class); $access->authorizeRole($user);
        $record=HealthRecord::findOrFail($id); $access->authorize($user,$record->player,null);
        return $record;
    }
    public function show(int $record)
    {
        $healthRecord=$this->record($record);
        $records=HealthRecord::withoutGlobalScopes()->where('player_id',$healthRecord->player_id)->orderByDesc('record_date')->get();
        $sectionHistory=app(HealthRecordSections::class)->history($records);
        $sectionDocuments=Schema::hasTable('health_record_documents')
            ?HealthRecordDocument::where('player_id',$healthRecord->player_id)->orderByDesc('exam_date')->get():collect();
        return view('health-records.player-detail',compact('healthRecord','sectionHistory','sectionDocuments'));
    }
    public function document(int $record,int $document)
    {
        $healthRecord=$this->record($record);
        $item=HealthRecordDocument::where('health_record_id',$healthRecord->id)
            ->where('player_id',$healthRecord->player_id)->findOrFail($document);
        $bytes=base64_decode($item->content,true);
        abort_unless(is_string($bytes)&&hash_equals($item->sha256,hash('sha256',$bytes)),409);
        $name=basename(str_replace('\\','/',$item->original_name));
        // Pièce jointe privée, sans rendu de contenu actif ni URL publique.
        return response()->streamDownload(fn()=>print($bytes),$name,[
            'Content-Type'=>'application/octet-stream','Cache-Control'=>'private, no-store',
            'X-Content-Type-Options'=>'nosniff']);
    }
}
