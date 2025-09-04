@extends('layouts.app')

@section('title', 'Feuille de Match')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête du match -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Feuille de Match</h1>
                        <p class="text-sm text-gray-600">{{ $feuilleMatch['competition'] }} - Journée {{ $feuilleMatch['journee'] }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Date</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $feuilleMatch['date']->format('d/m/Y') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Heure</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $feuilleMatch['heure'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Stade</div>
                            <div class="text-sm font-medium text-gray-900">{{ $feuilleMatch['stade'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Score du match -->
            <div class="px-6 py-8">
                <div class="flex items-center justify-center space-x-8">
                    <!-- Équipe domicile -->
                    <div class="text-center">
                        <div class="h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4">
                            <span class="text-blue-600 font-bold text-xl">{{ substr($feuilleMatch['domicile']->name ?? $feuilleMatch['domicile']->short_name ?? 'CLUB', 0, 2) }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $feuilleMatch['domicile']->name ?? 'Club Domicile' }}</h3>
                        <p class="text-sm text-gray-500">{{ $feuilleMatch['domicile']->short_name ?? 'CD' }}</p>
                    </div>

                    <!-- Score -->
                    <div class="text-center">
                        <div class="text-6xl font-bold text-gray-900 mb-2">
                            @if($feuilleMatch['statut'] === 'Terminé')
                                {{ $feuilleMatch['buts_domicile'] }} - {{ $feuilleMatch['buts_exterieur'] }}
                            @else
                                <span class="text-gray-400">- - -</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500">
                            @if($feuilleMatch['statut'] === 'Terminé')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Terminé
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    À venir
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Équipe extérieure -->
                    <div class="text-center">
                        <div class="h-20 w-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                            <span class="text-red-600 font-bold text-xl">{{ substr($feuilleMatch['exterieur']->name ?? $feuilleMatch['exterieur']->short_name ?? 'CLUB', 0, 2) }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $feuilleMatch['exterieur']->name ?? 'Club Extérieur' }}</h3>
                        <p class="text-sm text-gray-500">{{ $feuilleMatch['exterieur']->short_name ?? 'CE' }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($feuilleMatch['statut'] === 'À venir')
            <!-- Message pour match à venir -->
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Match à venir</h3>
                <p class="text-gray-500 mb-4">
                    Ce match n'a pas encore été joué. Les compositions d'équipes, les arbitres et les événements seront disponibles après le match.
                </p>
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Informations disponibles :</strong> Date, heure, stade, équipes
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Composition des équipes -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Équipe domicile -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                            <h3 class="text-lg font-semibold text-blue-900">
                                <i class="fas fa-home mr-2"></i>
                                {{ $feuilleMatch['domicile']->name ?? 'Club Domicile' }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Titulaires -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Titulaires</h4>
                                    <div class="space-y-2">
                                        @foreach(collect($feuilleMatch['joueurs_domicile'])->where('titulaire', true) as $joueur)
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                <div class="flex items-center space-x-3">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 text-sm font-medium">
                                                        {{ $joueur['numero'] }}
                                                    </span>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $joueur['nom'] }}</div>
                                                        <div class="text-xs text-gray-500">{{ $joueur['position'] }}</div>
                                                    </div>
                                                </div>
                                                <div class="flex space-x-1">
                                                    @if($joueur['carton_jaune'])
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-100 text-yellow-800 text-xs font-medium">
                                                            {{ $joueur['carton_jaune'] }}'
                                                        </span>
                                                    @endif
                                                    @if($joueur['carton_rouge'])
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-800 text-xs font-medium">
                                                            {{ $joueur['carton_rouge'] }}'
                                                        </span>
                                                    @endif
                                                    @if($joueur['buts'] > 0)
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-800 text-xs font-medium">
                                                            {{ $joueur['buts'] }}⚽
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Remplaçants -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Remplaçants</h4>
                                    <div class="space-y-2">
                                        @foreach(collect($feuilleMatch['joueurs_domicile'])->where('remplacant', true) as $joueur)
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                <div class="flex items-center space-x-3">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-800 text-sm font-medium">
                                                        {{ $joueur['numero'] }}
                                                    </span>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $joueur['nom'] }}</div>
                                                        <div class="text-xs text-gray-500">{{ $joueur['position'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Équipe extérieure -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                            <h3 class="text-lg font-semibold text-red-900">
                                <i class="fas fa-plane mr-2"></i>
                                {{ $feuilleMatch['exterieur']->name ?? 'Club Extérieur' }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Titulaires -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Titulaires</h4>
                                    <div class="space-y-2">
                                        @foreach(collect($feuilleMatch['joueurs_exterieur'])->where('titulaire', true) as $joueur)
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                <div class="flex items-center space-x-3">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-800 text-sm font-medium">
                                                        {{ $joueur['numero'] }}
                                                    </span>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $joueur['nom'] }}</div>
                                                        <div class="text-xs text-gray-500">{{ $joueur['position'] }}</div>
                                                    </div>
                                                </div>
                                                <div class="flex space-x-1">
                                                    @if($joueur['carton_jaune'])
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-100 text-yellow-800 text-xs font-medium">
                                                            {{ $joueur['carton_jaune'] }}'
                                                        </span>
                                                    @endif
                                                    @if($joueur['carton_rouge'])
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-800 text-xs font-medium">
                                                            {{ $joueur['carton_rouge'] }}'
                                                        </span>
                                                    @endif
                                                    @if($joueur['buts'] > 0)
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-800 text-xs font-medium">
                                                            {{ $joueur['buts'] }}⚽
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Remplaçants -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Remplaçants</h4>
                                    <div class="space-y-2">
                                        @foreach(collect($feuilleMatch['joueurs_exterieur'])->where('remplacant', true) as $joueur)
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                                <div class="flex items-center space-x-3">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-800 text-sm font-medium">
                                                        {{ $joueur['numero'] }}
                                                    </span>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $joueur['nom'] }}</div>
                                                        <div class="text-xs text-gray-500">{{ $joueur['position'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informations du match -->
            <div class="space-y-6">
                    <!-- Corps arbitral -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-whistle mr-2"></i>
                                Corps Arbitral
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Arbitre Principal</span>
                                <span class="text-sm font-medium text-gray-900">{{ $feuilleMatch['arbitre_principal'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Assistant 1</span>
                                <span class="text-sm font-medium text-gray-900">{{ $feuilleMatch['arbitre_assistant_1'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Assistant 2</span>
                                <span class="text-sm font-medium text-gray-900">{{ $feuilleMatch['arbitre_assistant_2'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">VAR</span>
                                <span class="text-sm font-medium text-gray-900">{{ $feuilleMatch['arbitre_var'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Délégué de Match</span>
                                <span class="text-sm font-medium text-gray-900">{{ $feuilleMatch['delegue_match'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Observateur</span>
                                <span class="text-sm font-medium text-gray-900">{{ $feuilleMatch['observateur'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chronologie du match -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-clock mr-2"></i>
                                Chronologie
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($feuilleMatch['evenements'] as $evenement)
                                    <div class="flex items-center space-x-3">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 text-sm font-medium">
                                            {{ $evenement['minute'] }}'
                                        </span>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900">{{ $evenement['type'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $evenement['joueur'] }} - {{ $evenement['equipe'] }}</div>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @if($evenement['type'] === 'But')
                                                <i class="fas fa-futbol text-green-500"></i>
                                            @elseif($evenement['type'] === 'Carton Jaune')
                                                <i class="fas fa-square text-yellow-500"></i>
                                            @elseif($evenement['type'] === 'Carton Rouge')
                                                <i class="fas fa-square text-red-500"></i>
                                            @else
                                                <i class="fas fa-exchange-alt text-blue-500"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-cog mr-2"></i>
                                Actions
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <button onclick="printFeuilleMatch()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-print mr-2"></i>
                                Imprimer
                            </button>
                            <button onclick="exportFeuilleMatch()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-download mr-2"></i>
                                Exporter PDF
                            </button>
                            <a href="{{ url()->previous() }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Fonction pour imprimer la feuille de match
function printFeuilleMatch() {
    window.print();
}

// Fonction pour exporter la feuille de match
function exportFeuilleMatch() {
    // Simulation d'export PDF
    showNotification('Export PDF en cours...', 'info');
    
    setTimeout(() => {
        showNotification('Feuille de match exportée avec succès!', 'success');
    }, 2000);
}

// Fonction pour afficher des notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${icon} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
</script>
@endsection
