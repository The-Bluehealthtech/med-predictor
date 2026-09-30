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
@include('health-records.aut-fields')
<button type="submit" class="bg-blue-600 text-white rounded-lg px-6 py-3">{{ __('medical_aut.save') }}</button>
<a class="ml-4" href="{{ route('medical-aut.index',$healthRecord->id) }}">{{ __('medical_aut.back') }}</a>
</form>
</div>
@endsection
