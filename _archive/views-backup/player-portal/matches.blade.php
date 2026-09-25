@extends('layouts.app')

@section('title', 'Matchs - Portail Joueur')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Mes Matchs</h1>
        <p class="text-gray-600">Historique et calendrier des matchs</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Matchs Récents</h2>
        </div>
        <div class="p-6">
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun match disponible</h3>
                <p class="text-gray-500">Aucun match n'a été trouvé pour le moment.</p>
            </div>
        </div>
    </div>
</div>
@endsection


















