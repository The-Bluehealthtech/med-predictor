<?php
namespace App\Services;

use App\Models\HealthRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

// Adaptation explicite des formulaires au schéma principal, sans interprétation clinique.
final class HealthRecordSections
{
    public function definitions(): array { return config('medical_sections.sections', []); }
    private function clean(mixed $value): mixed
    {
        if (is_string($value)) {
            $value=trim($value);
            if ($value==='' || in_array(mb_strtolower($value),['données non disponibles','data unavailable','null'],true)) return null;
        }
        return $value;
    }
    public function prepare(Request $request): array
    {
        $rules=['capture'=>'nullable|array','section_dates'=>'nullable|array','section_source'=>'nullable|array'];
        foreach($this->definitions() as $section=>$def) {
            $rules['capture.'.$section]='nullable|boolean';
            if (!$request->boolean('capture.'.$section)) continue;
            $rules['section_dates.'.$section]='required|date';
            $rules['section_source.'.$section]=in_array($section,['biological','laboratory'],true)?'required|string|max:1000':'nullable|string|max:1000';
            $rules['section_values.'.$section]='nullable|array';
            foreach($def['fields'] as $field=>$type) {
                $rules[$field]=match($type) {
                    'number'=>'nullable|numeric','nonnegative_integer'=>'nullable|integer|min:0',
                    'symptom'=>'nullable|integer|min:0|max:6',
                    'balance'=>'nullable|integer|min:0|max:10','date'=>'nullable|date',
                    'time'=>'nullable|date_format:H:i','boolean'=>'nullable|boolean',
                    'json'=>'nullable|json|max:100000','list'=>'nullable|array',
                    default=>'nullable|string|max:60000',
                };
                if ($type==='list') $rules[$field.'.*']='nullable|string|max:1000';
                // normalizeLists a déjà converti les anciens JSON : accepter ces tableaux.
                if ($type==='text' && is_array($request->input($field))) $rules[$field]='nullable|array';
                if ($type==='json' && is_array($request->input($field))) $rules[$field]='nullable|array';
                $rules['section_values.'.$section.'.'.$field]=$rules[$field];
                if($type==='list') {
                    // Le formulaire simplifié fournit une entrée par ligne.
                    $rules['section_values.'.$section.'.'.$field]='nullable|string|max:60000';
                }
            }
            if(in_array($section,['biological','laboratory'],true)) {
                $rules['lab_rows.'.$section]='nullable|array|max:100';
                $rules['lab_rows.'.$section.'.*.analyte']='nullable|string|max:255';
                foreach(['value','unit','reference','method','report_id','laboratory'] as $field)
                    $rules['lab_rows.'.$section.'.*.'.$field]='nullable|string|max:1000';
                $rules['lab_rows.'.$section.'.*.sample_date']='nullable|date';
            }
            $rules['medical_files.'.$section]='nullable|array|max:'.config('medical_sections.max_files',10);
            $rules['medical_files.'.$section.'.*']='file|mimes:pdf,jpg,jpeg,png,dcm|max:'.config('medical_sections.max_file_kb',10240);
        }
        $validated=$request->validate($rules); $out=[];
        foreach($this->definitions() as $section=>$def) {
            if(!$request->boolean('capture.'.$section)) continue;
            $values=[];
            foreach($def['fields'] as $field=>$type) {
                $posted=$validated['section_values'][$section] ?? [];
                if(!array_key_exists($field,$validated) && !array_key_exists($field,$posted)) continue;
                $preferred=$this->clean($posted[$field] ?? null);
                $value=$preferred ?? $this->clean($validated[$field] ?? null);
                if($type==='list' && is_string($value)) $value=preg_split('/\r?\n/',$value);
                if($type==='json' && is_string($value)) $value=json_decode($value,true,512,JSON_THROW_ON_ERROR);
                if($value!==null && $value!==[]) $values[$field]=$value;
            }
            $rows=[];
            foreach($validated['lab_rows'][$section] ?? [] as $row) {
                $row=array_filter(array_map(fn($v)=>$this->clean($v),$row),fn($v)=>$v!==null);
                if(!$row) continue;
                Validator::make($row,['analyte'=>'required','value'=>'required','laboratory'=>'required',
                    'sample_date'=>'required|date','report_id'=>'required'])->validate();
                $rows[]=$row;
            }
            if($rows) $values['lab_rows']=$rows;
            $files=$request->file('medical_files.'.$section,[]);
            foreach(config('medical_sections.file_inputs',[]) as $input=>$target) {
                if($section!==$target) continue;
                $selected=$request->file($input,[]);
                if(!is_array($selected)) $selected=[$selected];
                foreach(array_filter($selected) as $file) {
                    Validator::make(['file'=>$file],['file'=>'file|mimes:pdf,jpg,jpeg,png,dcm|max:'.config('medical_sections.max_file_kb',10240)])->validate();
                    $files[]=$file;
                }
            }
            Validator::make(['files'=>$files],['files'=>'array|max:'.config('medical_sections.max_files',10)])->validate();
            Validator::make(['values'=>$values,'files'=>$files],[
                'values'=>count($files)?'array':'required|array|min:1',
            ])->validate();
            if(isset($values['imaging_data'])) {
                Validator::make(['exams'=>$values['imaging_data']],['exams'=>'array|max:100',
                    'exams.*'=>'array','exams.*.imaging_date'=>'required|date',
                    'exams.*.imaging_type'=>'required|string|max:255',
                    'exams.*.imaging_findings'=>'nullable|string|max:60000'])->validate();
            }
            $out[$section]=['date'=>$validated['section_dates'][$section],
                'source'=>$validated['section_source'][$section] ?? null,'values'=>$values,'files'=>$files];
        }
        return $out;
    }
    public function persist(HealthRecord $record,array $sections): void
    {
        foreach($sections as $section=>$data) {
            $column=$this->definitions()[$section]['column'];
            $history=$this->decode($record->$column);
            // Ne jamais effacer les tableaux historiques, même si leur ancien format diffère.
            if($history && !array_is_list($history)) $history=[['legacy'=>$history]];
            $entry=['_fit_entry'=>true,'id'=>(string)Str::uuid(),'date'=>$data['date'],
                'recorded_at'=>now()->toIso8601String(),'recorded_by'=>auth()->id(),
                'source'=>$data['source'],'version'=>config('medical_sections.version'),'values'=>$data['values']];
            $history[]=$entry;
            $record->$column=$column==='imaging_results'?json_encode($history,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR):$history;
            // Garder les vues historiques cohérentes avec les dernières valeurs réellement saisies.
            $columns=\Illuminate\Support\Facades\Schema::getColumnListing($record->getTable());
            $managed=array_column($this->definitions(),'column');
            foreach($data['values'] as $field=>$value) {
                $target=match($field){'mapa_date'=>'mapa_test_date','ecg_effort_max_fc'=>'ecg_effort_max_hr',default=>$field};
                if(!in_array($target,$record->getFillable(),true) || !in_array($target,$columns,true) || in_array($target,$managed,true)) continue;
                $record->$target=($record->getCasts()[$target] ?? null)==='array' && !is_array($value)?[$value]:$value;
            }
            foreach($data['files'] as $file) {
                $bytes=file_get_contents($file->getRealPath());
                \App\Models\HealthRecordDocument::create(['health_record_id'=>$record->id,
                    'player_id'=>$record->player_id,'section'=>$section,'entry_id'=>$entry['id'],
                    'exam_date'=>$data['date'],'original_name'=>$file->getClientOriginalName(),
                    'mime_type'=>$file->getMimeType(),'sha256'=>hash('sha256',$bytes),
                    'content'=>base64_encode($bytes),'recorded_by'=>auth()->id()]);
            }
        }
        $record->save();
    }
    private function decode(mixed $value): array
    {
        if(is_string($value)) { $decoded=json_decode($value,true); return is_array($decoded)?$decoded:($value!==''?[$value]:[]); }
        return is_array($value)?$value:($value!==null?[$value]:[]);
    }
    public function history(iterable $records): array
    {
        $out=array_fill_keys(array_keys($this->definitions()),[]);
        foreach($records as $record) foreach($this->definitions() as $section=>$def) {
            $entries=$this->decode($record->{$def['column']});
            if($entries && !array_is_list($entries)) $entries=[['legacy'=>$entries]];
            foreach($entries as $entry) {
                $out[$section][]=['record_id'=>$record->id,'record_date'=>$record->record_date,
                    'date'=>is_array($entry)?($entry['date'] ?? null):null,
                    'entry'=>is_array($entry)?$entry:['legacy'=>$entry]];
            }
        }
        foreach($out as &$entries) usort($entries,fn($a,$b)=>strcmp($b['date'] ?? '',$a['date'] ?? ''));
        return $out;
    }
    public function formValues(HealthRecord $record): array
    {
        $out=[];
        foreach($this->definitions() as $def) {
            foreach(array_reverse($this->decode($record->{$def['column']})) as $entry) {
                if(is_array($entry) && ($entry['_fit_entry'] ?? false)) {
                    foreach($entry['values'] ?? [] as $field=>$value) if(!array_key_exists($field,$out)) $out[$field]=$value;
                    if(!array_key_exists($def['column'],$out)) $out[$def['column']]=null;
                    break;
                }
            }
        }
        foreach($record->getFillable() as $field) if(!array_key_exists($field,$out)) $out[$field]=$record->$field;
        return $out;
    }
    // Éviter que extraRules écrase le JSON historique avant l'ajout d'un examen.
    public function protectedColumns(array $data,array $sections): array
    {
        foreach($this->definitions() as $section=>$def) {
            $column=$def['column'];
            if(isset($sections[$section]) || (array_key_exists($column,$data) && ($data[$column]===null || $data[$column]==='' || $data[$column]===[]))) unset($data[$column]);
        }
        return $data;
    }
    public function label(string $field): string
    {
        if(\Illuminate\Support\Facades\Lang::has('medical_sections.'.$field)) return __('medical_sections.'.$field);
        if(\Illuminate\Support\Facades\Lang::has('health_records_edit.'.$field)) return __('health_records_edit.'.$field);
        return ucfirst(str_replace('_',' ',$field));
    }
}
