@extends('layouts.secretary')

@section('title', __('secretary.athlete_search_title'))

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('secretary.athlete_search_heading') }}</h1>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4">
            <input type="text" placeholder="{{ __('secretary.athlete_search_placeholder') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <p class="text-gray-600">{{ __('secretary.search_results_placeholder') }}</p>
    </div>
</div>
@endsection
