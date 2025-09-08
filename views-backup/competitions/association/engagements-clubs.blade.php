@extends('layouts.app')

@section('title', 'Engagements des Clubs - Association')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-clipboard-check text-blue-600 mr-3"></i>
                Engagements des Clubs
            </h1>
            <p class="text-gray-600 mt-2">Validation des inscriptions aux compétitions</p>
        </div>
        <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux Modules
        </a>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-blue-600 text-2xl">🏢</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-600">Total Clubs</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $clubs->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-green-600 text-2xl">✅</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-600">Engagés</p>
                    <p class="text-2xl font-bold text-green-900">{{ $clubs->where('statut_engagement', 'Engagé')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-yellow-600 text-2xl">⏳</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-600">En Attente</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $clubs->where('statut_engagement', 'Non engagé')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-purple-600 text-2xl">⚽</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-purple-600">Total Matchs</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $clubs->sum('total_matches') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions globales -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex flex-wrap gap-4">
            <button onclick="exportEngagements()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                📥 Exporter les Engagements
            </button>
            <button onclick="validateAllEngagements()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                ✅ Valider Tous
            </button>
            <button onclick="refreshData()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                🔄 Actualiser
            </button>
            <button onclick="addNewEngagement()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                ➕ Nouvel Engagement
            </button>
        </div>
    </div>

    <!-- Liste des clubs -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Clubs Inscrits</h2>
        </div>
        
        @if($clubs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compétition</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matchs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière Activité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($clubs as $club)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-lg">
                                            <span class="text-white font-bold text-lg">{{ substr($club['nom'], 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $club['nom'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $club['teams_count'] }} équipe(s) • {{ $club['competitions_count'] }} compétition(s)</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $club['competition'] }}
                                @if($club['competitions_count'] > 1)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        +{{ $club['competitions_count'] - 1 }} autres
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($club['statut_engagement'] === 'Engagé')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✅ Engagé
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        ⏳ En Attente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <span class="text-green-600 font-medium">{{ $club['feuilles_validees'] }}</span>
                                    <span class="text-gray-400 mx-1">/</span>
                                    <span class="text-gray-600">{{ $club['feuilles_soumises'] }}</span>
                                </div>
                                <div class="text-xs text-gray-500">Terminés / Total</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $club['derniere_activite'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="viewClubDetails({{ $club['id'] }})" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded" title="Voir détails">👁️ Voir</button>
                                    <button onclick="validateEngagement({{ $club['id'] }})" class="text-green-600 hover:text-green-900 px-2 py-1 rounded" title="Valider engagement">✅ Valider</button>
                                    <button onclick="editEngagement({{ $club['id'] }})" class="text-yellow-600 hover:text-yellow-900 px-2 py-1 rounded" title="Modifier">✏️ Modifier</button>
                                    <button onclick="exportClubData({{ $club['id'] }})" class="text-purple-600 hover:text-purple-900 px-2 py-1 rounded" title="Exporter">📥 Exporter</button>
                                    <button onclick="suspendEngagement({{ $club['id'] }})" class="text-red-600 hover:text-red-900 px-2 py-1 rounded" title="Suspendre">🚫 Suspendre</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun engagement trouvé</h3>
                <p class="text-gray-500">Les engagements des clubs apparaîtront ici.</p>
            </div>
        @endif
    </div>
</div>

<script>
// Actions JavaScript pour les boutons
function exportEngagements() {
    // Créer un formulaire pour l'export
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("test.export.engagements") }}';
    form.target = '_blank';
    
    // Ajouter le token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function validateAllEngagements() {
    if (confirm('Voulez-vous valider tous les engagements en attente ?')) {
        fetch('{{ route("test.validate.all.engagements") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            alert('Erreur lors de la validation: ' + error);
        });
    }
}

function refreshData() {
    location.reload();
}

function addNewEngagement() {
    alert('Fonctionnalité d\'ajout d\'engagement à implémenter');
    // Ici on pourrait rediriger vers un formulaire d'ajout
}

function viewClubDetails(clubId) {
    fetch(`{{ url('test-club-details') }}/${clubId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher les détails dans une modal ou une nouvelle page
                const club = data.club;
                alert(`Club: ${club.name}\nÉquipes: ${club.teams.length}\nCompétitions: ${club.teams.flatMap(t => t.competitions).length}`);
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            alert('Erreur lors de la récupération: ' + error);
        });
}

function validateEngagement(clubId) {
    if (confirm(`Voulez-vous valider l'engagement du club ${clubId} ?`)) {
        fetch(`{{ url('test-validate-engagement') }}/${clubId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            alert('Erreur lors de la validation: ' + error);
        });
    }
}

function editEngagement(clubId) {
    alert(`Fonctionnalité de modification d'engagement ${clubId} à implémenter`);
    // Ici on pourrait ouvrir un formulaire de modification
}

function exportClubData(clubId) {
    fetch(`{{ url('test-export-club-data') }}/${clubId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `club_${clubId}_${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        alert('Erreur lors de l\'export: ' + error);
    });
}

function suspendEngagement(clubId) {
    if (confirm(`Voulez-vous suspendre l'engagement du club ${clubId} ?`)) {
        fetch(`{{ url('test-suspend-engagement') }}/${clubId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            alert('Erreur lors de la suspension: ' + error);
        });
    }
}
</script>
@endsection
