@extends('layouts.app')

@section('title', 'Import en Lot - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">📥 Import en Lot</h1>
            <p class="text-gray-600 mt-2">Importez plusieurs joueurs simultanément via JSON ou CSV</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('players.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                <span class="mr-2">←</span>
                <span class="hidden sm:inline">Retour aux joueurs</span>
                <span class="sm:hidden">Retour</span>
            </a>
            <button onclick="exportPlayers()" 
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                <span class="mr-2">📤</span>
                <span class="hidden sm:inline">Exporter</span>
                <span class="sm:hidden">Export</span>
            </button>
        </div>
    </div>

    <!-- Import Methods -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- File Upload -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">📁 Import par Fichier</h2>
            
            <div id="fileUploadArea" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors cursor-pointer">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>
                <p class="text-lg font-medium text-gray-700 mb-2">Glissez-déposez votre fichier ici</p>
                <p class="text-sm text-gray-500 mb-4">ou cliquez pour sélectionner</p>
                <input type="file" id="fileInput" accept=".json,.csv" class="hidden">
                <button onclick="document.getElementById('fileInput').click()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Choisir un fichier
                </button>
                <p class="text-xs text-gray-400 mt-2">Formats supportés: JSON, CSV (max 10MB)</p>
            </div>

            <div id="filePreview" class="hidden mt-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Fichier sélectionné:</span>
                        <button onclick="clearFile()" class="text-red-600 hover:text-red-800 text-sm">Supprimer</button>
                    </div>
                    <div id="fileInfo" class="text-sm text-gray-600"></div>
                    <div id="fileValidation" class="mt-2"></div>
                </div>
            </div>
        </div>

        <!-- Manual JSON Input -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">📝 Import JSON Manuel</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Données JSON</label>
                <textarea id="jsonInput" rows="12" 
                          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                          placeholder='[
  {
    "name": "Lionel Messi",
    "first_name": "Lionel",
    "last_name": "Messi",
    "date_of_birth": "1987-06-24",
    "nationality": "Argentina",
    "position": "RW",
    "overall_rating": 93
  }
]'></textarea>
            </div>

            <div class="flex gap-3">
                <button onclick="validateJson()" 
                        class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    🔍 Valider JSON
                </button>
                <button onclick="loadSampleData()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    📋 Exemple
                </button>
            </div>

            <div id="jsonValidation" class="mt-4"></div>
        </div>
    </div>

    <!-- Import Options -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">⚙️ Options d'Import</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mode d'import</label>
                <select id="importMode" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="create_update">Créer et mettre à jour</option>
                    <option value="create_only">Créer uniquement</option>
                    <option value="update_only">Mettre à jour uniquement</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Identifiant de correspondance</label>
                <select id="matchField" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="fifa_connect_id">FIFA Connect ID</option>
                    <option value="name">Nom complet</option>
                    <option value="email">Email</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gestion des erreurs</label>
                <select id="errorHandling" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="continue">Continuer en cas d'erreur</option>
                    <option value="stop">Arrêter à la première erreur</option>
                    <option value="skip">Ignorer les lignes en erreur</option>
                </select>
            </div>
        </div>

        <div class="mt-6">
            <label class="flex items-center">
                <input type="checkbox" id="validateData" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                <span class="ml-2 text-sm text-gray-700">Valider les données avant import</span>
            </label>
        </div>
    </div>

    <!-- Import Progress -->
    <div id="importProgress" class="hidden bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">📊 Progression de l'Import</h2>
        
        <div class="mb-4">
            <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span id="progressText">Préparation...</span>
                <span id="progressPercent">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        <div id="importStats" class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
            <div class="bg-green-50 rounded-lg p-3">
                <div class="text-2xl font-bold text-green-600" id="importedCount">0</div>
                <div class="text-sm text-green-700">Importés</div>
            </div>
            <div class="bg-blue-50 rounded-lg p-3">
                <div class="text-2xl font-bold text-blue-600" id="updatedCount">0</div>
                <div class="text-sm text-blue-700">Mis à jour</div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-3">
                <div class="text-2xl font-bold text-yellow-600" id="skippedCount">0</div>
                <div class="text-sm text-yellow-700">Ignorés</div>
            </div>
            <div class="bg-red-50 rounded-lg p-3">
                <div class="text-2xl font-bold text-red-600" id="errorCount">0</div>
                <div class="text-sm text-red-700">Erreurs</div>
            </div>
        </div>
    </div>

    <!-- Import Results -->
    <div id="importResults" class="hidden bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">📋 Résultats de l'Import</h2>
        
        <div id="resultsContent"></div>
        
        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <button onclick="downloadResults()" 
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                📥 Télécharger le rapport
            </button>
            <button onclick="clearResults()" 
                    class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                Effacer les résultats
            </button>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row justify-center gap-4">
        <button id="startImportBtn" onclick="startImport()" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-200 text-lg disabled:opacity-50 disabled:cursor-not-allowed"
                disabled>
            🚀 Démarrer l'Import
        </button>
        <button onclick="resetForm()" 
                class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-200 text-lg">
            🔄 Réinitialiser
        </button>
    </div>
</div>

<script>
let importData = null;
let importResults = null;

// File upload handling
document.getElementById('fileInput').addEventListener('change', handleFileSelect);
document.getElementById('fileUploadArea').addEventListener('dragover', handleDragOver);
document.getElementById('fileUploadArea').addEventListener('drop', handleDrop);
document.getElementById('jsonInput').addEventListener('input', debounce(validateJson, 500));

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('border-blue-400', 'bg-blue-50');
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('border-blue-400', 'bg-blue-50');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFile(files[0]);
    }
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        handleFile(file);
    }
}

function handleFile(file) {
    const fileInfo = document.getElementById('fileInfo');
    const filePreview = document.getElementById('filePreview');
    const fileValidation = document.getElementById('fileValidation');
    
    fileInfo.innerHTML = `
        <div class="flex items-center">
            <span class="font-medium">${file.name}</span>
            <span class="ml-2 text-gray-500">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
        </div>
    `;
    
    filePreview.classList.remove('hidden');
    
    // Validate file type
    if (!file.name.match(/\.(json|csv)$/i)) {
        fileValidation.innerHTML = '<div class="text-red-600 text-sm">Format de fichier non supporté. Utilisez JSON ou CSV.</div>';
        return;
    }
    
    if (file.size > 10 * 1024 * 1024) { // 10MB
        fileValidation.innerHTML = '<div class="text-red-600 text-sm">Fichier trop volumineux. Taille maximale: 10MB.</div>';
        return;
    }
    
    // Read and parse file
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            if (file.name.toLowerCase().endsWith('.json')) {
                importData = JSON.parse(e.target.result);
            } else if (file.name.toLowerCase().endsWith('.csv')) {
                importData = parseCSV(e.target.result);
            }
            
            validateImportData(importData);
        } catch (error) {
            fileValidation.innerHTML = `<div class="text-red-600 text-sm">Erreur de parsing: ${error.message}</div>`;
        }
    };
    reader.readAsText(file);
}

function parseCSV(csvText) {
    const lines = csvText.split('\n');
    const headers = lines[0].split(',').map(h => h.trim().replace(/"/g, ''));
    const data = [];
    
    for (let i = 1; i < lines.length; i++) {
        if (lines[i].trim()) {
            const values = lines[i].split(',').map(v => v.trim().replace(/"/g, ''));
            const row = {};
            headers.forEach((header, index) => {
                row[header] = values[index] || '';
            });
            data.push(row);
        }
    }
    
    return data;
}

function validateJson() {
    const jsonInput = document.getElementById('jsonInput').value.trim();
    const validation = document.getElementById('jsonValidation');
    
    if (!jsonInput) {
        validation.innerHTML = '';
        return;
    }
    
    try {
        importData = JSON.parse(jsonInput);
        validateImportData(importData);
    } catch (error) {
        validation.innerHTML = `<div class="text-red-600 text-sm">❌ JSON invalide: ${error.message}</div>`;
        importData = null;
    }
}

function validateImportData(data) {
    if (!Array.isArray(data)) {
        showValidationError('Les données doivent être un tableau d\'objets');
        return;
    }
    
    if (data.length === 0) {
        showValidationError('Le fichier ne contient aucune donnée');
        return;
    }
    
    if (data.length > 100) {
        showValidationError('Maximum 100 joueurs par import');
        return;
    }
    
    const requiredFields = ['name', 'first_name', 'last_name', 'date_of_birth', 'nationality', 'position'];
    const errors = [];
    
    data.forEach((player, index) => {
        requiredFields.forEach(field => {
            if (!player[field]) {
                errors.push(`Ligne ${index + 1}: Champ "${field}" manquant`);
            }
        });
        
        // Validate date format
        if (player.date_of_birth && !isValidDate(player.date_of_birth)) {
            errors.push(`Ligne ${index + 1}: Date de naissance invalide`);
        }
        
        // Validate rating range
        if (player.overall_rating && (player.overall_rating < 1 || player.overall_rating > 99)) {
            errors.push(`Ligne ${index + 1}: Note générale doit être entre 1 et 99`);
        }
    });
    
    if (errors.length > 0) {
        showValidationError(errors.join('<br>'));
        return;
    }
    
    showValidationSuccess(`${data.length} joueurs validés avec succès`);
    updateImportButton();
}

function showValidationError(message) {
    const validation = document.getElementById('jsonValidation');
    validation.innerHTML = `<div class="text-red-600 text-sm">❌ ${message}</div>`;
    importData = null;
    updateImportButton();
}

function showValidationSuccess(message) {
    const validation = document.getElementById('jsonValidation');
    validation.innerHTML = `<div class="text-green-600 text-sm">✅ ${message}</div>`;
    updateImportButton();
}

function updateImportButton() {
    const button = document.getElementById('startImportBtn');
    button.disabled = !importData;
}

function startImport() {
    if (!importData) return;
    
    const progress = document.getElementById('importProgress');
    const results = document.getElementById('importResults');
    
    progress.classList.remove('hidden');
    results.classList.add('hidden');
    
    // Reset stats
    document.getElementById('importedCount').textContent = '0';
    document.getElementById('updatedCount').textContent = '0';
    document.getElementById('skippedCount').textContent = '0';
    document.getElementById('errorCount').textContent = '0';
    
    const importOptions = {
        players: importData,
        mode: document.getElementById('importMode').value,
        match_field: document.getElementById('matchField').value,
        error_handling: document.getElementById('errorHandling').value,
        validate_data: document.getElementById('validateData').checked
    };
    
    // Simulate progress
    let currentProgress = 0;
    const progressInterval = setInterval(() => {
        currentProgress += Math.random() * 10;
        if (currentProgress > 90) currentProgress = 90;
        
        document.getElementById('progressBar').style.width = currentProgress + '%';
        document.getElementById('progressPercent').textContent = Math.round(currentProgress) + '%';
        document.getElementById('progressText').textContent = 'Import en cours...';
    }, 200);
    
    fetch('{{ route("players.bulk-import.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(importOptions)
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(progressInterval);
        
        document.getElementById('progressBar').style.width = '100%';
        document.getElementById('progressPercent').textContent = '100%';
        document.getElementById('progressText').textContent = 'Import terminé';
        
        if (data.success) {
            document.getElementById('importedCount').textContent = data.imported_count;
            document.getElementById('updatedCount').textContent = data.updated_count;
            document.getElementById('errorCount').textContent = data.errors.length;
            
            showImportResults(data);
        } else {
            showNotification('Erreur lors de l\'import: ' + data.message, 'error');
        }
    })
    .catch(error => {
        clearInterval(progressInterval);
        console.error('Import failed:', error);
        showNotification('Erreur lors de l\'import', 'error');
    });
}

function showImportResults(data) {
    const results = document.getElementById('importResults');
    const content = document.getElementById('resultsContent');
    
    let html = `
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <div class="flex items-center">
                <div class="w-5 h-5 bg-green-500 rounded-full mr-3"></div>
                <span class="text-green-800 font-medium">Import terminé avec succès</span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="text-2xl font-bold text-green-600">${data.imported_count}</div>
                <div class="text-sm text-gray-600">Nouveaux joueurs</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="text-2xl font-bold text-blue-600">${data.updated_count}</div>
                <div class="text-sm text-gray-600">Joueurs mis à jour</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="text-2xl font-bold text-gray-600">${data.total_processed}</div>
                <div class="text-sm text-gray-600">Total traité</div>
            </div>
        </div>
    `;
    
    if (data.errors.length > 0) {
        html += `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-red-800 font-medium mb-2">Erreurs rencontrées (${data.errors.length})</h3>
                <div class="text-sm text-red-700 max-h-40 overflow-y-auto">
                    ${data.errors.map(error => `<div class="mb-1">• ${error}</div>`).join('')}
                </div>
            </div>
        `;
    }
    
    content.innerHTML = html;
    results.classList.remove('hidden');
    importResults = data;
}

function exportPlayers() {
    const params = new URLSearchParams();
    
    // Add filters if needed
    const position = document.getElementById('positionFilter')?.value;
    const nationality = document.getElementById('nationalityFilter')?.value;
    
    if (position) params.append('position', position);
    if (nationality) params.append('nationality', nationality);
    
    fetch(`{{ route('players.export') }}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const blob = new Blob([JSON.stringify(data.data, null, 2)], { type: 'application/json' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = data.filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                showNotification(`Export réussi: ${data.total} joueurs`, 'success');
            } else {
                showNotification('Erreur lors de l\'export: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Export failed:', error);
            showNotification('Erreur lors de l\'export', 'error');
        });
}

function loadSampleData() {
    const sampleData = [
        {
            "fifa_connect_id": "sample_001",
            "name": "Lionel Messi",
            "first_name": "Lionel",
            "last_name": "Messi",
            "date_of_birth": "1987-06-24",
            "nationality": "Argentina",
            "position": "RW",
            "height": 170,
            "weight": 72,
            "overall_rating": 93,
            "potential_rating": 93,
            "value_eur": 50000000,
            "wage_eur": 500000,
            "preferred_foot": "Left",
            "weak_foot": 4,
            "skill_moves": 4,
            "international_reputation": 5,
            "work_rate": "Medium/Low",
            "body_type": "Lean",
            "real_face": true,
            "release_clause_eur": 100000000,
            "player_face_url": "https://example.com/messi.jpg",
            "club_logo_url": "https://example.com/inter-miami.png",
            "nation_flag_url": "https://example.com/argentina.png",
            "contract_valid_until": "2025-12-31",
            "fifa_version": "FIFA 24"
        },
        {
            "fifa_connect_id": "sample_002",
            "name": "Cristiano Ronaldo",
            "first_name": "Cristiano",
            "last_name": "Ronaldo",
            "date_of_birth": "1985-02-05",
            "nationality": "Portugal",
            "position": "ST",
            "height": 187,
            "weight": 83,
            "overall_rating": 88,
            "potential_rating": 88,
            "value_eur": 15000000,
            "wage_eur": 200000,
            "preferred_foot": "Right",
            "weak_foot": 4,
            "skill_moves": 5,
            "international_reputation": 5,
            "work_rate": "High/High",
            "body_type": "Athletic",
            "real_face": true,
            "release_clause_eur": 50000000,
            "player_face_url": "https://example.com/ronaldo.jpg",
            "club_logo_url": "https://example.com/al-nassr.png",
            "nation_flag_url": "https://example.com/portugal.png",
            "contract_valid_until": "2025-06-30",
            "fifa_version": "FIFA 24"
        }
    ];
    
    document.getElementById('jsonInput').value = JSON.stringify(sampleData, null, 2);
    validateJson();
}

function clearFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').classList.add('hidden');
    document.getElementById('fileInfo').innerHTML = '';
    document.getElementById('fileValidation').innerHTML = '';
    importData = null;
    updateImportButton();
}

function clearResults() {
    document.getElementById('importResults').classList.add('hidden');
    importResults = null;
}

function resetForm() {
    document.getElementById('jsonInput').value = '';
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').classList.add('hidden');
    document.getElementById('importProgress').classList.add('hidden');
    document.getElementById('importResults').classList.add('hidden');
    document.getElementById('jsonValidation').innerHTML = '';
    document.getElementById('fileValidation').innerHTML = '';
    
    importData = null;
    importResults = null;
    updateImportButton();
}

function downloadResults() {
    if (!importResults) return;
    
    const report = {
        timestamp: new Date().toISOString(),
        summary: {
            imported_count: importResults.imported_count,
            updated_count: importResults.updated_count,
            total_processed: importResults.total_processed,
            errors_count: importResults.errors.length
        },
        errors: importResults.errors
    };
    
    const blob = new Blob([JSON.stringify(report, null, 2)], { type: 'application/json' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `import_report_${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}

function isValidDate(dateString) {
    const date = new Date(dateString);
    return date instanceof Date && !isNaN(date);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateImportButton();
});
</script>
@endsection 