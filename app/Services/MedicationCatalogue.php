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
        $term = strtolower(Str::ascii(trim($term)));
        $term = str_replace(['%','_'], '', $term);
        if (strlen($term) < 2) return [];
        return DB::table('medication_catalogue')->where('search_text','like','%'.$term.'%')
            ->orderBy('name')->limit(20)->get()->map(fn ($r)=>$this->product($r))->all();
    }
    public function forEditing(array $items): array
    {
        if (!$items) return [];
        $rows=DB::table('medication_catalogue')->whereIn('product_id',array_column($items,'id'))
            ->get()->keyBy('product_id');
        $result=[];
        foreach($items as $item) {
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
        $result['medical_history']['medication_products']=$this->selections($data['medication_selection'] ?? '[]');
        unset($data['medication_selection']);
        $data['result_json']=$result;
        return $data;
    }
    public function selections(string $json): array
    {
        $items = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        Validator::make(['items'=>$items], ['items'=>'array',
            'items.*'=>'array', 'items.*.id'=>'required|string|max:40',
            'items.*.presentation_id'=>'nullable|string|max:40',
            'items.*.dose'=>'nullable|string|max:200', 'items.*.route'=>'nullable|string|max:200',
            'items.*.frequency'=>'nullable|string|max:200'])->validate();
        $selected = [];
        foreach ($items as $item) {
            $row = DB::table('medication_catalogue')->where('product_id',$item['id'])->first();
            abort_unless($row,422,'Médicament absent du catalogue.');
            $product=$this->product($row);
            $presentation=null;
            if (!empty($item['presentation_id'])) {
                foreach ($product['presentations'] as $p) if ($p['id']===$item['presentation_id']) $presentation=$p;
                abort_unless($presentation,422,'Présentation absente du médicament sélectionné.');
            }
            $selected[]=['id'=>$product['id'],'name'=>$product['name'],'substances'=>$product['substances'],
                'presentation'=>$presentation, 'atc'=>null, 'source'=>$product['source'],'version'=>$product['version'],
                'dose'=>$item['dose']??null,'route'=>$item['route']??null,'frequency'=>$item['frequency']??null];
        }
        return $selected;
    }
}
