@extends('layouts.app')

@section('title', 'Passeports Joueurs')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Passeports Joueurs</h1>
                    <p class="mt-1 text-sm text-gray-500">Gestion des passeports FIFA et conformité</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('player-passports.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nouveau Passeport
                    </a>
                    <button onclick="bulkExport()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Nom, FIFA ID, Passport...">
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="status" id="status" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expiré</option>
                        <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>Révoqué</option>
                    </select>
                </div>

                <div>
                    <label for="validity_status" class="block text-sm font-medium text-gray-700">Validité</label>
                    <select name="validity_status" id="validity_status" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Toutes les validités</option>
                        <option value="valid" {{ request('validity_status') == 'valid' ? 'selected' : '' }}>Valide</option>
                        <option value="expired" {{ request('validity_status') == 'expired' ? 'selected' : '' }}>Expirée</option>
                        <option value="expiring_soon" {{ request('validity_status') == 'expiring_soon' ? 'selected' : '' }}>Expire bientôt</option>
                    </select>
                </div>

                <div>
                    <label for="club_id" class="block text-sm font-medium text-gray-700">Club</label>
                    <select name="club_id" id="club_id" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Tous les clubs</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Passeports</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $passports->total() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Conformes FIFA</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $passports->where('compliance_status', 'compliant')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Expirent bientôt</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $passports->where('valid_until', '<', now()->addDays(30))->where('valid_until', '>', now())->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Expirés</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $passports->where('valid_until', '<', now())->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Passport List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($passports as $passport)
                <li>
                    <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    @if($passport->photo_url)
                                        <img class="h-12 w-12 rounded-full object-cover" src="{{ asset('storage/' . $passport->photo_url) }}" alt="{{ $passport->player->first_name }}">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($passport->player->first_name, 0, 1) }}{{ substr($passport->player->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $passport->player->first_name }} {{ $passport->player->last_name }}
                                        </p>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $passport->fifa_id }}
                                        </span>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $passport->passport_number }}
                                        </span>
                                    </div>
                                    <div class="flex items-center mt-1 space-x-4">
                                        <p class="text-sm text-gray-500">
                                            {{ $passport->nationality }} • {{ $passport->position }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Licence: {{ $passport->license_number }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Valide jusqu'au: {{ $passport->valid_until->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                <!-- Status Badges -->
                                <div class="flex flex-col space-y-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $passport->status === 'active' ? 'bg-green-100 text-green-800' : ($passport->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($passport->status) }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $passport->compliance_status === 'compliant' ? 'bg-green-100 text-green-800' : ($passport->compliance_status === 'requires_review' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $passport->compliance_status)) }}
                                    </span>
                                </div>

                                <!-- Validity Indicator -->
                                <div class="flex items-center">
                                    @if($passport->valid_until > now()->addDays(30))
                                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                        <span class="ml-1 text-xs text-gray-500">Valide</span>
                                    @elseif($passport->valid_until > now())
                                        <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                        <span class="ml-1 text-xs text-gray-500">Expire bientôt</span>
                                    @else
                                        <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                        <span class="ml-1 text-xs text-gray-500">Expiré</span>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('player-passports.show', $passport) }}" 
                                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Voir
                                    </a>
                                    <a href="{{ route('player-passports.edit', $passport) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Modifier
                                    </a>
                                    <a href="{{ route('player-passports.export-pdf', $passport) }}" 
                                       class="text-green-600 hover:text-green-900 text-sm font-medium">
                                        PDF
                                    </a>
                                    <button onclick="deletePassport({{ $passport->id }})" 
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @empty
                <li class="px-4 py-8 text-center">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun passeport trouvé</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par créer un nouveau passeport joueur.</p>
                        <div class="mt-6">
                            <a href="{{ route('player-passports.create') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Nouveau Passeport
                            </a>
                        </div>
                    </div>
                </li>
                @endforelse
            </ul>
        </div>

        <!-- Pagination -->
        @if($passports->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($passports->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Précédent
                    </span>
                @else
                    <a href="{{ $passports->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Précédent
                    </a>
                @endif

                @if($passports->hasMorePages())
                    <a href="{{ $passports->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Suivant
                    </a>
                @else
                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Suivant
                    </span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Affichage de <span class="font-medium">{{ $passports->firstItem() }}</span> à <span class="font-medium">{{ $passports->lastItem() }}</span> sur <span class="font-medium">{{ $passports->total() }}</span> résultats
                    </p>
                </div>
                <div>
                    {{ $passports->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Bulk Export Modal -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Export en lot</h3>
                <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="exportModalContent">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="mt-4 text-gray-600">Préparation de l'export...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function deletePassport(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce passeport ?')) {
        fetch(`/player-passports/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression');
        });
    }
}

function bulkExport() {
    document.getElementById('exportModal').classList.remove('hidden');
    
    // Simulate bulk export
    setTimeout(() => {
        document.getElementById('exportModalContent').innerHTML = `
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Export terminé</h3>
                <p class="mt-1 text-sm text-gray-500">Les passeports ont été exportés avec succès.</p>
                <div class="mt-6">
                    <button onclick="closeExportModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Fermer
                    </button>
                </div>
            </div>
        `;
    }, 2000);
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
}
</script>
@endpush 