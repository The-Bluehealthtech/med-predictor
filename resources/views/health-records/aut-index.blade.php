@extends('layouts.app')
@section('title', __('medical_aut.title'))
@section('content')
<div class="container mx-auto px-4 py-8">
<h1 class="text-3xl font-bold">{{ __('medical_aut.title') }}</h1>
<p>{{ $healthRecord->player?->full_name }}</p>
<p class="bg-yellow-50 border rounded-lg p-4 my-4">{{ __('medical_aut.draft_note') }}</p>
<div class="flex flex-wrap gap-4 my-4">
<a href="{{ route('medical-aut.create',$healthRecord->id) }}">{{ __('medical_aut.create') }}</a>
<a href="{{ route('medical-aut.source',$healthRecord->id) }}">{{ __('medical_aut.source') }}</a>
<a href="{{ route('health-records.show',$healthRecord->id) }}">{{ __('medical_aut.back_record') }}</a>
</div>
@forelse($requests as $item)
<div class="bg-white rounded-lg shadow-md p-6 my-4">
<h2 class="font-bold">AUT #{{ $item->id }}</h2>
<p>{{ __('medical_aut.internal_date') }} : {{ $item->request_date?->format('d/m/Y') ?? '—' }}</p>
<p>{{ __('medical_aut.status') }} : {{ __('medical_aut.status_'.$item->status) }}</p>
<p>{{ $item->medication ?? '—' }}</p>
@if($item->status==='pending')<a href="{{ route('medical-aut.edit',[$healthRecord->id,$item->id]) }}">{{ __('medical_aut.edit') }}</a>@endif
@foreach($item->supporting_documents ?? [] as $i=>$file)
@if(is_array($file)&&isset($file['document_id']))
<p><a href="{{ route('medical-aut.document',[$healthRecord->id,$item->id,$i]) }}">{{ $file['name'] ?? __('medical_aut.document') }}</a></p>
@endif
@endforeach
</div>
@empty
<p>{{ __('medical_aut.empty') }}</p>
@endforelse
</div>
@endsection
