@extends('layouts.app')

@section('title', 'Rapports & Statistiques - Association')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
                Rapports & Statistiques
            </h1>
            <p class="text-gray-600 mt-2">Génération et export des rapports de compétition</p>
        </div>
        <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux Modules
        </a>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Rapports</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $rapports->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Disponibles</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $rapports->where('statut', 'Disponible')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">En Cours</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $rapports->where('statut', 'En cours')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-download text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Exports Aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-900">12</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="createNewReport()">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-plus text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Nouveau Rapport</h3>
                    <p class="text-sm text-gray-500">Créer un rapport personnalisé</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="scheduleReport()">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-calendar text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Rapport Automatique</h3>
                    <p class="text-sm text-gray-500">Programmer un rapport récurrent</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="manageTemplates()">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-cog text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Modèles</h3>
                    <p class="text-sm text-gray-500">Gérer les modèles de rapport</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des rapports -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Rapports Disponibles</h2>
        </div>
        
        @if($rapports->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom du Rapport</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compétition</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Génération</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formats</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($rapports as $rapport)
                            <tr class="hover:bg-gray-50 cursor-pointer" onclick="viewReport({{ $rapport['id'] }})">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-file-alt text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $rapport['nom'] }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $rapport['id'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($rapport['type'] === 'Classement')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            🏆 Classement
                                        </span>
                                    @elseif($rapport['type'] === 'Statistiques')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            📊 Statistiques
                                        </span>
                                    @elseif($rapport['type'] === 'Discipline')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            ⚖️ Discipline
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $rapport['type'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rapport['competition'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($rapport['date_generation'])->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap status-badge">
                                    @if($rapport['statut'] === 'Disponible')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            ✅ Disponible
                                        </span>
                                    @elseif($rapport['statut'] === 'En cours')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            ⏳ En cours
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $rapport['statut'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-1">
                                        @foreach($rapport['formats'] as $format)
                                            @if($format === 'PDF')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    PDF
                                                </span>
                                            @elseif($format === 'Excel')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Excel
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium actions-cell">
                                    <div class="flex space-x-2" onclick="event.stopPropagation()">
                                        @if($rapport['statut'] === 'Disponible')
                                            @foreach($rapport['formats'] as $format)
                                                <button onclick="downloadReport({{ $rapport['id'] }}, '{{ $format }}')" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" title="Télécharger {{ $format }}">
                                                    @if($format === 'PDF')
                                                        <i class="fas fa-file-pdf mr-1"></i>PDF
                                                    @elseif($format === 'Excel')
                                                        <i class="fas fa-file-excel mr-1"></i>Excel
                                                    @endif
                                                </button>
                                            @endforeach
                                        @else
                                            <button onclick="generateReport({{ $rapport['id'] }})" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" title="Générer">
                                                <i class="fas fa-play mr-1"></i>Générer
                                            </button>
                                        @endif
                                        <button onclick="viewReport({{ $rapport['id'] }})" class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" title="Voir détails">
                                            <i class="fas fa-eye mr-1"></i>Détails
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6">
                <div class="text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun rapport</h3>
                    <p class="mt-1 text-sm text-gray-500">Aucun rapport n'est disponible pour le moment.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Fonction pour télécharger un rapport
function downloadReport(reportId, format) {
    // Simulation d'un téléchargement
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    // Animation de chargement
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Téléchargement...';
    button.disabled = true;
    
    // Simulation du téléchargement
    setTimeout(() => {
        // Créer un fichier de test pour le téléchargement
        const content = `Rapport #${reportId} - Format ${format}\n\nCeci est un exemple de rapport généré le ${new Date().toLocaleDateString('fr-FR')}.\n\nContenu du rapport:\n- Statistiques des matchs\n- Classements des équipes\n- Données des joueurs\n- Analyses des performances`;
        
        const blob = new Blob([content], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `rapport_${reportId}_${format.toLowerCase()}.txt`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        // Restaurer le bouton
        button.innerHTML = originalText;
        button.disabled = false;
        
        // Afficher un message de succès
        showNotification('Rapport téléchargé avec succès!', 'success');
    }, 2000);
}

// Fonction pour générer un rapport
function generateReport(reportId) {
    if (confirm('Voulez-vous générer ce rapport ?')) {
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        
        // Animation de génération
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Génération...';
        button.disabled = true;
        
        // Simulation de la génération
        setTimeout(() => {
            // Mettre à jour le statut du rapport dans l'interface
            const row = button.closest('tr');
            const statusCell = row.querySelector('.status-badge');
            if (statusCell) {
                statusCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disponible</span>';
            }
            
            // Ajouter les boutons de téléchargement
            const actionsCell = row.querySelector('.actions-cell');
            if (actionsCell) {
                actionsCell.innerHTML = `
                    <div class="flex space-x-2" onclick="event.stopPropagation()">
                        <button onclick="downloadReport(${reportId}, 'PDF')" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" title="Télécharger PDF">
                            <i class="fas fa-file-pdf mr-1"></i>PDF
                        </button>
                        <button onclick="downloadReport(${reportId}, 'Excel')" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" title="Télécharger Excel">
                            <i class="fas fa-file-excel mr-1"></i>Excel
                        </button>
                        <button onclick="viewReport(${reportId})" class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" title="Voir détails">
                            <i class="fas fa-eye mr-1"></i>Détails
                        </button>
                    </div>
                `;
            }
            
            // Restaurer le bouton
            button.innerHTML = originalText;
            button.disabled = false;
            
            // Afficher un message de succès
            showNotification('Rapport généré avec succès!', 'success');
        }, 3000);
    }
}

// Fonction pour voir les détails d'un rapport
function viewReport(reportId) {
    // Créer un modal de détails
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Détails du Rapport #${reportId}</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom du rapport</label>
                        <p class="mt-1 text-sm text-gray-900">Rapport détaillé #${reportId}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <p class="mt-1 text-sm text-gray-900">Rapport de statistiques</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Compétition</label>
                        <p class="mt-1 text-sm text-gray-900">Championnat Régional U19</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de génération</label>
                        <p class="mt-1 text-sm text-gray-900">${new Date().toLocaleDateString('fr-FR')}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disponible</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Formats disponibles</label>
                        <div class="mt-1 flex space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">PDF</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Excel</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Fermer
                    </button>
                    <button onclick="downloadReport(${reportId}, 'PDF'); closeModal();" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Télécharger PDF
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Fonction pour fermer le modal
function closeModal() {
    const modal = document.querySelector('.fixed.inset-0.bg-gray-600');
    if (modal) {
        modal.remove();
    }
}

// Fonction pour créer un nouveau rapport
function createNewReport() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Créer un Nouveau Rapport</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom du rapport</label>
                        <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Rapport mensuel">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type de rapport</label>
                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option>Classement</option>
                            <option>Statistiques</option>
                            <option>Discipline</option>
                            <option>Financier</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Compétition</label>
                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option>Championnat Régional U19</option>
                            <option>Coupe de Tunisie</option>
                            <option>Championnat National</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Formats</label>
                        <div class="mt-1 space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <span class="ml-2 text-sm text-gray-700">PDF</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <span class="ml-2 text-sm text-gray-700">Excel</span>
                            </label>
                        </div>
                    </div>
                </form>
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Annuler
                    </button>
                    <button onclick="createReport(); closeModal();" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Créer le rapport
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Fonction pour créer le rapport
function createReport() {
    showNotification('Rapport créé avec succès!', 'success');
    // Ici vous pourriez ajouter le nouveau rapport à la liste
}

// Fonction pour programmer un rapport
function scheduleReport() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Programmer un Rapport</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom du rapport</label>
                        <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Rapport hebdomadaire">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fréquence</label>
                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option>Quotidien</option>
                            <option>Hebdomadaire</option>
                            <option>Mensuel</option>
                            <option>Trimestriel</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jour de la semaine</label>
                        <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option>Lundi</option>
                            <option>Mardi</option>
                            <option>Mercredi</option>
                            <option>Jeudi</option>
                            <option>Vendredi</option>
                            <option>Samedi</option>
                            <option>Dimanche</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Heure</label>
                        <input type="time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="09:00">
                    </div>
                </form>
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Annuler
                    </button>
                    <button onclick="scheduleReportConfirm(); closeModal();" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Programmer
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Fonction pour confirmer la programmation
function scheduleReportConfirm() {
    showNotification('Rapport programmé avec succès!', 'success');
}

// Fonction pour gérer les modèles
function manageTemplates() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Gestion des Modèles de Rapport</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Modèle Classement</h4>
                            <p class="text-sm text-gray-600 mb-3">Modèle pour les rapports de classement des équipes</p>
                            <button onclick="editTemplate('classement')" class="text-blue-600 hover:text-blue-800 text-sm">Modifier</button>
                        </div>
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Modèle Statistiques</h4>
                            <p class="text-sm text-gray-600 mb-3">Modèle pour les rapports de statistiques des joueurs</p>
                            <button onclick="editTemplate('statistiques')" class="text-blue-600 hover:text-blue-800 text-sm">Modifier</button>
                        </div>
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Modèle Discipline</h4>
                            <p class="text-sm text-gray-600 mb-3">Modèle pour les rapports de discipline</p>
                            <button onclick="editTemplate('discipline')" class="text-blue-600 hover:text-blue-800 text-sm">Modifier</button>
                        </div>
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Modèle Financier</h4>
                            <p class="text-sm text-gray-600 mb-3">Modèle pour les rapports financiers</p>
                            <button onclick="editTemplate('financier')" class="text-blue-600 hover:text-blue-800 text-sm">Modifier</button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Fermer
                    </button>
                    <button onclick="createNewTemplate(); closeModal();" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Nouveau Modèle
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Fonction pour éditer un modèle
function editTemplate(type) {
    showNotification(`Ouverture de l'éditeur pour le modèle ${type}`, 'info');
}

// Fonction pour créer un nouveau modèle
function createNewTemplate() {
    showNotification('Création d\'un nouveau modèle...', 'info');
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
</script>
@endsection
