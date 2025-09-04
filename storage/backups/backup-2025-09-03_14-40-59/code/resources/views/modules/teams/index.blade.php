@extends('layouts.app')

@section('title', 'Gestion des Équipes')

@section('content')
<div class="container-fluid">
    <!-- Header avec bouton retour -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-users text-blue-600 mr-3"></i>
                Gestion des Équipes
            </h1>
            <p class="text-gray-600 mt-2">Gestion des équipes selon les standards FIFA Connect</p>
        </div>
        <a href="{{ route('modules.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour vers Modules
        </a>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">
                        Total Équipes
                    </p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $teams->count() }}
                    </p>
                </div>
                <div class="text-gray-400">
                    <i class="fas fa-users text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">
                        Clubs Actifs
                    </p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $clubs->where('status', 'active')->count() }}
                    </p>
                </div>
                <div class="text-gray-400">
                    <i class="fas fa-building text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">
                        Associations
                    </p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $clubs->pluck('association_id')->unique()->count() }}
                    </p>
                </div>
                <div class="text-gray-400">
                    <i class="fas fa-flag text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">
                        En Attente
                    </p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $teams->where('status', 'pending')->count() }}
                    </p>
                </div>
                <div class="text-gray-400">
                    <i class="fas fa-clock text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex flex-wrap gap-4">
            <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Créer une Équipe
            </button>
            <button onclick="openBulkCreateModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-layer-group mr-2"></i>
                Création en Masse
            </button>
            <button onclick="exportTeams()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-download mr-2"></i>
                Exporter
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtres</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Club</label>
                <select id="clubFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les clubs</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}">{{ $club->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Niveau</label>
                <select id="levelFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les niveaux</option>
                    <option value="professional">Professionnel</option>
                    <option value="semi-professional">Semi-Professionnel</option>
                    <option value="amateur">Amateur</option>
                    <option value="youth">Jeunes</option>
                    <option value="academy">Académie</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select id="statusFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                    <option value="pending">En attente</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tableau des équipes -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Liste des Équipes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Équipe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Club
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Niveau
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Catégorie
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Discipline
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($teams as $team)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if($team->club && $team->club->logo_path)
                                            <img src="{{ asset('storage/' . $team->club->logo_path) }}" 
                                                 alt="Logo {{ $team->club->name }}" 
                                                 class="h-12 w-12 rounded-full object-cover border-2 border-gray-200">
                                        @elseif($team->club)
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center border-2 border-gray-200">
                                                <span class="text-white font-bold text-lg">{{ substr($team->club->name, 0, 2) }}</span>
                                            </div>
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                                <i class="fas fa-users text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $team->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            ID: {{ $team->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $team->club->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $team->club->association->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($team->level === 'professional') bg-purple-100 text-purple-800
                                    @elseif($team->level === 'semi-professional') bg-blue-100 text-blue-800
                                    @elseif($team->level === 'amateur') bg-green-100 text-green-800
                                    @elseif($team->level === 'youth') bg-yellow-100 text-yellow-800
                                    @elseif($team->level === 'academy') bg-indigo-100 text-indigo-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($team->level ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $team->age_category ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ucfirst($team->discipline ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($team->status === 'active') bg-green-100 text-green-800
                                    @elseif($team->status === 'inactive') bg-red-100 text-red-800
                                    @elseif($team->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($team->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="editTeam({{ $team->id }})" 
                                            class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded-lg text-sm font-medium transition-colors duration-200"
                                            title="Modifier l'équipe">
                                        <i class="fas fa-edit mr-1"></i>
                                        Modifier
                                    </button>
                                    <button onclick="viewTeam({{ $team->id }})" 
                                            class="bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded-lg text-sm font-medium transition-colors duration-200"
                                            title="Voir les détails">
                                        <i class="fas fa-eye mr-1"></i>
                                        Voir
                                    </button>
                                    <button onclick="deleteTeam({{ $team->id }})" 
                                            class="bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded-lg text-sm font-medium transition-colors duration-200"
                                            title="Supprimer l'équipe">
                                        <i class="fas fa-trash mr-1"></i>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                Aucune équipe trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modales et scripts JavaScript -->
<script>
// Fonctions pour les modales et actions
function openCreateModal() {
    window.location.href = '{{ route("modules.teams.create") }}';
}

function openBulkCreateModal() {
    // Créer une modale pour la création en masse
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Création en Masse d'Équipes</h3>
                    <button onclick="closeBulkCreateModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Club</label>
                    <select id="bulkClubId" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner un club</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}">{{ $club->name }} ({{ $club->association->name ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Niveau</label>
                    <select id="bulkLevel" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner un niveau</option>
                        <option value="professional">Professionnel</option>
                        <option value="semi-professional">Semi-Professionnel</option>
                        <option value="amateur">Amateur</option>
                        <option value="youth">Jeunes</option>
                        <option value="academy">Académie</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Discipline</label>
                    <select id="bulkDiscipline" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner une discipline</option>
                        <option value="football">Football</option>
                        <option value="futsal">Futsal</option>
                        <option value="beach_soccer">Beach Soccer</option>
                        <option value="women_football">Football Féminin</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select id="bulkStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="active">Actif</option>
                        <option value="inactive">Inactif</option>
                        <option value="pending">En attente</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Équipes à créer (une par ligne)</label>
                    <textarea id="bulkTeamNames" 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                              rows="6" 
                              placeholder="Équipe Première&#10;Équipe Réserve&#10;U-17&#10;U-19&#10;Féminines"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeBulkCreateModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Annuler
                    </button>
                    <button onclick="submitBulkCreate()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-plus mr-2"></i>
                        Créer les équipes
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function closeBulkCreateModal() {
    const modal = document.querySelector('.fixed.inset-0');
    if (modal) {
        modal.remove();
    }
}

function submitBulkCreate() {
    const clubId = document.getElementById('bulkClubId').value;
    const level = document.getElementById('bulkLevel').value;
    const discipline = document.getElementById('bulkDiscipline').value;
    const status = document.getElementById('bulkStatus').value;
    const teamNames = document.getElementById('bulkTeamNames').value;
    
    if (!clubId || !level || !discipline || !teamNames.trim()) {
        alert('Veuillez remplir tous les champs obligatoires');
        return;
    }
    
    const names = teamNames.split('\n').filter(name => name.trim());
    if (names.length === 0) {
        alert('Veuillez saisir au moins un nom d\'équipe');
        return;
    }
    
    // Créer un formulaire pour la soumission
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("modules.teams.bulk-store") }}';
    
    // Ajouter le token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Ajouter les données
    const dataInput = document.createElement('input');
    dataInput.type = 'hidden';
    dataInput.name = 'data';
    dataInput.value = JSON.stringify({
        club_id: clubId,
        level: level,
        discipline: discipline,
        status: status,
        team_names: names
    });
    form.appendChild(dataInput);
    
    document.body.appendChild(form);
    form.submit();
}

function exportTeams() {
    alert('Fonctionnalité d\'export à implémenter');
}

function editTeam(id) {
    // Redirection vers la page d'édition
    window.location.href = '/modules/teams/' + id + '/edit';
}

function viewTeam(id) {
    // Redirection vers la page de visualisation
    window.location.href = '/modules/teams/' + id;
}

function deleteTeam(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette équipe ? Cette action est irréversible.')) {
        // Créer un formulaire pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/modules/teams/' + id;
        
        // Ajouter le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Ajouter la méthode DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Soumettre le formulaire
        document.body.appendChild(form);
        form.submit();
    }
}

// Filtres
document.getElementById('clubFilter').addEventListener('change', function() {
    // Logique de filtrage à implémenter
    console.log('Filtre club:', this.value);
});

document.getElementById('levelFilter').addEventListener('change', function() {
    // Logique de filtrage à implémenter
    console.log('Filtre niveau:', this.value);
});

document.getElementById('statusFilter').addEventListener('change', function() {
    // Logique de filtrage à implémenter
    console.log('Filtre statut:', this.value);
});
</script>
@endsection