@extends('layouts.app')
@section('title', __('medical_module.history'))
@section('content')
<div class="container mx-auto px-4 py-8">
<h1 class="text-3xl font-bold">{{ __('medical_module.history') }}</h1>
<p class="bg-yellow-50 border rounded-lg p-4 my-4">{{ __('healthcare_repair.unvalidated') }}</p>
<div class="bg-white rounded-lg shadow-md p-6">
<p>{{ $item->player?->full_name ?? '—' }}</p>
<p>{{ $item->prediction_date?->format('d/m/Y') ?? '—' }}</p>
<p>{{ $item->prediction_type }} · {{ $item->ai_model_version ?? '—' }}</p>
</div>
<a href="{{ route('modules.medical.index') }}">{{ __('medical_module.back') }}</a>
</div>
@endsection
