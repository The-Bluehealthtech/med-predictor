@extends('layouts.app')
@section('title',__('medical_sections.player_title'))
@section('content')
<main class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">{{ __('medical_sections.player_title') }}</h1>
    <p class="mb-6">{{ __('medical_sections.readonly') }}</p>
    @include('health-records.section-history')
</main>
@endsection
