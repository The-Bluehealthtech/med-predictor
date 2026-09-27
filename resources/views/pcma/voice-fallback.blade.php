<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('pcma_extra.label_b1a8552a4d75') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div id="app" class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                {{ __('pcma_extra.label_1250717738e4') }}
            </h1>
            <p class="text-gray-600 text-lg">
                {{ __('pcma_extra.label_efbceb0e98f4') }}
            </p>
            <div class="mt-4 p-3 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded">
                <p class="text-sm">
                    <strong>{{ __('pcma.vocal_console_note_label') }}</strong> {{ __('pcma_extra.label_1f7c5e3b763d') }}
                </p>
            </div>
        </div>

        <!-- Formulaire PCMA -->
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Identité du joueur -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-blue-100 text-blue-800 p-2 rounded-full mr-3">1</span>
                        {{ __('pcma_extra.label_37405ba73062') }}
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="player_name" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma_extra.label_6736098cc29a') }}
                            </label>
                            <input 
                                type="text" 
                                id="player_name" 
                                v-model="formData.player_name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Ex: Mohamed Ben Ali"
                                required
                            >
                        </div>
                        
                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma_extra.label_12dbdddda3a2') }}
                            </label>
                            <input 
                                type="number" 
                                id="age" 
                                v-model="formData.age"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Ex: 24"
                                min="16" 
                                max="50"
                                required
                            >
                        </div>
                    </div>
                </div>

                <!-- Position -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-green-100 text-green-800 p-2 rounded-full mr-3">2</span>
                        {{ __('pcma_extra.label_656d424bbb67') }}
                    </h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" v-model="formData.position" value="attaquant" class="mr-2">
                            <span class="text-sm font-medium">{{ __('pcma.position_forward_option') }}</span>
                        </label>
                        
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" v-model="formData.position" value="défenseur" class="mr-2">
                            <span class="text-sm font-medium">{{ __('pcma.position_defender_option') }}</span>
                        </label>
                        
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" v-model="formData.position" value="milieu" class="mr-2">
                            <span class="text-sm font-medium">{{ __('pcma.position_midfielder_option') }}</span>
                        </label>
                        
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" v-model="formData.position" value="gardien" class="mr-2">
                            <span class="text-sm font-medium">{{ __('pcma.position_goalkeeper_option') }}</span>
                        </label>
                    </div>
                </div>

                <!-- Antécédents médicaux -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-yellow-100 text-yellow-800 p-2 rounded-full mr-3">3</span>
                        {{ __('pcma_extra.label_4d47b91647c8') }}
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma_extra.label_ac39d851e3a4') }}
                            </label>
                            <textarea 
                                v-model="formData.medical_history"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                rows="3"
                                placeholder="{{ __('pcma_extra.label_67559a7edd6d') }}"
                            ></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('pcma_extra.label_4a0d961d4c49') }}
                                </label>
                                <input 
                                    type="text" 
                                    v-model="formData.allergies"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="{{ __('pcma_extra.label_f647fd2f9bc5') }}"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('pcma_extra.label_357fc17a6e22') }}
                                </label>
                                <input 
                                    type="text" 
                                    v-model="formData.current_medication"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="{{ __('Ex: Aucun, Vitamines...') }}"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- COVID-19 -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-red-100 text-red-800 p-2 rounded-full mr-3">4</span>
                        COVID-19
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma_extra.label_bdc64d6935ad') }}
                            </label>
                            <select 
                                v-model="formData.covid_test"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">{{ __('health_records_edit.select_2') }}</option>
                                <option value="negative">{{ __('health_records_edit.negative') }}</option>
                                <option value="positive">{{ __('health_records_edit.positive') }}</option>
                                <option value="not_tested">{{ __('pcma_extra.label_2611a6b69742') }}</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('pcma_extra.label_bd4aa845022b') }}
                            </label>
                            <select 
                                v-model="formData.covid_vaccination"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">{{ __('health_records_edit.select_2') }}</option>
                                <option value="not_vaccinated">{{ __('pcma_extra.label_73077969f6f3') }}</option>
                                <option value="partially_vaccinated">{{ __('pcma_extra.label_bf3fa9ed09f2') }}</option>
                                <option value="fully_vaccinated">{{ __('pcma_extra.label_f20355e82b8e') }}</option>
                                <option value="booster">{{ __('pcma_extra.label_0d2f44971c8f') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Examen physique -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-purple-100 text-purple-800 p-2 rounded-full mr-3">5</span>
                        {{ __('pcma_extra.label_18c07c15a5dc') }}
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.height_cm') }}
                            </label>
                            <input 
                                type="number" 
                                id="height" 
                                v-model="formData.height"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Ex: 175"
                                min="150" 
                                max="220"
                            >
                        </div>
                        
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('health_records_edit.weight_kg') }}
                            </label>
                            <input 
                                type="number" 
                                id="weight" 
                                v-model="formData.weight"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Ex: 70"
                                min="45" 
                                max="150"
                            >
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('pcma_extra.label_c76d855bde58') }}
                        </label>
                        <textarea 
                            v-model="formData.physical_observations"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            rows="3"
                            placeholder="{{ __('pcma_extra.label_58476fc382df') }}"
                        ></textarea>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button 
                        type="submit" 
                        :disabled="isSubmitting"
                        class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span v-if="isSubmitting" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('pcma_extra.label_a6e2189fb190') }}
                        </span>
                        <span v-else>{{ __('pcma_extra.label_ca0311254bdd') }}</span>
                    </button>
                    
                    <button 
                        type="button" 
                        @click="resetForm"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    >
                        {{ __('pcma_extra.label_685ed6e85342') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Résultat de soumission - Temporairement désactivé -->

        <!-- Retour à la voix -->
        <div class="text-center mt-8">
            <button 
                @click="returnToVoice"
                class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
            >
                {{ __('pcma_extra.label_53923f4987d3') }}
            </button>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    formData: {
                        player_name: '',
                        age: '',
                        position: '',
                        medical_history: '',
                        allergies: '',
                        current_medication: '',
                        covid_test: '',
                        covid_vaccination: '',
                        height: '',
                        weight: '',
                        physical_observations: ''
                    },
                    isSubmitting: false,
                    submissionResult: null
                };
            },
            
            mounted() {
                // Charger les données de session si disponibles
                this.loadSessionData();
            },
            
            methods: {
                async loadSessionData() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const sessionId = urlParams.get('session');
                    
                    if (sessionId) {
                        try {
                            const response = await axios.get(`/api/google-assistant/session/${sessionId}`);
                            if (response.data.success && response.data.session) {
                                const session = response.data.session;
                                const sessionData = session.session_data || {};
                                
                                // Récupérer le nom du joueur depuis session.player_name en priorité
                                this.formData.player_name = session.player_name || sessionData.player_name || '';
                                this.formData.age = sessionData.age1 || sessionData.age || '';
                                
                                // Normaliser la position pour correspondre aux valeurs des boutons radio
                                const position = sessionData.position || '';
                                this.formData.position = this.normalizePosition(position);
                                
                                console.log('Données de session chargées:', {
                                    player_name: this.formData.player_name,
                                    age: this.formData.age,
                                    position: this.formData.position,
                                    original_position: position
                                });
                            }
                        } catch (error) {
                            console.log('Session non trouvée ou erreur de chargement:', error);
                        }
                    }
                },
                
                // Normaliser la position pour correspondre aux valeurs des boutons radio
                normalizePosition(position) {
                    if (!position) return '';
                    
                    const normalized = position.toLowerCase().trim();
                    
                    if (normalized.includes('défenseur') || normalized.includes('defenseur')) {
                        return 'défenseur';
                    } else if (normalized.includes('attaquant')) {
                        return 'attaquant';
                    } else if (normalized.includes('milieu')) {
                        return 'milieu';
                    } else if (normalized.includes('gardien')) {
                        return 'gardien';
                    }
                    
                    return '';
                },
                
                async submitForm() {
                    this.isSubmitting = true;
                    this.submissionResult = null;
                    
                    try {
                        const response = await axios.post('/api/google-assistant/submit-pcma', this.formData);
                        
                        if (response.data.success) {
                            this.submissionResult = {
                                success: true,
                                message: 'Votre formulaire PCMA a été envoyé avec succès au système FIT.',
                                reference: response.data.reference || 'N/A'
                            };
                            
                            // Réinitialiser le formulaire après succès
                            setTimeout(() => {
                                this.resetForm();
                            }, 5000);
                        } else {
                            this.submissionResult = {
                                success: false,
                                message: response.data.message || @json(__('Erreur lors de la soumission du formulaire.'))
                            };
                        }
                    } catch (error) {
                        console.error(@json(__('Erreur de soumission:')), error);
                        this.submissionResult = {
                            success: false,
                            message: @json(__('Erreur de connexion au serveur. Veuillez réessayer.'))
                        };
                    } finally {
                        this.isSubmitting = false;
                    }
                },
                
                resetForm() {
                    this.formData = {
                        player_name: '',
                        age: '',
                        position: '',
                        medical_history: '',
                        allergies: '',
                        current_medication: '',
                        covid_test: '',
                        covid_vaccination: '',
                        height: '',
                        weight: '',
                        physical_observations: ''
                    };
                    this.submissionResult = null;
                },
                
                returnToVoice() {
                    // Rediriger vers l'assistant vocal ou afficher un message
                    alert(@json(__('Dites "Hey Google, parler à PCMA-FIT" pour reprendre par la voix, ou "recommencer" pour reprendre le formulaire.')));
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
