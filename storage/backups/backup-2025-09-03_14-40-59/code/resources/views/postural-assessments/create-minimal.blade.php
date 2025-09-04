<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Évaluation Posturale - Med Predictor</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div id="app" class="container mx-auto p-8">
        <h1 class="text-3xl font-bold mb-8">📊 Nouvelle Évaluation Posturale</h1>
        
        <!-- Debug Info -->
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            <strong>Debug:</strong> 
            <span id="vue-status">Vérification de Vue.js...</span>
            <br>
            <span id="component-status">Vérification du composant...</span>
        </div>

        <!-- Form -->
        <form action="{{ route('postural-assessments.store') }}" method="POST" id="posturalAssessmentForm">
            @csrf
            
            <!-- Player Selection -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">👤 Sélection du Joueur</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Joueur *
                        </label>
                        <select name="player_id" id="player_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner un joueur</option>
                            @foreach($players as $player)
                                <option value="{{ $player->id }}">
                                    {{ $player->name }} ({{ $player->position }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="assessment_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type d'Évaluation *
                        </label>
                        <select name="assessment_type" id="assessment_type" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner le type</option>
                            <option value="routine">Routine</option>
                            <option value="injury">Blessure</option>
                            <option value="follow_up">Suivi</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="assessment_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date d'Évaluation *
                    </label>
                    <input type="datetime-local" name="assessment_date" id="assessment_date" 
                           value="{{ now()->format('Y-m-d\TH:i') }}" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Interactive Chart -->
            <div class="bg-white rounded-lg shadow-md mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">🎯 Analyse Posturale Interactive</h2>
                    <p class="text-gray-600 mt-2">Utilisez les outils ci-dessous pour analyser la posture du joueur</p>
                </div>
                
                <!-- Vue Component will be mounted here -->
                <div id="postural-chart-app">
                    <interactive-postural-chart 
                        :session-data="sessionData"
                        @session-saved="handleSessionSaved"
                        @view-changed="handleViewChanged">
                    </interactive-postural-chart>
                </div>
            </div>

            <!-- Clinical Notes -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">📝 Notes Cliniques</h2>
                <div class="space-y-4">
                    <div>
                        <label for="clinical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Observations Cliniques
                        </label>
                        <textarea name="clinical_notes" id="clinical_notes" rows="4" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Décrivez vos observations cliniques..."></textarea>
                    </div>
                    
                    <div>
                        <label for="recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                            Recommandations
                        </label>
                        <textarea name="recommendations" id="recommendations" rows="4" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Proposez des recommandations..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for Vue data -->
            <input type="hidden" name="view" id="view" value="anterior">
            <input type="hidden" name="annotations" id="annotations" value="[]">
            <input type="hidden" name="markers" id="markers" value="[]">
            <input type="hidden" name="angles" id="angles" value="[]">

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="history.back()" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                    Annuler
                </button>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    💾 Sauvegarder l'Évaluation
                </button>
            </div>
        </form>
    </div>

    <script>
        const { createApp } = Vue;
        
        // Simple component for testing
        const InteractivePosturalChart = {
            template: `
                <div class="postural-chart-container">
                    <div class="bg-blue-100 p-4 rounded">
                        <h3 class="font-bold">Composant Postural Chart</h3>
                        <p>Vue actuelle: {{ sessionData.view }}</p>
                        <p>Annotations: {{ sessionData.annotations.length }}</p>
                        <button @click="changeView" class="bg-blue-500 text-white px-3 py-1 rounded mt-2">
                            Changer de vue
                        </button>
                    </div>
                </div>
            `,
            props: {
                sessionData: {
                    type: Object,
                    default: () => ({
                        view: 'anterior',
                        annotations: [],
                        markers: [],
                        angles: []
                    })
                }
            },
            methods: {
                changeView() {
                    const views = ['anterior', 'posterior', 'lateral'];
                    const currentIndex = views.indexOf(this.sessionData.view);
                    const nextIndex = (currentIndex + 1) % views.length;
                    this.sessionData.view = views[nextIndex];
                    this.$emit('view-changed', this.sessionData.view);
                }
            }
        };
        
        createApp({
            components: {
                InteractivePosturalChart
            },
            data() {
                return {
                    sessionData: {
                        view: 'anterior',
                        annotations: [],
                        markers: [],
                        angles: []
                    }
                }
            },
            methods: {
                handleSessionSaved(data) {
                    console.log('Session saved:', data);
                    // Update hidden form fields
                    document.getElementById('view').value = data.view;
                    document.getElementById('annotations').value = JSON.stringify(data.annotations);
                    document.getElementById('markers').value = JSON.stringify(data.markers);
                    document.getElementById('angles').value = JSON.stringify(data.angles);
                    
                    // Show success message
                    alert('Session sauvegardée avec succès');
                },
                handleViewChanged(view) {
                    console.log('View changed:', view);
                    this.sessionData.view = view;
                    document.getElementById('view').value = view;
                }
            },
            mounted() {
                // Debug Vue.js status
                const vueStatus = document.getElementById('vue-status');
                const componentStatus = document.getElementById('component-status');
                
                if (typeof Vue !== 'undefined') {
                    vueStatus.textContent = '✅ Vue.js est chargé';
                    vueStatus.className = 'text-green-600';
                } else {
                    vueStatus.textContent = '❌ Vue.js n\'est pas chargé';
                    vueStatus.className = 'text-red-600';
                }
                
                // Check if component is registered
                setTimeout(() => {
                    const appElement = document.getElementById('postural-chart-app');
                    if (appElement && appElement.querySelector('interactive-postural-chart')) {
                        componentStatus.textContent = '✅ Composant chargé';
                        componentStatus.className = 'text-green-600';
                    } else {
                        componentStatus.textContent = '❌ Composant non trouvé';
                        componentStatus.className = 'text-red-600';
                    }
                }, 1000);
            }
        }).mount('#app');
    </script>
</body>
</html> 