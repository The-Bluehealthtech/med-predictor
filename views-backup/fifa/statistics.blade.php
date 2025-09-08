@extends('layouts.app')

@section('title', 'FIFA Statistics')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">FIFA Statistics</h1>
    <p class="text-gray-600 mb-6">Ceci est une page placeholder pour les statistiques FIFA. Les données sont accessibles via l'API JSON pour le dashboard Vue.</p>

    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Navigation FIFA</h2>
        <ul class="space-y-2">
            <li><a href="{{ route('fifa.dashboard') }}" class="text-blue-600 hover:underline">FIFA Dashboard</a></li>
            <li><a href="{{ route('fifa.sync-dashboard') }}" class="text-blue-600 hover:underline">Synchronisation FIFA</a></li>
            <li><a href="{{ route('fifa.analytics') }}" class="text-blue-600 hover:underline">Analytics FIFA</a></li>
            <li><a href="{{ route('fifa.contracts') }}" class="text-blue-600 hover:underline">Contrats FIFA</a></li>
            <li><a href="{{ route('fifa.connectivity') }}" class="text-blue-600 hover:underline">Connectivité FIFA</a></li>
        </ul>
    </div>
</div>
@endsection 