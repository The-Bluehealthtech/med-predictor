<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Dropdowns</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">🧪 Test des Dropdowns PCMA</h1>
        
        <!-- Medical History Section -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-semibold text-green-900">🏥 Antécédents Médicaux</h3>
                <button type="button" onclick="testDropdowns()" class="ml-4 px-3 py-1 bg-blue-500 text-white rounded text-sm">Test Dropdowns</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="cardiovascular_history" class="block text-sm font-medium text-gray-700 mb-2">
                        Antécédents Cardio-vasculaires
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="cardiovascular_search" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="Rechercher des conditions cardio-vasculaires..."
                               autocomplete="off">
                        <div id="cardiovascular_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden"></div>
                        <input type="hidden" id="cardiovascular_history" name="cardiovascular_history" value="">
                        <div id="cardiovascular_selected" class="mt-2 space-y-1"></div>
                    </div>
                </div>
                
                <div>
                    <label for="surgical_history" class="block text-sm font-medium text-gray-700 mb-2">
                        Antécédents Chirurgicaux
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="surgical_search" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="Rechercher des procédures chirurgicales..."
                               autocomplete="off">
                        <div id="surgical_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden"></div>
                        <input type="hidden" id="surgical_history" name="surgical_history" value="">
                        <div id="surgical_selected" class="mt-2 space-y-1"></div>
                    </div>
                </div>
                
                <div>
                    <label for="medications" class="block text-sm font-medium text-gray-700 mb-2">
                        Médicaments Actuels
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="medication_search" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="Rechercher des médicaments..."
                               autocomplete="off">
                        <div id="medication_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden"></div>
                        <input type="hidden" id="medications" name="medications" value="">
                        <div id="medication_selected" class="mt-2 space-y-1"></div>
                    </div>
                </div>
                
                <div>
                    <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                        Allergies
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="allergy_search" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="Rechercher des allergies..."
                               autocomplete="off">
                        <div id="allergy_results" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden"></div>
                        <input type="hidden" id="allergies" name="allergies" value="">
                        <div id="allergy_selected" class="mt-2 space-y-1"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">📊 Résultats des Tests</h3>
            <div id="test-results" class="text-sm text-gray-600">
                Cliquez sur "Test Dropdowns" pour commencer les tests...
            </div>
        </div>
    </div>

    <script>
        console.log('=== TEST PAGE LOADED ===');
        
        // API URLs
        const icd11ApiUrl = 'https://icd.who.int/icdapi/2/2019-04/content/search';
        const vidalApiUrl = 'https://api.vidal.fr/rest/api/search';
        const allergiesApiUrl = 'https://api.allergies.org/v1/search';
        
        // Search configurations for different medical categories
        const searchConfigs = {
            cardiovascular: {
                searchId: 'cardiovascular_search',
                resultsId: 'cardiovascular_results',
                selectedId: 'cardiovascular_selected',
                hiddenId: 'cardiovascular_history',
                placeholder: 'Rechercher des conditions cardio-vasculaires...',
                icd11Filters: ['cardiovascular', 'heart', 'hypertension', 'coronary', 'arrhythmia']
            },
            surgical: {
                searchId: 'surgical_search',
                resultsId: 'surgical_results',
                selectedId: 'surgical_selected',
                hiddenId: 'surgical_history',
                placeholder: 'Rechercher des procédures chirurgicales...',
                icd11Filters: ['surgical', 'procedure', 'operation', 'intervention']
            },
            medication: {
                searchId: 'medication_search',
                resultsId: 'medication_results',
                selectedId: 'medication_selected',
                hiddenId: 'medications',
                placeholder: 'Rechercher des médicaments...',
                icd11Filters: ['medication', 'drug', 'pharmaceutical', 'therapeutic']
            },
            allergy: {
                searchId: 'allergy_search',
                resultsId: 'allergy_results',
                selectedId: 'allergy_selected',
                hiddenId: 'allergies',
                placeholder: 'Rechercher des allergies...',
                icd11Filters: ['allergy', 'hypersensitivity', 'intolerance']
            }
        };

        // Search Function (ICD-11 for medical conditions, VIDAL for medications, Allergies API for allergies)
        window.searchICD11 = async function(query, filters, resultsDiv, selectedItems, selectedDiv, hiddenInput, config) {
            try {
                // Show loading state
                resultsDiv.innerHTML = '<div class="p-3 text-gray-500">Recherche en cours...</div>';
                resultsDiv.classList.remove('hidden');

                let results;
                
                // Use specific APIs for different categories
                if (config.hiddenId === 'medications') {
                    results = await window.searchVidalAPI(query);
                } else if (config.hiddenId === 'allergies') {
                    results = await window.searchAllergiesAPI(query);
                } else {
                    results = await window.simulateICD11Search(query, filters);
                }
                
                if (results.length === 0) {
                    resultsDiv.innerHTML = '<div class="p-3 text-gray-500">Aucun résultat trouvé</div>';
                    return;
                }

                // Display results
                resultsDiv.innerHTML = results.map(item => `
                    <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" 
                         onclick="selectItem('${item.id}', '${item.title.replace(/'/g, "\\'")}', '${config.hiddenId}', '${config.selectedId}')">
                        <div class="font-medium text-gray-900">${item.title}</div>
                        <div class="text-sm text-gray-600">
                            ${item.code || item.dosage || ''}
                            ${item.severity ? ` • ${item.severity}` : ''}
                            ${item.type ? ` • ${item.type}` : ''}
                        </div>
                    </div>
                `).join('');

            } catch (error) {
                console.error('Error searching:', error);
                resultsDiv.innerHTML = '<div class="p-3 text-red-500">Erreur lors de la recherche</div>';
            }
        }

        // VIDAL API Search Function
        window.searchVidalAPI = async function(query) {
            try {
                // Simulated VIDAL API response
                const vidalMedications = [
                    { id: 'vidal_001', title: 'Doliprane 500mg', dosage: 'Comprimé 500mg' },
                    { id: 'vidal_002', title: 'Aspirine 100mg', dosage: 'Comprimé 100mg' },
                    { id: 'vidal_003', title: 'Ibuprofène 400mg', dosage: 'Comprimé 400mg' },
                    { id: 'vidal_004', title: 'Paracétamol 1000mg', dosage: 'Comprimé 1000mg' },
                    { id: 'vidal_005', title: 'Atorvastatine 20mg', dosage: 'Comprimé 20mg' }
                ];

                // Filter by query
                const filtered = vidalMedications.filter(med => 
                    med.title.toLowerCase().includes(query.toLowerCase()) ||
                    med.dosage.toLowerCase().includes(query.toLowerCase())
                );

                return filtered;
            } catch (error) {
                console.error('Error searching VIDAL API:', error);
                return [];
            }
        }

        // Allergies API Search Function
        window.searchAllergiesAPI = async function(query) {
            try {
                // Simulated Allergies API response
                const allergiesData = [
                    { id: 'all_001', title: 'Allergie aux pénicillines', severity: 'Sévère', type: 'Médicamenteuse' },
                    { id: 'all_002', title: 'Allergie aux céphalosporines', severity: 'Modérée', type: 'Médicamenteuse' },
                    { id: 'all_003', title: 'Allergie aux sulfamides', severity: 'Sévère', type: 'Médicamenteuse' },
                    { id: 'all_011', title: 'Allergie aux arachides', severity: 'Sévère', type: 'Alimentaire' },
                    { id: 'all_012', title: 'Allergie aux noix', severity: 'Sévère', type: 'Alimentaire' }
                ];

                // Filter by query
                const filtered = allergiesData.filter(allergy => 
                    allergy.title.toLowerCase().includes(query.toLowerCase()) ||
                    allergy.type.toLowerCase().includes(query.toLowerCase()) ||
                    allergy.severity.toLowerCase().includes(query.toLowerCase())
                );

                return filtered;
            } catch (error) {
                console.error('Error searching Allergies API:', error);
                return [];
            }
        }

        // Simulate ICD-11 API with predefined medical terms
        window.simulateICD11Search = function(query, filters) {
            const medicalTerms = {
                cardiovascular: [
                    { id: 'cv_001', title: 'Hypertension artérielle', code: 'I10' },
                    { id: 'cv_002', title: 'Infarctus du myocarde', code: 'I21' },
                    { id: 'cv_003', title: 'Angine de poitrine', code: 'I20' },
                    { id: 'cv_004', title: 'Insuffisance cardiaque', code: 'I50' },
                    { id: 'cv_005', title: 'Arythmie cardiaque', code: 'I49' }
                ],
                surgical: [
                    { id: 'surg_001', title: 'Pontage aorto-coronarien', code: '0210' },
                    { id: 'surg_002', title: 'Remplacement valvulaire', code: '0211' },
                    { id: 'surg_003', title: 'Appendicectomie', code: '0212' },
                    { id: 'surg_004', title: 'Cholécystectomie', code: '0213' },
                    { id: 'surg_005', title: 'Herniorraphie', code: '0214' }
                ]
            };

            return new Promise((resolve) => {
                setTimeout(() => {
                    const category = filters[0]; // Use first filter to determine category
                    let terms = [];
                    
                    if (category.includes('cardiovascular') || category.includes('heart')) {
                        terms = medicalTerms.cardiovascular;
                    } else if (category.includes('surgical') || category.includes('procedure')) {
                        terms = medicalTerms.surgical;
                    }

                    // Filter by query
                    const filtered = terms.filter(term => 
                        term.title.toLowerCase().includes(query.toLowerCase()) ||
                        term.code.toLowerCase().includes(query.toLowerCase())
                    );

                    resolve(filtered);
                }, 200);
            });
        }

        // Global functions for item selection and removal
        window.selectItem = function(id, title, hiddenInputId, selectedDivId) {
            const hiddenInput = document.getElementById(hiddenInputId);
            const selectedDiv = document.getElementById(selectedDivId);
            
            let selectedItems = [];
            try {
                selectedItems = JSON.parse(hiddenInput.value || '[]');
            } catch (e) {
                selectedItems = [];
            }

            // Check if item already selected
            if (!selectedItems.find(item => item.id === id)) {
                selectedItems.push({ id, title });
                hiddenInput.value = JSON.stringify(selectedItems);
                window.updateSelectedDisplay(selectedItems, selectedDiv, searchConfigs[Object.keys(searchConfigs).find(key => 
                    searchConfigs[key].hiddenId === hiddenInputId
                )]);
            }

            // Hide results
            document.getElementById(searchConfigs[Object.keys(searchConfigs).find(key => 
                searchConfigs[key].hiddenId === hiddenInputId
            )].resultsId).classList.add('hidden');
            
            // Clear search input
            document.getElementById(searchConfigs[Object.keys(searchConfigs).find(key => 
                searchConfigs[key].hiddenId === hiddenInputId
            )].searchId).value = '';
        };

        window.updateSelectedDisplay = function(selectedItems, selectedDiv, config) {
            selectedDiv.innerHTML = selectedItems.map(item => `
                <div class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded px-3 py-2">
                    <span class="text-sm text-blue-900">${item.title}</span>
                    <button type="button" 
                            onclick="removeItem('${item.id}', '${config.hiddenId}', '${config.selectedId}')"
                            class="text-red-500 hover:text-red-700 ml-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            `).join('');
        };

        window.removeItem = function(id, hiddenInputId, selectedDivId) {
            const hiddenInput = document.getElementById(hiddenInputId);
            const selectedDiv = document.getElementById(selectedDivId);
            
            let selectedItems = [];
            try {
                selectedItems = JSON.parse(hiddenInput.value || '[]');
            } catch (e) {
                selectedItems = [];
            }

            selectedItems = selectedItems.filter(item => item.id !== id);
            hiddenInput.value = JSON.stringify(selectedItems);
            
            const config = searchConfigs[Object.keys(searchConfigs).find(key => 
                searchConfigs[key].hiddenId === hiddenInputId
            )];
            window.updateSelectedDisplay(selectedItems, selectedDiv, config);
        };

        // Test function to verify dropdowns are working
        window.testDropdowns = function() {
            console.log('=== TEST DROPDOWNS FUNCTION CALLED ===');
            console.log('Testing dropdowns...');
            
            const resultsDiv = document.getElementById('test-results');
            let results = [];
            
            if (typeof searchConfigs === 'undefined') {
                results.push('❌ searchConfigs is not defined!');
                resultsDiv.innerHTML = results.join('<br>');
                return;
            }
            
            results.push('✅ searchConfigs is defined');
            console.log('searchConfigs:', searchConfigs);
            
            Object.keys(searchConfigs).forEach(category => {
                const config = searchConfigs[category];
                const searchInput = document.getElementById(config.searchId);
                const resultsDiv = document.getElementById(config.resultsId);
                const selectedDiv = document.getElementById(config.selectedId);
                const hiddenInput = document.getElementById(config.hiddenId);
                
                const elementStatus = {
                    searchInput: !!searchInput,
                    resultsDiv: !!resultsDiv,
                    selectedDiv: !!selectedDiv,
                    hiddenInput: !!hiddenInput
                };
                
                results.push(`${category}: ${Object.values(elementStatus).every(Boolean) ? '✅' : '❌'} ${Object.keys(elementStatus).filter(key => elementStatus[key]).join(', ')}`);
                
                // Test search functionality
                if (searchInput && resultsDiv) {
                    if (typeof window.searchICD11 === 'function') {
                        window.searchICD11('test', config.icd11Filters, resultsDiv, [], selectedDiv, hiddenInput, config);
                        results.push(`✅ Search function called for ${category}`);
                    } else {
                        results.push(`❌ searchICD11 function is not defined for ${category}`);
                    }
                }
            });
            
            results.push('=== TEST DROPDOWNS COMPLETED ===');
            resultsDiv.innerHTML = results.join('<br>');
            console.log('=== TEST DROPDOWNS COMPLETED ===');
        };

        // Initialize search functionality when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing search functionality...');
            
            // Initialize search functionality for each category
            Object.keys(searchConfigs).forEach(category => {
                const config = searchConfigs[category];
                const searchInput = document.getElementById(config.searchId);
                const resultsDiv = document.getElementById(config.resultsId);
                const selectedDiv = document.getElementById(config.selectedId);
                const hiddenInput = document.getElementById(config.hiddenId);
                
                // Check if elements exist
                if (!searchInput || !resultsDiv || !selectedDiv || !hiddenInput) {
                    console.error(`Missing elements for ${category}`);
                    return;
                }
                
                let selectedItems = [];
                let searchTimeout;

                // Load initial data if exists
                if (hiddenInput.value) {
                    try {
                        selectedItems = JSON.parse(hiddenInput.value);
                        window.updateSelectedDisplay(selectedItems, selectedDiv, config);
                    } catch (e) {
                        console.log('No initial data for', category);
                    }
                }

                // Search functionality
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    if (query.length < 2) {
                        resultsDiv.classList.add('hidden');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        window.searchICD11(query, config.icd11Filters, resultsDiv, selectedItems, selectedDiv, hiddenInput, config);
                    }, 300);
                });

                // Hide results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                        resultsDiv.classList.add('hidden');
                    }
                });
            });
            
            console.log('✅ Search functionality initialized');
        });
    </script>
</body>
</html> 