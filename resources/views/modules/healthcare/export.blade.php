@extends('layouts.app')
@section('title', __('healthcare_repair.export'))
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold">{{ __('healthcare_repair.export') }}</h1>
        <p class="my-4">{{ __('healthcare_repair.export_scope') }}</p>
        <a href="{{ route('healthcare.export', ['download'=>1]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md">{{ __('healthcare_repair.download') }}</a>
        <a href="{{ route('modules.healthcare.index') }}" class="ml-4">{{ __('errors.generic_back') }}</a>
    </div>
</div>
@endsection
