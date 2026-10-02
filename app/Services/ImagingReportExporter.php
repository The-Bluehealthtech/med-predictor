<?php
namespace App\Services;
use App\Models\ImagingReport;
final class ImagingReportExporter {
    public function payload(ImagingReport $report): array {
        abort_unless($report->status==='validated',409,'Validez le compte rendu avant son export.');
        $study=$report->study()->with(['player','instances'=>fn($q)=>$q->metadataOnly()])->firstOrFail();
        $patient=$study->source_identity;
        if (!$patient) $patient=['id'=>'FIT-'.$study->player_id,'name'=>$study->player->full_name??$study->player->name,'birth_date'=>$study->player->date_of_birth?->format('Ymd')??'','sex'=>''];
        $refs=[];
        foreach ($report->reference_images??[] as $ref) {
            $image=$study->instances->firstWhere('id',(int)$ref['instance_id']);
            abort_unless($image,409,'Image de référence indisponible.');
            $refs[]=array_merge($ref,['sop_uid'=>$image->sop_uid,'sop_class_uid'=>$image->sop_class_uid,'series_uid'=>$image->series_uid,'frames'=>$image->metadata['frames']??1]);
        }
        $first=$study->instances->first();
        $age=$report->age_review??[]; $ageSummary='';
        if ($study->purpose==='age_u17') {
            $ageSummary=json_encode($age,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)."\nMaturité osseuse : aucun âge réel ni verdict de fraude ou d’éligibilité n’est déduit.";
        }
        $previous=$study->reports()->where('status','validated')->where('version','<',$report->version)->first();
        return ['sop_uid'=>$report->sop_uid,'study_uid'=>$study->study_uid,'series_uid'=>$report->sop_uid.'.1',
            'study_date'=>($first?->metadata['study_date']??null)?:$study->exam_date->format('Ymd'),'study_time'=>$first?->metadata['study_time']??'',
            'accession'=>$first?->metadata['accession']??'','patient'=>$patient,'validated_at'=>$report->validated_at->toIso8601String(),
            'validator'=>$report->validator_name??$report->validator?->name??'Médecin FIT','organization'=>$study->source,'version'=>$report->version,
            'indication'=>$study->indication,'technique'=>$report->technique,'quality'=>$report->quality,
            'findings'=>$report->findings,'conclusion'=>$report->conclusion,'age_summary'=>$ageSummary,'age_review'=>$age,'references'=>$refs,
            'predecessor'=>$previous?['sop_uid'=>$previous->sop_uid,'series_uid'=>$previous->sop_uid.'.1']:null];
    }
    public function dicom(ImagingReport $report): string {
        $result=app(MedicalImagingDicom::class)->run('sr',$this->payload($report));
        return base64_decode($result['content'],true);
    }
}
