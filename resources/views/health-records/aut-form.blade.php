@extends('layouts.app')
@section('title', __('medical_aut.title'))
@section('content')
@php
$locale=app()->getLocale()==='fr'?'fr':'en';
$sections=config('medical_aut.sections');
$source=$sourceText;
$prefill=['surname'=>$healthRecord->player?->last_name,'given_names'=>$healthRecord->player?->first_name,
    'club_federation'=>$healthRecord->player?->club?->name,'diagnosis'=>$healthRecord->diagnosis];
$values=old('form',$item?->aut_form_data['fields'] ?? $prefill);
@endphp
<div class="container mx-auto px-4 py-8">
<h1 class="text-3xl font-bold">{{ __('medical_aut.title') }}</h1>
<p>{{ $healthRecord->player?->full_name }} · {{ config('medical_aut.source') }}</p>
<p class="whitespace-pre-line my-4">{{ $source['instructions'] }}</p>
<p class="bg-yellow-50 border rounded-lg p-4 my-4">{{ __('medical_aut.draft_note') }}</p>
<a href="{{ route('medical-aut.source',$healthRecord->id) }}">{{ __('medical_aut.source') }}</a>
@if($errors->any())<div role="alert" class="border rounded p-4">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<form method="post" enctype="multipart/form-data"
 action="{{ $item ? route('medical-aut.update',[$healthRecord->id,$item->id]) : route('medical-aut.store',$healthRecord->id) }}">
@csrf @if($item) @method('PUT') @endif
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
@foreach($healthRecord->icd11_diagnoses ?? [] as $code)
<p>{{ $code['code'] ?? '—' }} — {{ $code['label'] ?? '—' }} ({{ $code['release'] ?? '—' }})</p>
@endforeach
<a href="{{ route('health-records.edit',$healthRecord->id) }}">{{ __('medical_aut.search') }}</a>
</div>
@endif
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
@foreach($section['fields'] as $field)
@php($value=$values[$field[0]] ?? '')
<div>
<label class="block font-semibold" for="aut-{{ $field[0] }}">{{ $field[$locale==='fr'?1:2] }}</label>
@if(($field[3]??'text')==='textarea')
<textarea class="w-full border rounded p-2" rows="4" id="aut-{{ $field[0] }}" name="form[{{ $field[0] }}]">{{ $value }}</textarea>
@elseif(($field[3]??'text')==='select')
<select class="w-full border rounded p-2" id="aut-{{ $field[0] }}" name="form[{{ $field[0] }}]">
<option value="">—</option>
@foreach($field[4] as $key=>$label)<option value="{{ $key }}" @selected((string)$value===(string)$key)>{{ __('medical_aut.option_'.$key) }}</option>@endforeach
</select>
@else
<input class="w-full border rounded p-2" id="aut-{{ $field[0] }}" name="form[{{ $field[0] }}]"
 type="{{ $field[3]??'text' }}" value="{{ $value }}">
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
<input id="aut-documents" type="file" name="documents[]" multiple accept=".pdf,.png,.jpg,.jpeg">
</div>
<button type="submit" class="bg-blue-600 text-white rounded-lg px-6 py-3">{{ __('medical_aut.save') }}</button>
<a class="ml-4" href="{{ route('medical-aut.index',$healthRecord->id) }}">{{ __('medical_aut.back') }}</a>
</form>
</div>
@endsection
