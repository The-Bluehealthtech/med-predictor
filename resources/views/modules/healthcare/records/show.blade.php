@extends('layouts.app')
@section('title', __('healthcare.records_title'))
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold">{{ __('healthcare.records_title') }}</h1>
    <div class="flex gap-4 my-4">
        <a href="{{ route('modules.healthcare.index') }}">{{ __('errors.generic_back') }}</a>
        <a href="{{ route('health-records.show', $healthRecord) }}">{{ __('healthcare_repair.full_record') }}</a>
        <a href="{{ route('health-records.edit', $healthRecord) }}">{{ __('healthcare.edit') }}</a>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold">{{ $healthRecord->player?->full_name ?? __('healthcare.na') }}</h2>
        <p>{{ $healthRecord->record_date?->format('d/m/Y') ?? __('healthcare.na') }} — {{ __('healthcare.status_'.$healthRecord->status) }}</p>
        <p>{{ $healthRecord->user?->name ?? __('healthcare.na') }}</p>
        @foreach(['blood_pressure_systolic','blood_pressure_diastolic','heart_rate','temperature','weight','height','bmi','allergies','medications','medical_history','symptoms','diagnosis','treatment_plan','chief_complaint','physical_examination','laboratory_results','imaging_results','prescriptions','follow_up_instructions','visit_notes'] as $field)
            <div class="mt-3">
                <h3 class="font-medium">{{ __('healthcare_repair.fields.'.$field) }}</h3>
                <p class="whitespace-pre-wrap">{{ is_array($healthRecord->$field) ? json_encode($healthRecord->$field, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) : ($healthRecord->$field ?? __('healthcare.na')) }}</p>
            </div>
        @endforeach
    </div>
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-xl font-semibold">PCMA</h2>
        @forelse($pcmaRecords as $pcma)
            <p class="mt-2"><a href="{{ route('pcma.show', $pcma) }}">{{ $pcma->assessment_date?->format('d/m/Y') ?? __('healthcare.na') }}</a> — {{ $pcma->is_signed ? __('pcma_workflow.signed_by').' '.($pcma->signed_by ?? '') : __('pcma_workflow.unsigned') }}</p>
        @empty
            <p>{{ __('healthcare_repair.no_pcma') }}</p>
        @endforelse
    </div>
    <p class="bg-yellow-50 rounded-lg p-4 mt-6">{{ __('healthcare_repair.unvalidated') }}</p>
</div>
@endsection
