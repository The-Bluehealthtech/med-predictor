@extends('layouts.app')

@section('title', 'Test Portail Joueur')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Test Portail Joueur</h1>
        
        <div class="space-y-4">
            <div>
                <h2 class="text-lg font-semibold">Utilisateur connecté :</h2>
                <p><strong>Nom :</strong> {{ Auth::user()->name ?? 'Non connecté' }}</p>
                <p><strong>Email :</strong> {{ Auth::user()->email ?? 'Non connecté' }}</p>
                <p><strong>Rôle :</strong> {{ Auth::user()->role ?? 'Non connecté' }}</p>
            </div>
            
            @if(Auth::user() && Auth::user()->player)
            <div>
                <h2 class="text-lg font-semibold">Joueur associé :</h2>
                <p><strong>Nom :</strong> {{ Auth::user()->player->first_name }} {{ Auth::user()->player->last_name }}</p>
                <p><strong>ID :</strong> {{ Auth::user()->player->id }}</p>
                <p><strong>Club :</strong> {{ Auth::user()->player->club->name ?? 'Aucun club' }}</p>
            </div>
            
            <div>
                <h2 class="text-lg font-semibold">Données disponibles :</h2>
                <p><strong>Dossiers de santé :</strong> {{ Auth::user()->player->healthRecords->count() }}</p>
                <p><strong>Performances :</strong> {{ Auth::user()->player->performances->count() }}</p>
                <p><strong>PCMA :</strong> {{ Auth::user()->player->pcmas->count() }}</p>
                <p><strong>Prédictions :</strong> {{ Auth::user()->player->medicalPredictions->count() }}</p>
            </div>
            
            <div>
                <h2 class="text-lg font-semibold">Actions :</h2>
                <a href="{{ route('player-portal.dashboard') }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded mr-2">Dashboard</a>
                <a href="{{ route('player-portal.medical-records') }}" class="inline-block bg-green-500 text-white px-4 py-2 rounded mr-2">Dossiers Médicaux</a>
                <a href="{{ route('player-portal.performances') }}" class="inline-block bg-orange-500 text-white px-4 py-2 rounded">Performances</a>
            </div>
            @else
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <strong>Erreur :</strong> Aucun joueur associé à cet utilisateur.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection














