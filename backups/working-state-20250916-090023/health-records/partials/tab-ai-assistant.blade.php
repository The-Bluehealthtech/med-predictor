<!-- Onglet: Assistant IA -->
<div class="space-y-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            <h3 class="text-xl font-semibold text-blue-900">🤖 Assistant IA Médical</h3>
        </div>
        <p class="text-blue-700 mb-4">Décrivez les symptômes et observations cliniques pour une analyse automatique</p>
        
        <div class="space-y-4">
            <div>
                <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes Cliniques
                </label>
                <textarea 
                    id="clinical_notes" 
                    name="clinical_notes" 
                    rows="6" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Exemple: Patient se plaint de douleurs thoraciques depuis 2 jours, tension artérielle 140/90, fréquence cardiaque 85 bpm. Pas d'essoufflement ni de vertiges..."
                >{{ old('clinical_notes') }}</textarea>
            </div>
            
            <div class="flex space-x-4">
                <button 
                    type="button" 
                    id="ai-analyze-btn"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center"
                >
                    <span class="mr-2">🔍</span>
                    Analyser avec l'IA
                </button>
                <button 
                    type="button" 
                    id="clear-notes-btn"
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Effacer
                </button>
            </div>
            
            <div id="ai-results" class="hidden bg-white border border-gray-200 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-3">Analyse IA</h4>
                <div id="ai-content" class="text-sm text-gray-700"></div>
            </div>
        </div>
    </div>

    <!-- ICD-11 Diagnostic Search -->
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-purple-900 mb-4">🔍 Recherche ICD-11</h3>
        <p class="text-purple-700 mb-4">Recherchez des codes de diagnostic ICD-11 pour standardiser les diagnostics</p>
        
        <div class="space-y-4">
            <div>
                <label for="icd11_search" class="block text-sm font-medium text-gray-700 mb-2">
                    Rechercher un diagnostic
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="icd11_search" 
                        placeholder="Exemple: diabète, fracture, infection..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                    <div id="icd11_results" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                </div>
            </div>
            
            <div id="selected_icd11" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Diagnostic sélectionné:</label>
                <div class="flex items-center justify-between p-3 bg-purple-100 rounded-md">
                    <div>
                        <span id="selected_icd11_code" class="font-semibold text-purple-800"></span> - 
                        <span id="selected_icd11_label" class="text-purple-700"></span>
                    </div>
                    <button type="button" onclick="clearICD11Selection()" class="text-purple-600 hover:text-purple-800">×</button>
                </div>
                <input type="hidden" name="icd11_diagnostic" id="icd11_diagnostic" value="{{ old('icd11_diagnostic') }}">
            </div>
        </div>
    </div>

    <!-- AI Recommendations -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-green-900 mb-4">💡 Recommandations IA</h3>
        <p class="text-green-700 mb-4">Suggestions basées sur l'analyse des symptômes et des données cliniques</p>
        
        <div id="ai-recommendations" class="space-y-3">
            <div class="bg-white p-4 rounded-lg border border-green-200">
                <h4 class="font-medium text-green-800 mb-2">🔍 Tests Recommandés</h4>
                <div id="recommended-tests" class="text-sm text-gray-700">
                    <!-- Les tests recommandés seront affichés ici -->
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-green-200">
                <h4 class="font-medium text-green-800 mb-2">⚠️ Alertes</h4>
                <div id="ai-alerts" class="text-sm text-gray-700">
                    <!-- Les alertes seront affichées ici -->
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-lg border border-green-200">
                <h4 class="font-medium text-green-800 mb-2">📋 Actions Suggérées</h4>
                <div id="suggested-actions" class="text-sm text-gray-700">
                    <!-- Les actions suggérées seront affichées ici -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ICD-11 Search Functionality
let icd11SearchTimeout;

document.getElementById('icd11_search').addEventListener('input', function(e) {
    const query = e.target.value;
    
    clearTimeout(icd11SearchTimeout);
    
    if (query.length < 2) {
        hideICD11Results();
        return;
    }
    
    icd11SearchTimeout = setTimeout(() => {
        searchICD11(query);
    }, 300);
});

function searchICD11(query) {
    const resultsDiv = document.getElementById('icd11_results');
    
    fetch(`/api/v1/icd11/search?query=${encodeURIComponent(query)}&language=fr&limit=10`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                displayICD11Results(data.data);
            } else {
                displayICD11Results(getFallbackICD11Results(query));
            }
        })
        .catch(error => {
            console.error('Erreur lors de la recherche ICD-11:', error);
            displayICD11Results(getFallbackICD11Results(query));
        });
}

function displayICD11Results(results) {
    const resultsDiv = document.getElementById('icd11_results');
    resultsDiv.innerHTML = '';
    
    results.forEach(item => {
        const div = document.createElement('div');
        div.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200';
        div.innerHTML = `
            <div class="font-semibold text-gray-800">${item.code}</div>
            <div class="text-sm text-gray-600">${item.label}</div>
        `;
        div.onclick = () => selectICD11(item.code, item.label);
        resultsDiv.appendChild(div);
    });
    
    resultsDiv.classList.remove('hidden');
}

function selectICD11(code, label) {
    document.getElementById('selected_icd11_code').textContent = code;
    document.getElementById('selected_icd11_label').textContent = label;
    document.getElementById('icd11_diagnostic').value = code;
    document.getElementById('selected_icd11').classList.remove('hidden');
    document.getElementById('icd11_search').value = '';
    hideICD11Results();
}

function clearICD11Selection() {
    document.getElementById('selected_icd11').classList.add('hidden');
    document.getElementById('icd11_diagnostic').value = '';
    document.getElementById('icd11_search').value = '';
}

function hideICD11Results() {
    document.getElementById('icd11_results').classList.add('hidden');
}

function getFallbackICD11Results(query) {
    // Résultats de fallback pour la démo
    return [
        { code: 'E11.9', label: 'Diabète sucré de type 2 sans complications' },
        { code: 'I10', label: 'Hypertension essentielle (primitive)' },
        { code: 'M79.3', label: 'Douleur dans les membres' },
        { code: 'S93.4', label: 'Entorse de la cheville' }
    ].filter(item => 
        item.label.toLowerCase().includes(query.toLowerCase()) ||
        item.code.toLowerCase().includes(query.toLowerCase())
    );
}

// AI Analysis Functionality
document.getElementById('ai-analyze-btn').addEventListener('click', function() {
    const clinicalNotes = document.getElementById('clinical_notes').value;
    
    if (!clinicalNotes.trim()) {
        alert('Veuillez saisir des notes cliniques pour l\'analyse IA');
        return;
    }
    
    // Simulation de l'analyse IA
    analyzeWithAI(clinicalNotes);
});

function analyzeWithAI(notes) {
    const aiContent = document.getElementById('ai-content');
    const aiResults = document.getElementById('ai-results');
    
    // Simulation d'analyse IA
    const analysis = {
        symptoms: extractSymptoms(notes),
        riskFactors: identifyRiskFactors(notes),
        recommendations: generateRecommendations(notes),
        urgency: assessUrgency(notes)
    };
    
    aiContent.innerHTML = `
        <div class="space-y-4">
            <div>
                <h5 class="font-semibold text-gray-800 mb-2">🔍 Symptômes identifiés:</h5>
                <ul class="list-disc list-inside text-gray-700">
                    ${analysis.symptoms.map(s => `<li>${s}</li>`).join('')}
                </ul>
            </div>
            
            <div>
                <h5 class="font-semibold text-gray-800 mb-2">⚠️ Facteurs de risque:</h5>
                <ul class="list-disc list-inside text-gray-700">
                    ${analysis.riskFactors.map(r => `<li>${r}</li>`).join('')}
                </ul>
            </div>
            
            <div>
                <h5 class="font-semibold text-gray-800 mb-2">💡 Recommandations:</h5>
                <ul class="list-disc list-inside text-gray-700">
                    ${analysis.recommendations.map(r => `<li>${r}</li>`).join('')}
                </ul>
            </div>
            
            <div>
                <h5 class="font-semibold text-gray-800 mb-2">🚨 Niveau d'urgence:</h5>
                <span class="px-2 py-1 rounded text-sm ${analysis.urgency === 'high' ? 'bg-red-100 text-red-800' : analysis.urgency === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}">
                    ${analysis.urgency === 'high' ? 'Élevé' : analysis.urgency === 'medium' ? 'Modéré' : 'Faible'}
                </span>
            </div>
        </div>
    `;
    
    aiResults.classList.remove('hidden');
}

function extractSymptoms(notes) {
    const symptoms = [];
    const keywords = ['douleur', 'fièvre', 'fatigue', 'essoufflement', 'vertiges', 'nausée', 'vomissements'];
    
    keywords.forEach(keyword => {
        if (notes.toLowerCase().includes(keyword)) {
            symptoms.push(keyword.charAt(0).toUpperCase() + keyword.slice(1));
        }
    });
    
    return symptoms.length > 0 ? symptoms : ['Aucun symptôme spécifique identifié'];
}

function identifyRiskFactors(notes) {
    const riskFactors = [];
    const keywords = ['hypertension', 'diabète', 'obésité', 'tabac', 'alcool', 'antécédents'];
    
    keywords.forEach(keyword => {
        if (notes.toLowerCase().includes(keyword)) {
            riskFactors.push(keyword.charAt(0).toUpperCase() + keyword.slice(1));
        }
    });
    
    return riskFactors.length > 0 ? riskFactors : ['Aucun facteur de risque identifié'];
}

function generateRecommendations(notes) {
    const recommendations = ['Examen clinique complet', 'Analyses sanguines de routine'];
    
    if (notes.toLowerCase().includes('douleur')) {
        recommendations.push('Évaluation de la douleur');
    }
    
    if (notes.toLowerCase().includes('cardiaque') || notes.toLowerCase().includes('cœur')) {
        recommendations.push('Électrocardiogramme');
    }
    
    return recommendations;
}

function assessUrgency(notes) {
    const urgentKeywords = ['douleur thoracique', 'essoufflement', 'perte de conscience', 'saignement'];
    const mediumKeywords = ['fièvre', 'douleur', 'fatigue'];
    
    for (const keyword of urgentKeywords) {
        if (notes.toLowerCase().includes(keyword)) {
            return 'high';
        }
    }
    
    for (const keyword of mediumKeywords) {
        if (notes.toLowerCase().includes(keyword)) {
            return 'medium';
        }
    }
    
    return 'low';
}

// Clear notes functionality
document.getElementById('clear-notes-btn').addEventListener('click', function() {
    document.getElementById('clinical_notes').value = '';
    document.getElementById('ai-results').classList.add('hidden');
});
</script> 