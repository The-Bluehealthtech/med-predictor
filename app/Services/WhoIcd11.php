<?php
namespace App\Services;
use Illuminate\Support\Facades\{Cache, Http, Validator};

final class WhoIcd11
{
    private function token(bool $renew=false): string
    {
        $id=config('services.icd11.client_id'); $secret=config('services.icd11.client_secret');
        abort_unless($id && $secret,503,__('pcma_icd11.unavailable'));
        $key='icd11.token.'.hash('sha256',$id.$secret);
        if($renew) Cache::forget($key);
        $token=Cache::get($key);
        if(is_string($token) && $token!=='') return $token;
        try {
            $response=Http::asForm()->withBasicAuth($id,$secret)->withoutRedirecting()
                ->timeout((int)config('services.icd11.timeout',30))
                ->post('https://icdaccessmanagement.who.int/connect/token',
                    ['grant_type'=>'client_credentials','scope'=>'icdapi_access']);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            abort(503,__('pcma_icd11.unavailable'));
        }
        abort_unless($response->successful() && is_string($response->json('access_token'))
            && $response->json('access_token')!=='',503,__('pcma_icd11.unavailable'));
        $token=$response->json('access_token');
        Cache::put($key,$token,max(1,(int)$response->json('expires_in',300)-60));
        return $token;
    }
    private function get(string $path,string $language,array $query=[]): array
    {
        $base=rtrim(config('services.icd11.base_url','https://id.who.int'),'/');
        abort_unless($base==='https://id.who.int',503,__('pcma_icd11.unavailable'));
        for($attempt=0;$attempt<2;$attempt++) {
            $token=$this->token($attempt===1);
            try {
                $response=Http::withToken($token)->acceptJson()->withoutRedirecting()
                    ->withHeaders(['API-Version'=>'v2','Accept-Language'=>$language])
                    ->timeout((int)config('services.icd11.timeout',30))->get($base.$path,$query);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                abort(503,__('pcma_icd11.unavailable'));
            }
            if($response->status()===401 && $attempt===0) continue;
            abort_if($response->status()===404,422,__('pcma_icd11.unknown'));
            abort_unless($response->successful() && is_array($response->json()),
                503,__('pcma_icd11.unavailable'));
            return $response->json();
        }
        abort(503,__('pcma_icd11.unavailable'));
    }
    public function search(string $query,string $language): array
    {
        $release=config('services.icd11.release','2026-01');
        abort_unless(preg_match('/^[0-9]{4}-[0-9]{2}$/',$release),503,__('pcma_icd11.unavailable'));
        $response=$this->get('/icd/release/11/'.$release.'/mms/search',$language,
            ['q'=>$query,'flatResults'=>'true','highlightingEnabled'=>'false','medicalCodingMode'=>'true']);
        abort_unless(empty($response['error']) && is_array($response['destinationEntities']??null),
            503,__('pcma_icd11.unavailable'));
        $items=[];
        foreach($response['destinationEntities'] as $entry) {
            if(empty($entry['theCode']) || !is_string($entry['title']??null)) continue;
            $uri=$entry['id']??'';
            if(!preg_match('~^https?://id\\.who\\.int/icd/(?:entity/|release/11/[0-9]{4}-[0-9]{2}/mms/)([0-9]+)$~',$uri,$match)) continue;
            $items[]=['id'=>$match[1], 'code'=>$entry['theCode'],
                'label'=>html_entity_decode(strip_tags($entry['title']),ENT_QUOTES|ENT_HTML5,'UTF-8'),
                'entity_uri'=>$uri,'release'=>$release,'language'=>$language,
                'is_leaf'=>(bool)($entry['isLeaf']??false),
                'postcoordination'=>$entry['postcoordinationAvailability']??null];
            if(count($items)>=20) break;
        }
        return $items;
    }
    public function entity(string $id,string $release,string $language): array
    {
        Validator::make(compact('id','release','language'),['id'=>'required|regex:/^[0-9]+$/|max:20',
            'release'=>'required|regex:/^[0-9]{4}-[0-9]{2}$/','language'=>'required|in:fr,en'])->validate();
        $key='icd11.entity.'.hash('sha256',$id.$release.$language);
        return Cache::remember($key,(int)config('services.icd11.cache_ttl',3600),function()use($id,$release,$language){
            $entry=$this->get('/icd/release/11/'.$release.'/mms/'.$id,$language);
            $label=data_get($entry,'title.@value');
            abort_unless(is_string($entry['code']??null) && $entry['code']!==''
                && is_string($label) && $label!=='',422,__('pcma_icd11.unknown'));
            return ['id'=>$id,'code'=>$entry['code'],'label'=>strip_tags($label),
                'system'=>'http://id.who.int/icd/release/11/mms','release'=>$release,'language'=>$language,
                'entity_uri'=>$entry['@id']??null,'source'=>'WHO ICD-11 API',
                'coding_note'=>strip_tags(data_get($entry,'codingNote.@value','')),
                'postcoordination'=>$entry['postcoordinationScale']??[]];
        });
    }
    // Résoudre chaque choix auprès de l'OMS ; aucune confiance dans les libellés client.
    public function applySelections(array $data,?array $previous=null): array
    {
        $fields=['cardiovascular','surgical','allergies'];
        if(!array_intersect(array_keys($data),array_map(fn($f)=>$f.'_icd11_selection',$fields)))return $data;
        $result=$data['result_json']??$previous??[];
        if(is_string($result))$result=json_decode($result,true);
        $result=is_array($result)?$result:[];
        if($previous)$result=array_replace_recursive($previous,$result);
        if(!is_array($result['medical_history']??null))$result['medical_history']=[];
        foreach($fields as $field){
            $key=$field.'_icd11_selection';if(!array_key_exists($key,$data))continue;
            $items=json_decode($data[$key]??'[]',true);
            Validator::make(['items'=>$items],['items'=>'array|max:30','items.*'=>'array',
                'items.*.id'=>'required|string|regex:/^[0-9]+$/|max:20',
                'items.*.release'=>'required|string|regex:/^[0-9]{4}-[0-9]{2}$/',
                'items.*.language'=>'required|in:fr,en'])->validate();
            $selected=[];
            foreach($items as $item)$selected[]=$this->entity($item['id'],$item['release'],$item['language']);
            $result['medical_history'][$field.'_icd11_codes']=$selected;
            if($field==='cardiovascular')unset($result['medical_history']['cardiovascular_icd11']);
            unset($data[$key]);
        }
        $data['result_json']=$result;return $data;
    }
}
