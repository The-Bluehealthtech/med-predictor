<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau PCMA - Med Predictor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">📋 Nouveau PCMA</h1>
                <p class="text-gray-600 mt-2">Créer une nouvelle évaluation médicale pré-compétition</p>
            </div>

            <form action="{{ route('pcma.store') }}" method="POST" class="space-y-8">
                @csrf
                
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Informations PCMA</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="athlete_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Athlète *
                            </label>
                            <select name="athlete_id" id="athlete_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Sélectionner un athlète</option>
                                @foreach($athletes as $athlete)
                                    <option value="{{ $athlete['id'] }}">{{ $athlete['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="fifa_connect_id" class="block text-sm font-medium text-gray-700 mb-2">
                                ID FIFA Connect
                            </label>
                            <input type="text" name="fifa_connect_id" id="fifa_connect_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Entrez l'ID FIFA Connect du joueur">
                        </div>
                        
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Type d'évaluation *
                            </label>
                            <select name="type" id="type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Sélectionner le type</option>
                                <option value="bpma">BPMA</option>
                                <option value="cardio">Cardio</option>
                                <option value="dental">Dental</option>
                                <option value="neurological">Neurologique</option>
                                <option value="orthopedic">Orthopédique</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="assessor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Assesseur *
                            </label>
                            <select name="assessor_id" id="assessor_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Sélectionner un assesseur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="assessment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date d'évaluation *
                            </label>
                            <input type="date" name="assessment_date" id="assessment_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Statut *
                            </label>
                            <select name="status" id="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="pending">En attente</option>
                                <option value="completed">Terminé</option>
                                <option value="failed">Échoué</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Medical History with Searchable Dropdowns -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">🏥 Antécédents Médicaux</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">
                                Antécédents Cardio-vasculaires
                            </label>
                            <div class="relative">
                                <input type="text" id="medical_history_search" placeholder="Rechercher des antécédents cardiaques, hypertension, etc..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <div id="medical_history_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                            </div>
                            <input type="hidden" name="medical_history" id="medical_history">
                        </div>
                        
                        <div>
                            <label for="surgical_history" class="block text-sm font-medium text-gray-700 mb-2">
                                Antécédents Chirurgicaux
                            </label>
                            <div class="relative">
                                <input type="text" id="surgical_history_search" placeholder="Rechercher des interventions chirurgicales..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <div id="surgical_history_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                            </div>
                            <input type="hidden" name="surgical_history" id="surgical_history">
                        </div>
                        
                        <div>
                            <label for="medications" class="block text-sm font-medium text-gray-700 mb-2">
                                Médicaments Actuels
                            </label>
                            <div class="relative">
                                <input type="text" id="medications_search" placeholder="Rechercher des traitements en cours..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <div id="medications_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                            </div>
                            <input type="hidden" name="medications" id="medications">
                        </div>
                        
                        <div>
                            <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                                Allergies
                            </label>
                            <div class="relative">
                                <input type="text" id="allergies_search" placeholder="Rechercher des allergies médicamenteuses, alimentaires..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <div id="allergies_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                            </div>
                            <input type="hidden" name="allergies" id="allergies">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        💾 Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Searchable dropdown functionality
        const searchConfigs = {
            medical_history: {
                searchInput: 'medical_history_search',
                resultsDiv: 'medical_history_results',
                hiddenInput: 'medical_history',
                apiEndpoint: '/api/proxy/icd11'
            },
            surgical_history: {
                searchInput: 'surgical_history_search',
                resultsDiv: 'surgical_history_results',
                hiddenInput: 'surgical_history',
                apiEndpoint: '/api/proxy/icd11'
            },
            medications: {
                searchInput: 'medications_search',
                resultsDiv: 'medications_results',
                hiddenInput: 'medications',
                apiEndpoint: '/api/proxy/vidal'
            },
            allergies: {
                searchInput: 'allergies_search',
                resultsDiv: 'allergies_results',
                hiddenInput: 'allergies',
                apiEndpoint: '/api/proxy/allergies'
            }
        };

        // Initialize search functionality
        Object.keys(searchConfigs).forEach(field => {
            const config = searchConfigs[field];
            const searchInput = document.getElementById(config.searchInput);
            const resultsDiv = document.getElementById(config.resultsDiv);
            const hiddenInput = document.getElementById(config.hiddenInput);

            if (searchInput && resultsDiv && hiddenInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    if (query.length >= 2) {
                        performSearch(query, config);
                    } else {
                        resultsDiv.classList.add('hidden');
                    }
                });

                // Hide results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                        resultsDiv.classList.add('hidden');
                    }
                });
            }
        });

        async function performSearch(query, config) {
            try {
                const response = await fetch(`${config.apiEndpoint}?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                const resultsDiv = document.getElementById(config.resultsDiv);
                resultsDiv.innerHTML = '';
                
                if (data.results && data.results.length > 0) {
                    data.results.forEach(result => {
                        const item = document.createElement('div');
                        item.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                        item.textContent = result.title || result.name || result;
                        item.onclick = () => selectItem(result, config);
                        resultsDiv.appendChild(item);
                    });
                    resultsDiv.classList.remove('hidden');
                } else {
                    resultsDiv.classList.add('hidden');
                }
            } catch (error) {
                console.error('Search error:', error);
            }
        }

        function selectItem(item, config) {
            const searchInput = document.getElementById(config.searchInput);
            const hiddenInput = document.getElementById(config.hiddenInput);
            const resultsDiv = document.getElementById(config.resultsDiv);
            
            searchInput.value = item.title || item.name || item;
            hiddenInput.value = JSON.stringify(item);
            resultsDiv.classList.add('hidden');
        }
    </script>
</body>
</html> 