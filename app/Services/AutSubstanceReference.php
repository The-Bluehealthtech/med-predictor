<?php
namespace App\Services;
final class AutSubstanceReference
{
    public function data(): array
    {
        return json_decode(gzdecode(file_get_contents(config('medical_aut.source_directory').'/aut-reference-2025.json.gz')),true,512,JSON_THROW_ON_ERROR);
    }
    public function provenance(array $fields): array
    {
        $source=$this->data();$result=[];
        for($i=1;$i<=3;$i++){
            $label=$fields['substance_'.$i]??null;
            $entry=collect($source['entries'])->first(fn($e)=>$e['label']===$label);
            if($entry)$result['substance_'.$i]=[
                'source'=>$source['source_filename'],'source_sha256'=>$source['source_sha256'],
                'version'=>$source['version'],'effective_date'=>$source['effective_date'],
                'sheet'=>$source['sheet'],'row'=>$entry['row'],'section_row'=>$entry['section_row'],
                'label'=>$entry['label']];
        }
        // Aucun statut d'interdiction ni obligation d'AUT n'est inféré d'un nom.
        return $result;
    }
}
