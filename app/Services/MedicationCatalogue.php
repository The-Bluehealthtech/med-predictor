<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

final class MedicationCatalogue
{
    // Référentiel fourni, distinct des données cliniques et des fixtures.
    public function import(): int
    {
        $data = json_decode(gzdecode(file_get_contents(database_path('reference/medication-catalogue-2609A.json.gz'))), true, 512, JSON_THROW_ON_ERROR);
        $rows = [];
        foreach ($data['products'] as $product) {
            $rows[] = ['product_id'=>$product['id'], 'name'=>$product['name'],
                'search_text'=>strtolower(Str::ascii($product['id'].' '.$product['name'].' '.implode(' ', $product['substances']))),
                'source_version'=>$data['version'], 'payload'=>json_encode($product, JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)];
        }
        DB::transaction(function () use ($rows) {
            foreach (array_chunk($rows, 150) as $batch) DB::table('medication_catalogue')
                ->upsert($batch, ['product_id'], ['name','search_text','source_version','payload']);
        });
        return count($rows);
    }
    public function search(string $term): array
    {
        if (strlen(trim(str_replace(['%','_'], '', $term))) < 2) return [];
        return app(RxNorm::class)->search($term);
    }
    public function forEditing(array $items): array
    {
        if (!$items) return [];
        $rows=DB::table('medication_catalogue')->whereIn('product_id',array_column($items,'id'))
            ->get()->keyBy('product_id');
        $result=[];
        foreach($items as $item) {
            if (($item['source']??null)==='RxNorm' || preg_match('/^[0-9]+$/',(string)($item['id']??''))) {
                // Lecture du traitement enregistré sans dépendre de la disponibilité du fournisseur.
                $result[]=array_merge(['name'=>$item['id'],'presentations'=>[],'substances'=>[]],$item);
                continue;
            }
            if (!isset($rows[$item['id']])) continue;
            $result[]=array_merge($this->product($rows[$item['id']]),[
                'presentation_id'=>$item['presentation_id'] ?? $item['presentation']['id'] ?? null,
                'dose'=>$item['dose']??null, 'route'=>$item['route']??null, 'frequency'=>$item['frequency']??null]);
        }
        return $result;
    }
    private function product(object $row): array
    {
        return array_merge(json_decode($row->payload,true,512,JSON_THROW_ON_ERROR),
            ['source'=>'csv4Emd_Fr_2609A.zip','version'=>$row->source_version]);
    }
    // Les libellés du navigateur ne font jamais autorité : résolution par identifiant.
    public function applySelection(array $data, ?array $previous = null): array
    {
        if (!array_key_exists('medication_selection', $data)) return $data;
        $result=$data['result_json'] ?? $previous ?? [];
        if (is_string($result)) $result=json_decode($result, true);
        $result=is_array($result) ? $result : [];
        if ($previous) $result=array_replace_recursive($previous,$result);
        if (!is_array($result['medical_history'] ?? null)) $result['medical_history']=[];
        $result['medical_history']['medication_products']=$this->selections($data['medication_selection'] ?? '[]', $previous['medical_history']['medication_products'] ?? []);
        unset($data['medication_selection']);
        $data['result_json']=$result;
        return $data;
    }
    public function selections(string $json, array $previous = []): array
    {
        $items = json_decode($json, true);
        Validator::make(['items'=>$items], ['items'=>'present|array|max:30',
            'items.*'=>'array', 'items.*.id'=>'required|string|max:40',
            'items.*.presentation_id'=>'nullable|string|max:40',
            'items.*.dose'=>'nullable|string|max:200', 'items.*.route'=>'nullable|string|max:200',
            'items.*.frequency'=>'nullable|string|max:200'])->validate();
        $selected = [];
        foreach ($items as $item) {
            $legacy=collect($previous)->first(fn($p)=>($p['id']??null)===$item['id'] && ($p['source']??null)!=='RxNorm');
            if ($legacy) {
                // Seules les références historiques déjà présentes dans CE dossier sont conservables.
                $product=$legacy; $presentation=$legacy['presentation']??null;
            } else {
                abort_unless(empty($item['presentation_id']),422,'Présentation RxNorm non reconnue.');
                $product=app(RxNorm::class)->resolve($item['id']); $presentation=null;
            }
            $selected[]=['id'=>$product['id'],'rxcui'=>$product['rxcui']??null,'tty'=>$product['tty']??null,
                'name'=>$product['name'],'substances'=>$product['substances']??[],
                'presentation'=>$presentation, 'atc'=>null, 'source'=>$product['source'],'version'=>$product['version'],
                'antidoping'=>$product['antidoping']??['status'=>'unresolved','version'=>'2025','matches'=>[]],
                'dose'=>$item['dose']??null,'route'=>$item['route']??null,'frequency'=>$item['frequency']??null];
        }
        return $selected;
    }
}
