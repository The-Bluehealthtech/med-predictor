@extends('layouts.app')

@section('title', 'Data Synchronization')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Data Synchronization</h1>
    <p class="text-gray-600 mb-6">Cette page permet de gérer et de visualiser la synchronisation des données entre les différents modules du système.</p>

    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Accès rapide</h2>
        <ul class="space-y-2">
            <li><a href="{{ route('fifa.sync-dashboard') }}" class="text-blue-600 hover:underline">Synchronisation FIFA</a></li>
            <li><a href="{{ route('fifa.analytics') }}" class="text-blue-600 hover:underline">Analytics FIFA</a></li>
            <li><a href="{{ route('daily-passport.index') }}" class="text-blue-600 hover:underline">Passeport du Jour</a></li>
            <li><a href="{{ route('players.index') }}" class="text-blue-600 hover:underline">Gestion des Joueurs</a></li>
            <li><a href="{{ route('transfers.index') }}" class="text-blue-600 hover:underline">Transferts</a></li>
        </ul>
    </div>
</div>
@endsection 