@extends('layouts.app')

@section('title', 'Fixtures - Association')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        📅 Fixtures des Compétitions
                    </h1>
                    <p class="text-blue-200">
                        Calendrier et matchs de toutes les compétitions
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('competitions.association.supervision') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        ← Retour
                    </a>
                    <button onclick="exportFixtures()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                        📥 Exporter
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 mb-6 border border-white/20">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Rechercher un match..." 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <select id="competitionFilter" class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Toutes les compétitions</option>
                        @foreach($competitions as $competition)
                            <option value="{{ $competition->name }}">{{ $competition->name }}</option>
                        @endforeach
                    </select>
                    <select id="statusFilter" class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="Terminé">Terminé</option>
                        <option value="À venir">À venir</option>
                    </select>
                    <select id="journeeFilter" class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Toutes les journées</option>
                        @for($i = 1; $i <= 30; $i++)
                            <option value="{{ $i }}">Journée {{ $i }}</option>
                        @endfor
                    </select>
                    <button onclick="clearFilters()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        ❌ Effacer
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des Fixtures -->
        @if(isset($paginatedFixtures) && $paginatedFixtures->count() > 0)
            @foreach($paginatedFixtures as $journee)
                <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 overflow-hidden mb-6">
                    <div class="p-6 border-b border-white/20 bg-gradient-to-r from-blue-600/20 to-purple-600/20">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-white">
                                📅 Journée {{ $journee['journee'] }} - Championnat Tunisien
                            </h3>
                            <div class="text-sm text-gray-300">
                                🕐 {{ $journee['date']->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Heure</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Match</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Stade</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Résultat</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Arbitre</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-blue-200 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($journee['matchs'] as $match)
                                    <tr class="hover:bg-white/5 transition-colors cursor-pointer" onclick="viewMatchDetails({{ $match['id'] }})">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            <div class="flex items-center">
                                                <i class="fas fa-clock mr-2 text-blue-300"></i>
                                                {{ $match['heure'] }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <div class="text-sm font-medium text-white">
                                                    {{ $match['domicile'] }}
                                                </div>
                                                <div class="text-blue-300">vs</div>
                                                <div class="text-sm font-medium text-white">
                                                    {{ $match['exterieur'] }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            <i class="fas fa-map-marker-alt mr-2 text-orange-300"></i>
                                            {{ $match['stade'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($match['statut'] === 'Terminé')
                                                <div class="flex items-center space-x-1">
                                                    <span class="text-lg font-bold text-white">{{ $match['buts_domicile'] }}</span>
                                                    <span class="text-blue-300">-</span>
                                                    <span class="text-lg font-bold text-white">{{ $match['buts_exterieur'] }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            <i class="fas fa-whistle mr-2 text-purple-300"></i>
                                            {{ $match['arbitre_principal'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                                        <div class="flex space-x-2">
                                                <button onclick="event.stopPropagation(); viewMatchDetails({{ $match['id'] }})" 
                                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition-colors"
                                                        title="Voir détails">
                                                    👁️ Détails
                                                </button>
                                                <button onclick="event.stopPropagation(); viewMatchSheet({{ $match['id'] }})" 
                                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs transition-colors"
                                                        title="Feuille de match">
                                                    📋 Feuille
                                                </button>
                                                <button onclick="event.stopPropagation(); editMatch({{ $match['id'] }})" 
                                                        class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-xs transition-colors"
                                                        title="Modifier le match">
                                                    ✏️ Modifier
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
            
            <!-- Pagination -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-300">
                        Affichage de {{ $paginatedFixtures->firstItem() }} à {{ $paginatedFixtures->lastItem() }} 
                        sur {{ $paginatedFixtures->total() }} journées
                    </div>
                    <div class="flex space-x-2">
                        {{ $paginatedFixtures->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 p-12 text-center">
                <div class="bg-gray-500/20 p-6 rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-calendar text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-white mb-2">Aucune fixture trouvée</h3>
                <p class="text-blue-200 mb-6">
                    Il n'y a actuellement aucune fixture programmée.
                </p>
            </div>
        @endif
    </div>
</div>

<script>
// Fonctions pour les boutons d'action
function viewMatchDetails(matchId) {
    console.log('viewMatchDetails called with ID:', matchId);
    showMatchModal(matchId, 'details');
}

function viewMatchSheet(matchId) {
    console.log('viewMatchSheet called with ID:', matchId);
    // Rediriger vers la vraie feuille de match
    const url = `{{ route('competitions.association.feuille-match', '') }}/${matchId}`;
    console.log('Redirecting to:', url);
    window.location.href = url;
}

function editMatch(matchId) {
    console.log('editMatch called with ID:', matchId);
    showMatchModal(matchId, 'edit');
}

function exportFixtures() {
    alert('Export des fixtures en cours...');
    
    // Créer un fichier CSV fictif
    const csvContent = "Journée,Date,Heure,Équipe Domicile,Équipe Extérieur,Stade,Résultat,Arbitre\n" +
        "1,15/09/2024,15:00,Club A,Club B,Stade Municipal,2-1,Arbitre 1\n" +
        "1,15/09/2024,17:00,Club C,Club D,Stade Olympique,1-0,Arbitre 2";
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'fixtures_competitions.csv';
    a.click();
    window.URL.revokeObjectURL(url);
    
    showNotification('Fixtures exportées avec succès !', 'success');
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('competitionFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('journeeFilter').value = '';
    filterTable();
    showNotification('Filtres effacés !', 'success');
}

// Fonction de recherche et filtrage
function filterTable() {
    console.log('filterTable called'); // Debug log
    
    const searchInput = document.getElementById('searchInput');
    const competitionFilter = document.getElementById('competitionFilter');
    const statusFilter = document.getElementById('statusFilter');
    const journeeFilter = document.getElementById('journeeFilter');
    
    if (!searchInput || !competitionFilter || !statusFilter || !journeeFilter) {
        console.error('Search elements not found');
        return;
    }
    
    const searchTerm = searchInput.value.toLowerCase();
    const competitionValue = competitionFilter.value;
    const statusValue = statusFilter.value;
    const journeeValue = journeeFilter.value;
    
    console.log('Search term:', searchTerm, 'Competition:', competitionValue, 'Status:', statusValue);
    
    // Chercher dans tous les tableaux de toutes les journées
    const allRows = document.querySelectorAll('tbody tr');
    let visibleCount = 0;
    let totalCount = allRows.length;
    
    console.log('Total rows found:', totalCount);
    
    allRows.forEach(row => {
        const matchText = row.textContent.toLowerCase();
        const statusCell = row.querySelector('td:nth-child(4)'); // Le statut est maintenant en 4ème position
        const status = statusCell ? statusCell.textContent.trim() : '';
        
        // Recherche dans le texte de la ligne
        const matchesSearch = !searchTerm || matchText.includes(searchTerm.toLowerCase());
        
        // Filtre par compétition - pour l'instant on accepte tous les matchs car ils sont tous du même championnat
        const matchesCompetition = !competitionValue || true; // Tous les matchs sont du championnat tunisien
        
        // Filtre par statut - vérifier si le statut correspond
        const matchesStatus = !statusValue || status.toLowerCase().includes(statusValue.toLowerCase());
        
        // Filtre par journée - vérifier si la ligne appartient à la journée sélectionnée
        let matchesJournee = true;
        if (journeeValue) {
            // Trouver la carte de journée parente
            const journeeCard = row.closest('.bg-white\\/10');
            if (journeeCard) {
                const journeeTitle = journeeCard.querySelector('h3');
                if (journeeTitle) {
                    const journeeText = journeeTitle.textContent;
                    matchesJournee = journeeText.includes(`Journée ${journeeValue}`);
                }
            }
        }
        
        console.log(`Match: ${matchText.substring(0, 50)}... | Status: ${status} | Matches status: ${matchesStatus} | Matches journee: ${matchesJournee}`);
        
        if (matchesSearch && matchesCompetition && matchesStatus && matchesJournee) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Afficher le nombre de résultats
    showNotification(`${visibleCount} matchs sur ${totalCount} affichés`, 'success');
}

// Fonction pour afficher une modal de match
function showMatchModal(matchId, type) {
    // Trouver la ligne du match dans le tableau pour récupérer les vraies données
    const matchRow = document.querySelector(`tr[onclick*="${matchId}"]`);
    
    let matchData = {
        id: matchId,
        domicile: 'Équipe Domicile',
        exterieur: 'Équipe Extérieur',
        date: 'Date inconnue',
        heure: 'Heure inconnue',
        stade: 'Stade inconnu',
        statut: 'À venir',
        arbitre: 'Arbitre inconnu',
        score: '-'
    };
    
    if (matchRow) {
        const cells = matchRow.querySelectorAll('td');
        if (cells.length >= 6) {
            // Extraire les données de la ligne
            const matchCell = cells[1]; // Cellule du match
            const dateCell = cells[0]; // Cellule de l'heure
            const stadeCell = cells[2]; // Cellule du stade
            const resultatCell = cells[3]; // Cellule du résultat/score
            const arbitreCell = cells[4]; // Cellule de l'arbitre
            
            if (matchCell) {
                const matchText = matchCell.textContent.trim();
                const teams = matchText.split('vs');
                if (teams.length === 2) {
                    matchData.domicile = teams[0].trim();
                    matchData.exterieur = teams[1].trim();
                }
            }
            
            if (dateCell) {
                matchData.heure = dateCell.textContent.trim();
            }
            
            if (stadeCell) {
                matchData.stade = stadeCell.textContent.trim();
            }
            
            if (resultatCell) {
                const resultatText = resultatCell.textContent.trim();
                if (resultatText !== '-') {
                    // Nettoyer le score en supprimant les espaces et caractères invisibles
                    matchData.score = resultatText.replace(/\s+/g, ' ').trim();
                    matchData.statut = 'Terminé';
                } else {
                    matchData.statut = 'À venir';
                }
            }
            
            if (arbitreCell) {
                matchData.arbitre = arbitreCell.textContent.trim();
            }
        }
    }
    
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    
    const title = type === 'edit' ? 'Modifier le Match' : 'Détails du Match';
    const actionButton = type === 'edit' ? 
        '<button onclick="saveMatch(' + matchId + ')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Sauvegarder</button>' :
        '<button onclick="viewMatchSheet(' + matchId + ')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Voir Feuille</button>';
    
    const statutClass = matchData.statut.includes('Terminé') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
    
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">${title}</h3>
                <button onclick="closeModal(this)" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">ID Match</label>
                        <p class="text-lg font-semibold text-blue-600">${matchData.id}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <span class="inline-block px-3 py-1 rounded-full text-sm ${statutClass}">${matchData.statut}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Match</label>
                    <p class="text-lg font-semibold">${matchData.domicile} vs ${matchData.exterieur}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Heure</label>
                    <p class="text-lg">${matchData.heure}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stade</label>
                    <p class="text-lg">${matchData.stade}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Arbitre Principal</label>
                    <p class="text-lg">${matchData.arbitre}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Score</label>
                    <p class="text-2xl font-bold text-blue-600">${matchData.score}</p>
                </div>
                <div class="pt-4 border-t">
                    <div class="flex justify-end space-x-2">
                        ${actionButton}
                        <button onclick="closeModal(this.closest('.fixed'))" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function closeModal(button) {
    const modal = button.closest('.fixed');
    if (modal) {
        modal.remove();
    }
}

function saveMatch(matchId) {
    showNotification('Match sauvegardé avec succès !', 'success');
    closeModal(event.target.closest('.fixed'));
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter des effets de survol aux boutons
    const buttons = document.querySelectorAll('button[onclick]');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Ajouter les événements de recherche
    const searchInput = document.getElementById('searchInput');
    const competitionFilter = document.getElementById('competitionFilter');
    const statusFilter = document.getElementById('statusFilter');
    const journeeFilter = document.getElementById('journeeFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    if (competitionFilter) {
        competitionFilter.addEventListener('change', filterTable);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }
    if (journeeFilter) {
        journeeFilter.addEventListener('change', filterTable);
    }
});
</script>
@endsection
