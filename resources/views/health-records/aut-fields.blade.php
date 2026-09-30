@php
$locale=app()->getLocale()==='fr'?'fr':'en';
$sections=config('medical_aut.sections');
$source=$sourceText ?? json_decode(file_get_contents(config('medical_aut.source_directory').'/fifa-aut-fr-2024-text.json'),true);
$autRecord=$healthRecord ?? new \App\Models\HealthRecord;
$autItem=$item ?? null;
$prefill=($autEmbedded ?? false)?[]:['surname'=>$autRecord->player?->last_name,'given_names'=>$autRecord->player?->first_name,
    'club_federation'=>$autRecord->player?->club?->name,'diagnosis'=>$autRecord->diagnosis];
$values=old($autPrefix ?? 'form',$autItem?->aut_form_data['fields'] ?? $prefill);
@endphp
@if($autEmbedded ?? false)
<p class="whitespace-pre-line">{{ $source['instructions'] }}</p>
<p class="my-4">{{ __('medical_aut.draft_note') }}</p>
<a href="{{ route('medical-aut.blank-source') }}">{{ __('medical_aut.source') }}</a>
@endif
@foreach($sections as $section)
<fieldset class="bg-white rounded-lg shadow-md p-6 my-6">
<legend class="text-xl font-bold">{{ $section[$locale] }}</legend>
@if(in_array($section['key'],['physician','declaration']))
<p class="whitespace-pre-line my-4">{{ $source[$section['key']==='physician'?'physician':'player'] }}</p>
<p class="text-sm my-4">{{ __('medical_aut.signature_note') }}</p>
@endif
@if($section['key']==='medical')
<p class="whitespace-pre-line my-4">{{ $source['medical_note'] }}</p>
<div class="border rounded p-4 my-4">
<h3>{{ __('medical_aut.icd_title') }}</h3>
@foreach($autRecord->icd11_diagnoses ?? [] as $code)
<p>{{ $code['code'] ?? '—' }} — {{ $code['label'] ?? '—' }} ({{ $code['release'] ?? '—' }})</p>
@endforeach
@if($autRecord->exists)<a href="{{ route('health-records.edit',$autRecord->id) }}">{{ __('medical_aut.search') }}</a>@endif
</div>
@endif
@if($section['key']==='treatment')
@include('health-records.aut-substances')
@endif
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
@foreach($section['fields'] as $field)
@php($value=$values[$field[0]] ?? '')
<div>
<label class="block font-semibold" for="aut-{{ $field[0] }}">{{ $field[$locale==='fr'?1:2] }}</label>
@if(($field[3]??'text')==='textarea')
<textarea class="w-full border rounded p-2" rows="4" id="aut-{{ $field[0] }}" name="{{ $autPrefix ?? 'form' }}[{{ $field[0] }}]">{{ $value }}</textarea>
@elseif(($field[3]??'text')==='select')
<select class="w-full border rounded p-2" id="aut-{{ $field[0] }}" name="{{ $autPrefix ?? 'form' }}[{{ $field[0] }}]">
<option value="">—</option>
@foreach($field[4] as $key=>$label)<option value="{{ $key }}" @selected((string)$value===(string)$key)>{{ __('medical_aut.option_'.$key) }}</option>@endforeach
</select>
@else
<input class="w-full border rounded p-2" id="aut-{{ $field[0] }}" name="{{ $autPrefix ?? 'form' }}[{{ $field[0] }}]"
 type="{{ $field[3]??'text' }}" value="{{ $value }}" @if(str_starts_with($field[0],'substance_')) list="aut-substances" @endif>
@endif
</div>
@endforeach
</div>
</fieldset>
@endforeach
<div class="bg-blue-50 border rounded-lg p-4 my-4"><p class="whitespace-pre-line">{{ $source['submission'] }}</p></div>
<details class="bg-white rounded-lg p-6 my-4"><summary>{{ __('medical_aut.privacy') }}</summary>
<p class="whitespace-pre-line">{{ $source['privacy'] }}</p></details>
<div class="bg-white rounded-lg shadow-md p-6 my-4">
<label class="block font-semibold" for="aut-documents">{{ __('medical_aut.documents') }}</label>
<p>{{ __('medical_aut.documents_help') }}</p>
<input id="aut-documents" type="file" name="{{ $autDocumentName ?? 'documents' }}[]" multiple accept=".pdf,.png,.jpg,.jpeg">
</div>
