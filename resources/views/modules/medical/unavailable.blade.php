@extends('layouts.app')
@section('title', __('medical_module.history'))
@section('content')
<div class="container mx-auto px-4 py-8">
<h1 class="text-3xl font-bold">{{ __('medical_module.history') }}</h1>
<p class="bg-yellow-50 border rounded-lg p-4 my-4">{{ __('healthcare_repair.unvalidated') }}</p>
<a href="{{ route('modules.medical.index') }}">{{ __('medical_module.back') }}</a>
</div>
@endsection
