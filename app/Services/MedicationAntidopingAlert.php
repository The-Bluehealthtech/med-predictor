<?php
namespace App\Services;
use Illuminate\Support\Str;
final class MedicationAntidopingAlert
{
    // Correspondance textuelle stricte, jamais décision réglementaire ni traduction devinée.
    public function check(array $ingredients): array
    {
        $source=app(AutSubstanceReference::class)->data();$matches=[];$unmatched=[];
        foreach($ingredients as $ingredient){
            $name=$this->normalize($ingredient['name']);$found=false;
            if($name==='')continue;
            foreach($source['sections'] as $section){
                $hits=[];
                foreach($section['rows'] as $row){
                    if(preg_match('/(?<![a-z0-9])'.preg_quote($name,'/').'(?![a-z0-9])/',$this->normalize($row['text'])))$hits[]=$row;
                }
                if(!$hits)continue;$found=true;
                $scopeRow=$section['row']>=425?423:($section['row']>=273?271:42);
                $scope=array_values(array_filter($source['complete_rows'],
                    fn($r)=>($r['row']>=26 && $r['row']<=40) || $r['row']===$scopeRow));
                $matches[]=['ingredient'=>$ingredient,'category'=>$section['title'],
                    'rows'=>$hits,'context'=>array_merge($scope,$section['rows'])];
            }
            if(!$found)$unmatched[]=$ingredient;
        }
        return ['status'=>$matches?'mentions_found':'unresolved','version'=>$source['version'],
            'source_sha256'=>$source['source_sha256'],'source'=>$source['source_filename'],
            'effective_date'=>$source['effective_date'],'matches'=>$matches,
            'unmatched_ingredients'=>$unmatched,'method'=>'exact_normalized_ingredient_text'];
    }
    private function normalize(string $text): string
    {
        return strtolower(Str::ascii(trim($text)));
    }
}
