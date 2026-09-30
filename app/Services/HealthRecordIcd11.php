<?php
namespace App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
final class HealthRecordIcd11
{
    public function resolve(Request $request): array
    {
        if(!$request->has('icd11_selection')) return [];
        $raw=$request->input('icd11_selection');
        $items=is_string($raw)?json_decode($raw,true):$raw;
        Validator::make(['items'=>$items],[
            'items'=>'present|array|max:20','items.*'=>'array:id,release,language',
            'items.*.id'=>'required|string|regex:/^[0-9]+$/|max:20',
            'items.*.release'=>'required|regex:/^[0-9]{4}-[0-9]{2}$/',
            'items.*.language'=>'required|in:fr,en',
        ])->validate();
        $resolved=[]; $seen=[];
        foreach($items as $item){
            $key=$item['id'].':'.$item['release'].':'.$item['language'];
            if(isset($seen[$key]))continue;
            $seen[$key]=true;
            // Code et libellé viennent uniquement de l'OMS, jamais du navigateur.
            $resolved[]=app(WhoIcd11::class)->entity($item['id'],$item['release'],$item['language']);
        }
        return ['icd11_diagnoses'=>$resolved];
    }
}
