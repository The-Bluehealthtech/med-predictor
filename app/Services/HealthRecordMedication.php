<?php
namespace App\Services;
use Illuminate\Http\Request;
final class HealthRecordMedication
{
    public function resolve(Request $request): array
    {
        if (!$request->has('rxnorm_selection')) {
            $values=$request->input('medications',[]);
            if (!is_array($values)) return [];
            $rx=array_values(array_filter($values,fn($p)=>is_array($p)&&($p['source']??null)==='RxNorm'));
            if (!$rx) return [];
            // Une API ne peut pas contourner la résolution en fournissant un libellé RxNorm.
            $request->merge(['rxnorm_selection'=>json_encode($rx)]);
        }
        $request->validate(['rxnorm_selection'=>'required|string|max:50000']);
        $items=app(MedicationCatalogue::class)->selections($request->input('rxnorm_selection'));
        // Le texte clinique historique reste séparé des concepts vérifiés RxNorm.
        $notes=$request->input('medications',[]);
        $notes=is_array($notes)?array_values(array_filter($notes,
            fn($p)=>!is_array($p) || ($p['source']??null)!=='RxNorm')):[];
        return ['medications'=>array_merge($notes,$items)];
    }
}
