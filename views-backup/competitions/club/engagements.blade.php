@extends('layouts.app')

@section('title', 'Mes Engagements - Compétitions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Mes Engagements</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Compétitions Inscrites</h2>
        
        @if($engagements->count() > 0)
            <div class="space-y-4">
                @foreach($engagements as $engagement)
                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold text-lg">{{ $engagement['nom'] }}</h3>
                            <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                    {{ $engagement['type'] }}
                                </span>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                    {{ $engagement['categorie'] }}
                                </span>
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs">
                                    {{ $engagement['discipline'] }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $engagement['statut'] === 'Publié' ? 'bg-green-100 text-green-800' : 
                                   ($engagement['statut'] === 'Validé par la Fédération' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $engagement['statut'] }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Saison:</span>
                            <span class="font-medium">{{ $engagement['saison'] }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Classement:</span>
                            <span class="font-medium">{{ $engagement['classement'] }}ème</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Points:</span>
                            <span class="font-medium">{{ $engagement['points'] }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Matchs:</span>
                            <span class="font-medium">{{ $engagement['matchs_joues'] }}/{{ $engagement['nb_matchs'] }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <div class="flex justify-between items-center text-sm text-gray-600">
                            <div>
                                <span class="text-gray-500">Association:</span>
                                <span class="font-medium">{{ $engagement['association'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Confédération:</span>
                                <span class="font-medium">{{ $engagement['confederation'] }}</span>
                            </div>
                        </div>
                        @if($engagement['fifa_connect_id'])
                        <div class="mt-2 flex items-center justify-between">
                            <div class="flex items-center text-xs text-gray-500">
                                <i class="fas fa-link mr-1"></i>
                                <span>FIFA Connect ID: {{ $engagement['fifa_connect_id'] }}</span>
                            </div>
                            @if($engagement['fifa_sync_enabled'])
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-sync-alt mr-1"></i>
                                Sync Activé
                            </span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">Aucune compétition trouvée.</p>
        @endif
    </div>
</div>
@endsection