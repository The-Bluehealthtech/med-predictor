@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">{{ $title }}</h1>
    <div class="space-y-4">
    @forelse($records as $record)
        <article class="bg-white rounded-lg shadow p-4">
            <h2 class="font-bold">{{ $record['label'] ?? '—' }}</h2>
            <p>{{ $record['date'] ?? '—' }} · {{ $record['status'] ?? '—' }}</p>
            <p>{{ is_array($record['description'] ?? null) ? implode(' · ', array_filter($record['description'], 'is_scalar')) : ($record['description'] ?? '') }}</p>
        </article>
    @empty
        <p>{{ __('Aucune donnée disponible') }}</p>
    @endforelse
    </div>
</div>
@endsection
