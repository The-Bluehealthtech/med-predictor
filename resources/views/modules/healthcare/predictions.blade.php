@extends('layouts.app')
@section('title', __('healthcare.medical_predictions'))
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold">{{ __('healthcare.medical_predictions') }}</h1>
    <p class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 my-4">{{ __('healthcare_repair.unvalidated') }}</p>
    <a href="{{ route('modules.healthcare.index') }}">{{ __('errors.generic_back') }}</a>
    <div class="bg-white rounded-lg shadow-md p-6 mt-4">
        @forelse($predictions as $prediction)
            <div class="border-b py-3">
                <p>{{ $prediction->player?->full_name ?? __('healthcare.na') }}</p>
                <p>{{ $prediction->prediction_date?->format('d/m/Y') ?? __('healthcare.na') }} — {{ $prediction->prediction_type }}</p>
                <p class="text-sm text-gray-500">{{ __('healthcare_repair.historical') }} · {{ $prediction->ai_model_version ?? __('healthcare.na') }}</p>
                <a href="{{ route('healthcare.records.show', ['record'=>$prediction->health_record_id]) }}">{{ __('healthcare.view') }}</a>
            </div>
        @empty
            <p>{{ __('healthcare_repair.no_predictions') }}</p>
        @endforelse
        {{ $predictions->links() }}
    </div>
</div>
@endsection
