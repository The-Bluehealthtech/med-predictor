<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
final class RxNorm
{
    // Source fixe NLM ; aucun terme du patient n'est journalisé.
    private function get(string $path, array $query = []): array
    {
        try {
            $response=Http::acceptJson()->connectTimeout(3)->timeout(10)
                ->get('https://rxnav.nlm.nih.gov/REST/'.$path, $query);
            if (!$response->successful() || !is_array($response->json())) abort(503, 'RxNorm indisponible pour le moment.');
            return $response->json();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            abort(503, 'RxNorm indisponible pour le moment.');
        }
    }
    private function product(array $p): array
    {
        return ['id'=>(string)$p['rxcui'], 'rxcui'=>(string)$p['rxcui'],
            'name'=>$p['name'], 'tty'=>$p['tty']??null, 'source'=>'RxNorm',
            'substances'=>[], 'presentations'=>[], 'version'=>null];
    }
    public function search(string $term): array
    {
        $data=$this->get('drugs.json',['name'=>trim($term)]);
        $result=[];
        foreach ($data['drugGroup']['conceptGroup']??[] as $group) {
            foreach ($group['conceptProperties']??[] as $p) {
                if (preg_match('/^[0-9]+$/',(string)($p['rxcui']??'')) && !empty($p['name'])) {
                    $result[(string)$p['rxcui']]=$this->product($p);
                }
            }
        }
        return array_values($result);
    }
    public function resolve(string $id): array
    {
        abort_unless(preg_match('/^[0-9]{1,20}$/',$id),422,'Identifiant RxNorm invalide.');
        $data=$this->get('rxcui/'.$id.'/properties.json');
        $p=$data['properties']??null;
        abort_unless(is_array($p) && (string)($p['rxcui']??'')===$id && !empty($p['name']),422,'Médicament absent de RxNorm.');
        $product=$this->product($p);
        $version=$this->get('version.json');
        abort_unless(is_string($version['version']??null) && $version['version']!=='',503,'Version RxNorm indisponible.');
        $product['version']=$version['version'];
        return $product;
    }
}
