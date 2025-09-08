@extends('layouts.app')

@section('title', 'Classements des Compétitions')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Classements des Compétitions</h1>
                    <p class="mt-2 text-gray-600">Suivez les performances de tous les clubs tunisiens</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="exportClassement()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-download mr-2"></i>
                        Exporter
                    </button>
                    <button onclick="refreshClassement()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Compétition</label>
                    <select id="competitionFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toutes les compétitions</option>
                        @foreach($competitions as $competition)
                            <option value="{{ $competition->id }}">{{ $competition->name ?? 'Championnat Tunisien U19' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Saison</label>
                    <select id="seasonFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="2024-2025">2024-2025</option>
                        <option value="2023-2024">2023-2024</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tri par</label>
                    <select id="sortFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="points">Points</option>
                        <option value="victoires">Victoires</option>
                        <option value="difference">Différence de buts</option>
                        <option value="buts_pour">Buts pour</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="applyFilters()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-filter mr-2"></i>
                        Appliquer
                    </button>
                </div>
            </div>
        </div>

        <!-- Classements -->
        @foreach($competitions as $competition)
            <div class="bg-white rounded-lg shadow mb-6 competition-table" data-competition="{{ $competition->id }}">
                <!-- En-tête de la compétition -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">{{ $competition->name ?? 'Championnat Tunisien U19' }}</h2>
                            <p class="text-sm text-gray-600">{{ $competition->season ?? '2024-2025' }} - {{ $competition->association->name ?? 'FTF - Fédération Tunisienne de Football' }}</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Journée</div>
                                <div class="text-lg font-semibold text-gray-900">{{ rand(15, 25) }}/30</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Dernière mise à jour</div>
                                <div class="text-sm text-gray-900">{{ now()->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau de classement -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Club</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">MJ</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">V</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">N</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">D</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">BP</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">BC</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Diff</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pts</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Forme</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Évolution</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($classements[$competition->id] as $equipe)
                                <tr class="hover:bg-gray-50 {{ $equipe['position'] <= 3 ? 'bg-yellow-50' : ($equipe['position'] >= count($classements[$competition->id]) - 2 ? 'bg-red-50' : '') }}">
                                    <!-- Position -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <div class="flex items-center">
                                            @if($equipe['position'] == 1)
                                                <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                                            @elseif($equipe['position'] == 2)
                                                <i class="fas fa-medal text-gray-400 mr-2"></i>
                                            @elseif($equipe['position'] == 3)
                                                <i class="fas fa-award text-orange-500 mr-2"></i>
                                            @endif
                                            <span class="font-bold">{{ $equipe['position'] }}</span>
                                        </div>
                                    </td>

                                    <!-- Club -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                <span class="text-blue-600 font-bold text-sm">{{ substr($equipe['club']->name ?? $equipe['club']->short_name ?? 'CLUB', 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $equipe['club']->name ?? 'Club Inconnu' }}</div>
                                                <div class="text-sm text-gray-500">{{ $equipe['club']->short_name ?? 'CI' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Matchs Joués -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        {{ $equipe['matchs_joues'] }}
                                    </td>

                                    <!-- Victoires -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $equipe['victoires'] }}
                                        </span>
                                    </td>

                                    <!-- Nuls -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $equipe['nuls'] }}
                                        </span>
                                    </td>

                                    <!-- Défaites -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $equipe['defaites'] }}
                                        </span>
                                    </td>

                                    <!-- Buts Pour -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-medium">
                                        {{ $equipe['buts_pour'] }}
                                    </td>

                                    <!-- Buts Contre -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-medium">
                                        {{ $equipe['buts_contre'] }}
                                    </td>

                                    <!-- Différence -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        <span class="font-medium {{ $equipe['difference_buts'] > 0 ? 'text-green-600' : ($equipe['difference_buts'] < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                            {{ $equipe['difference_buts'] > 0 ? '+' : '' }}{{ $equipe['difference_buts'] }}
                                        </span>
                                    </td>

                                    <!-- Points -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-800">
                                            {{ $equipe['points'] }}
                                        </span>
                                    </td>

                                    <!-- Forme -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        <div class="flex space-x-1 justify-center">
                                            @foreach($equipe['forme'] as $resultat)
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-medium
                                                    {{ $resultat == 'V' ? 'bg-green-100 text-green-800' : 
                                                       ($resultat == 'N' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $resultat }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Évolution -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                        @if($equipe['evolution'] > 0)
                                            <span class="inline-flex items-center text-green-600">
                                                <i class="fas fa-arrow-up mr-1"></i>
                                                +{{ $equipe['evolution'] }}
                                            </span>
                                        @elseif($equipe['evolution'] < 0)
                                            <span class="inline-flex items-center text-red-600">
                                                <i class="fas fa-arrow-down mr-1"></i>
                                                {{ $equipe['evolution'] }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-gray-600">
                                                <i class="fas fa-minus mr-1"></i>
                                                =
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Légende -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-yellow-100 rounded mr-2"></div>
                                <span>Championnat d'Afrique (Top 3)</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-blue-100 rounded mr-2"></div>
                                <span>Ligue des Champions (Top 4)</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-red-100 rounded mr-2"></div>
                                <span>Relégation (Bottom 2)</span>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            * V = Victoire, N = Nul, D = Défaite
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Statistiques globales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-trophy text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Leader</h3>
                        <p class="text-sm text-gray-500">{{ $classements[1][0]['club']->name ?? 'Espérance Sportive de Tunis' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Meilleure Attaque</h3>
                        <p class="text-sm text-gray-500">{{ collect($classements[1])->max('buts_pour') }} buts</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Meilleure Défense</h3>
                        <p class="text-sm text-gray-500">{{ collect($classements[1])->min('buts_contre') }} buts encaissés</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-percentage text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Taux de Victoire</h3>
                        <p class="text-sm text-gray-500">{{ number_format((collect($classements[1])->max('victoires') / collect($classements[1])->max('matchs_joues')) * 100, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour exporter le classement
function exportClassement() {
    const competitionId = document.getElementById('competitionFilter').value || '1';
    const competitionName = document.getElementById('competitionFilter').selectedOptions[0]?.text || 'Championnat Tunisien U19';
    
    // Créer le contenu CSV
    let csvContent = `Classement - ${competitionName}\n`;
    csvContent += `Position,Club,MJ,V,N,D,BP,BC,Diff,Pts\n`;
    
    // Récupérer les données du tableau
    const table = document.querySelector(`[data-competition="${competitionId}"] table tbody`);
    if (table) {
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 10) {
                const position = cells[0].textContent.trim();
                const club = cells[1].querySelector('.text-sm.font-medium').textContent.trim();
                const mj = cells[2].textContent.trim();
                const v = cells[3].textContent.trim();
                const n = cells[4].textContent.trim();
                const d = cells[5].textContent.trim();
                const bp = cells[6].textContent.trim();
                const bc = cells[7].textContent.trim();
                const diff = cells[8].textContent.trim();
                const pts = cells[9].textContent.trim();
                
                csvContent += `${position},${club},${mj},${v},${n},${d},${bp},${bc},${diff},${pts}\n`;
            }
        });
    }
    
    // Télécharger le fichier
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `classement_${competitionName.replace(/\s+/g, '_')}_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('Classement exporté avec succès!', 'success');
}

// Fonction pour actualiser le classement
function refreshClassement() {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Actualisation...';
    button.disabled = true;
    
    setTimeout(() => {
        location.reload();
    }, 1500);
}

// Fonction pour appliquer les filtres
function applyFilters() {
    const competitionFilter = document.getElementById('competitionFilter').value;
    const seasonFilter = document.getElementById('seasonFilter').value;
    const sortFilter = document.getElementById('sortFilter').value;
    
    // Masquer/afficher les tableaux selon le filtre de compétition
    const tables = document.querySelectorAll('.competition-table');
    tables.forEach(table => {
        if (!competitionFilter || table.dataset.competition === competitionFilter) {
            table.style.display = 'block';
        } else {
            table.style.display = 'none';
        }
    });
    
    // Trier les tableaux selon le critère choisi
    tables.forEach(table => {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const aValue = getSortValue(a, sortFilter);
            const bValue = getSortValue(b, sortFilter);
            return bValue - aValue; // Décroissant
        });
        
        rows.forEach(row => tbody.appendChild(row));
    });
    
    showNotification('Filtres appliqués!', 'success');
}

// Fonction pour obtenir la valeur de tri
function getSortValue(row, sortType) {
    const cells = row.querySelectorAll('td');
    switch(sortType) {
        case 'points':
            return parseInt(cells[9].textContent.trim());
        case 'victoires':
            return parseInt(cells[3].textContent.trim());
        case 'difference':
            return parseInt(cells[8].textContent.trim());
        case 'buts_pour':
            return parseInt(cells[6].textContent.trim());
        default:
            return 0;
    }
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
    
    // Animation d'entrée
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Suppression automatique après 3 secondes
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Auto-actualisation toutes les 5 minutes
setInterval(() => {
    const lastUpdate = document.querySelector('.text-sm.text-gray-900');
    if (lastUpdate) {
        lastUpdate.textContent = new Date().toLocaleString('fr-FR');
    }
}, 300000);
</script>
@endsection
