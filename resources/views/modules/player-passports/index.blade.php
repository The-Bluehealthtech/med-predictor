@extends('layouts.app')

@section('title', 'Player Passports - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">🛂</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Player Passports
                                </h1>
                                <p class="text-sm text-gray-600">Gestion des passeports des joueurs</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour aux Modules</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('player-registration.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    ➕ Nouveau Passeport
                </a>
                <a href="{{ route('players.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    👥 Joueurs
                </a>
                <a href="{{ route('club.player-licenses.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    📋 Licences
                </a>
                <a href="#" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-center transition-colors">
                    🔍 Recherche
                </a>
            </div>
        </div>

        <!-- Passports List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Liste des Passeports ({{ $passports->total() }})</h2>
            </div>

            @if($passports->isEmpty())
            <div class="p-6">
                <div class="text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun passeport enregistré</h3>
                    <p class="mt-1 text-sm text-gray-500">Commencez par créer un nouveau passeport.</p>
                    <div class="mt-6">
                        <a href="{{ route('player-registration.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Créer un passeport
                        </a>
                    </div>
                </div>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Joueur</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Club actuel</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">N° Passeport</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">ID FIFA Connect</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Expiration</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($passports as $passport)
                        <tr>
                            <td class="p-3">{{ $passport->player?->full_name ?? '—' }}</td>
                            <td class="p-3">{{ $passport->currentClub?->name ?? '—' }}</td>
                            <td class="p-3">{{ $passport->passport_number ?? '—' }}</td>
                            <td class="p-3">{{ $passport->fifa_connect_id ?? '—' }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $passport->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $passport->status ?? 'inconnu' }}
                                </span>
                            </td>
                            <td class="p-3">{{ $passport->expiry_date?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200">
                {{ $passports->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
