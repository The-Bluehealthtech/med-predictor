<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rapport d'Arbitre - {{ optional(optional($match->homeTeam)->club)->short_name ?? optional(optional($match->homeTeam)->club)->name ?? 'TBD' }} vs {{ optional(optional($match->awayTeam)->club)->short_name ?? optional(optional($match->awayTeam)->club)->name ?? 'TBD' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100">
    <div id="app" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg mb-6">
                <div class="px-6 py-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold">Rapport d'Arbitre</h1>
                            <p class="text-orange-100 mt-2">
                                {{ optional(optional($match->homeTeam)->club)->short_name ?? optional(optional($match->homeTeam)->club)->name ?? 'TBD' }} vs {{ optional(optional($match->awayTeam)->club)->short_name ?? optional(optional($match->awayTeam)->club)->name ?? 'TBD' }}
                            </p>
                            <p class="text-orange-100 text-sm">
                                {{ $match->competition->name ?? 'Competition' }} • {{ $match->match_date ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <a href="/referee-create-report-working" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white hover:bg-opacity-30 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Retour aux Rapports
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button 
                            v-for="tab in tabs" 
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                activeTab === tab.id
                                    ? 'border-orange-500 text-orange-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                            ]"
                        >
                            @{{ tab.name }}
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitReport" class="space-y-6">
                @csrf
                <input type="hidden" name="match_id" value="{{ $match->id }}">

                <!-- Tab 1: Informations Match -->
                <div v-show="activeTab === 'info'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations Match</h2>
                        
                        <!-- Match Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Équipe Domicile</label>
                                <p class="mt-1 text-sm text-gray-900">{{ optional(optional($match->homeTeam)->club)->short_name ?? optional(optional($match->homeTeam)->club)->name ?? optional($match->homeTeam)->name ?? 'TBD' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Équipe Extérieur</label>
                                <p class="mt-1 text-sm text-gray-900">{{ optional(optional($match->awayTeam)->club)->short_name ?? optional(optional($match->awayTeam)->club)->name ?? optional($match->awayTeam)->name ?? 'TBD' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Compétition</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $match->competition->name ?? 'TBD' }}</p>
                            </div>
                        </div>

                        <!-- Match Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="final_score" class="block text-sm font-medium text-gray-700">Score Final *</label>
                                <input type="text" v-model="formData.final_score" name="final_score" id="final_score" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="e.g., 2-1" required>
                            </div>
                            <div>
                                <label for="half_time_score" class="block text-sm font-medium text-gray-700">Score Mi-temps</label>
                                <input type="text" v-model="formData.half_time_score" name="half_time_score" id="half_time_score" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="e.g., 1-0">
                            </div>
                            <div>
                                <label for="weather" class="block text-sm font-medium text-gray-700">Météo</label>
                                <select v-model="formData.weather" name="weather" id="weather" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Sélectionner...</option>
                                    <option value="Ensoleillé">Ensoleillé</option>
                                    <option value="Nuageux">Nuageux</option>
                                    <option value="Pluvieux">Pluvieux</option>
                                    <option value="Venteux">Venteux</option>
                                    <option value="Neigeux">Neigeux</option>
                                </select>
                            </div>
                            <div>
                                <label for="pitch_condition" class="block text-sm font-medium text-gray-700">État du Terrain</label>
                                <select v-model="formData.pitch_condition" name="pitch_condition" id="pitch_condition" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Sélectionner...</option>
                                    <option value="Excellent">Excellent</option>
                                    <option value="Bon">Bon</option>
                                    <option value="Moyen">Moyen</option>
                                    <option value="Mauvais">Mauvais</option>
                                    <option value="Très mauvais">Très mauvais</option>
                                </select>
                            </div>
                        </div>

                        <!-- Extra Time & Penalties -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label for="extra_time_minutes" class="block text-sm font-medium text-gray-700">Prolongations (minutes)</label>
                                <input type="number" v-model="formData.extra_time_minutes" name="extra_time_minutes" id="extra_time_minutes" min="0" max="30"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" v-model="formData.penalty_shootout" name="penalty_shootout" id="penalty_shootout" 
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <label for="penalty_shootout" class="ml-2 block text-sm text-gray-900">Tirs au but</label>
                            </div>
                            <div v-if="formData.penalty_shootout">
                                <label for="penalty_shootout_score" class="block text-sm font-medium text-gray-700">Score Tirs au but</label>
                                <input type="text" v-model="formData.penalty_shootout_score" name="penalty_shootout_score" id="penalty_shootout_score" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="e.g., 4-3">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Officiels -->
                <div v-show="activeTab === 'officials'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Officiels</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="main_referee" class="block text-sm font-medium text-gray-700">Arbitre Principal *</label>
                                <input type="text" v-model="formData.main_referee" name="main_referee" id="main_referee" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Nom de l'arbitre principal" required>
                            </div>
                            <div>
                                <label for="assistant_referee_1" class="block text-sm font-medium text-gray-700">Assistant Arbitre 1 *</label>
                                <input type="text" v-model="formData.assistant_referee_1" name="assistant_referee_1" id="assistant_referee_1" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Nom de l'assistant 1" required>
                            </div>
                            <div>
                                <label for="assistant_referee_2" class="block text-sm font-medium text-gray-700">Assistant Arbitre 2 *</label>
                                <input type="text" v-model="formData.assistant_referee_2" name="assistant_referee_2" id="assistant_referee_2" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Nom de l'assistant 2" required>
                            </div>
                            <div>
                                <label for="fourth_official" class="block text-sm font-medium text-gray-700">4ème Arbitre *</label>
                                <input type="text" v-model="formData.fourth_official" name="fourth_official" id="fourth_official" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Nom du 4ème arbitre" required>
                            </div>
                            <div>
                                <label for="var_referee" class="block text-sm font-medium text-gray-700">Arbitre VAR</label>
                                <input type="text" v-model="formData.var_referee" name="var_referee" id="var_referee" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Nom de l'arbitre VAR">
                            </div>
                            <div>
                                <label for="avar_referee" class="block text-sm font-medium text-gray-700">Assistant VAR</label>
                                <input type="text" v-model="formData.avar_referee" name="avar_referee" id="avar_referee" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Nom de l'assistant VAR">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Équipes -->
                <div v-show="activeTab === 'teams'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Équipes</h2>
                        
                        <!-- Home Team -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ optional(optional($match->homeTeam)->club)->short_name ?? optional(optional($match->homeTeam)->club)->name ?? 'Équipe Domicile' }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($homeTeamPlayers as $player)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $player->name }}</p>
                                            <p class="text-sm text-gray-600">#{{ $player->jersey_number ?? 'N/A' }} - {{ $player->position ?? 'N/A' }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button type="button" @click="addGoal('home', '{{ $player->name }}')" 
                                                    class="px-2 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">
                                                But
                                            </button>
                                            <button type="button" @click="addCard('yellow', 'home', '{{ $player->name }}')" 
                                                    class="px-2 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                                Jaune
                                            </button>
                                            <button type="button" @click="addCard('red', 'home', '{{ $player->name }}')" 
                                                    class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">
                                                Rouge
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Away Team -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ optional(optional($match->awayTeam)->club)->short_name ?? optional(optional($match->awayTeam)->club)->name ?? 'Équipe Extérieur' }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($awayTeamPlayers as $player)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $player->name }}</p>
                                            <p class="text-sm text-gray-600">#{{ $player->jersey_number ?? 'N/A' }} - {{ $player->position ?? 'N/A' }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button type="button" @click="addGoal('away', '{{ $player->name }}')" 
                                                    class="px-2 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">
                                                But
                                            </button>
                                            <button type="button" @click="addCard('yellow', 'away', '{{ $player->name }}')" 
                                                    class="px-2 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                                Jaune
                                            </button>
                                            <button type="button" @click="addCard('red', 'away', '{{ $player->name }}')" 
                                                    class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">
                                                Rouge
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Événements -->
                <div v-show="activeTab === 'events'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Événements</h2>
                        
                        <!-- Timeline des événements -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline des Événements</h3>
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                <div v-for="event in timelineEvents" :key="event.id" 
                                     :class="getEventClass(event.type)"
                                     class="flex items-center p-3 rounded-lg">
                                    <div class="flex-shrink-0 w-12 text-center">
                                        <span class="text-sm font-medium">@{{ event.minute }}'</span>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium">@{{ event.player }}</p>
                                        <p class="text-xs text-gray-600">@{{ event.description }}</p>
                                    </div>
                                    <button @click="removeEvent(event.id)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Ajouter un événement -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Ajouter un Événement</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Minute</label>
                                    <input type="number" v-model="newEvent.minute" min="1" max="120" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Joueur</label>
                                    <input type="text" v-model="newEvent.player" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Type</label>
                                    <select v-model="newEvent.type" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                        <option value="goal">But</option>
                                        <option value="yellow_card">Carton Jaune</option>
                                        <option value="red_card">Carton Rouge</option>
                                        <option value="substitution">Remplacement</option>
                                        <option value="injury">Blessure</option>
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" @click="addEvent" 
                                            class="w-full bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700">
                                        Ajouter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Discipline & Santé -->
                <div v-show="activeTab === 'discipline'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Discipline & Santé</h2>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="disciplinary_incidents" class="block text-sm font-medium text-gray-700">Incidents Disciplinaires</label>
                                <textarea v-model="formData.disciplinary_incidents" name="disciplinary_incidents" id="disciplinary_incidents" rows="4" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Décrivez les incidents disciplinaires..."></textarea>
                            </div>
                            
                            <div>
                                <label for="crowd_incidents" class="block text-sm font-medium text-gray-700">Incidents avec le Public</label>
                                <textarea v-model="formData.crowd_incidents" name="crowd_incidents" id="crowd_incidents" rows="4" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Décrivez les incidents avec le public..."></textarea>
                            </div>
                            
                            <div>
                                <label for="safety_issues" class="block text-sm font-medium text-gray-700">Problèmes de Sécurité</label>
                                <textarea v-model="formData.safety_issues" name="safety_issues" id="safety_issues" rows="4" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Décrivez les problèmes de sécurité..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 6: Observations -->
                <div v-show="activeTab === 'observations'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Observations</h2>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="general_comments" class="block text-sm font-medium text-gray-700">Observations Générales *</label>
                                <textarea v-model="formData.general_comments" name="general_comments" id="general_comments" rows="6" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Décrivez le déroulement général du match, l'ambiance, le comportement des équipes..." required></textarea>
                            </div>
                            
                            <div>
                                <label for="match_quality_assessment" class="block text-sm font-medium text-gray-700">Évaluation de la Qualité du Match</label>
                                <textarea v-model="formData.match_quality_assessment" name="match_quality_assessment" id="match_quality_assessment" rows="4" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Évaluez la qualité technique du match, le niveau de jeu..."></textarea>
                            </div>
                            
                            <div>
                                <label for="match_rating" class="block text-sm font-medium text-gray-700">Note du Match (1-10)</label>
                                <select v-model="formData.match_rating" name="match_rating" id="match_rating" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Sélectionner...</option>
                                    <option v-for="i in 10" :key="i" :value="i">@{{ i }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation et Validation -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-4">
                                <button type="button" @click="previousTab" 
                                        :disabled="activeTab === 'info'"
                                        :class="[
                                            activeTab === 'info' 
                                                ? 'bg-gray-300 cursor-not-allowed' 
                                                : 'bg-gray-600 hover:bg-gray-700',
                                            'text-white px-4 py-2 rounded-md'
                                        ]">
                                    Précédent
                                </button>
                                <button type="button" @click="nextTab" 
                                        :disabled="activeTab === 'observations'"
                                        :class="[
                                            activeTab === 'observations' 
                                                ? 'bg-gray-300 cursor-not-allowed' 
                                                : 'bg-orange-600 hover:bg-orange-700',
                                            'text-white px-4 py-2 rounded-md'
                                        ]">
                                    Suivant
                                </button>
                            </div>
                            
                            <div class="flex space-x-4">
                                <button type="button" @click="saveDraft" 
                                        class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">
                                    Sauvegarder Brouillon
                                </button>
                                <button type="submit" 
                                        :disabled="!isFormValid"
                                        :class="[
                                            isFormValid 
                                                ? 'bg-green-600 hover:bg-green-700' 
                                                : 'bg-gray-300 cursor-not-allowed',
                                            'text-white px-6 py-2 rounded-md'
                                        ]">
                                    Soumettre Rapport
                                </button>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="bg-orange-600 h-2 rounded-full transition-all duration-300" 
                                     :style="{ width: progressPercentage + '%' }"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">Progression: @{{ progressPercentage }}%</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    activeTab: 'info',
                    tabs: [
                        { id: 'info', name: 'Infos Match' },
                        { id: 'officials', name: 'Officiels' },
                        { id: 'teams', name: 'Équipes' },
                        { id: 'events', name: 'Événements' },
                        { id: 'discipline', name: 'Discipline' },
                        { id: 'observations', name: 'Observations' }
                    ],
                    formData: {
                        final_score: '',
                        half_time_score: '',
                        extra_time_minutes: 0,
                        penalty_shootout: false,
                        penalty_shootout_score: '',
                        weather: '',
                        pitch_condition: '',
                        main_referee: '',
                        assistant_referee_1: '',
                        assistant_referee_2: '',
                        fourth_official: '',
                        var_referee: '',
                        avar_referee: '',
                        disciplinary_incidents: '',
                        crowd_incidents: '',
                        safety_issues: '',
                        general_comments: '',
                        match_quality_assessment: '',
                        match_rating: ''
                    },
                    timelineEvents: [],
                    newEvent: {
                        minute: '',
                        player: '',
                        type: 'goal'
                    },
                    eventCounter: 0
                }
            },
            computed: {
                progressPercentage() {
                    const completedTabs = this.tabs.findIndex(tab => tab.id === this.activeTab) + 1;
                    return Math.round((completedTabs / this.tabs.length) * 100);
                },
                isFormValid() {
                    return this.formData.final_score && 
                           this.formData.main_referee && 
                           this.formData.assistant_referee_1 && 
                           this.formData.assistant_referee_2 && 
                           this.formData.fourth_official && 
                           this.formData.general_comments.length >= 10;
                }
            },
            methods: {
                nextTab() {
                    const currentIndex = this.tabs.findIndex(tab => tab.id === this.activeTab);
                    if (currentIndex < this.tabs.length - 1) {
                        this.activeTab = this.tabs[currentIndex + 1].id;
                    }
                },
                previousTab() {
                    const currentIndex = this.tabs.findIndex(tab => tab.id === this.activeTab);
                    if (currentIndex > 0) {
                        this.activeTab = this.tabs[currentIndex - 1].id;
                    }
                },
                addGoal(team, player) {
                    const minute = prompt(`Minute du but pour ${player}:`);
                    if (minute) {
                        this.timelineEvents.push({
                            id: ++this.eventCounter,
                            minute: parseInt(minute),
                            player: player,
                            type: 'goal',
                            description: `But - ${team === 'home' ? '{{ optional(optional($match->homeTeam)->club)->short_name ?? optional(optional($match->homeTeam)->club)->name ?? "Home" }}' : '{{ optional(optional($match->awayTeam)->club)->short_name ?? optional(optional($match->awayTeam)->club)->name ?? "Away" }}'}`
                        });
                        this.sortTimeline();
                    }
                },
                addCard(type, team, player) {
                    const minute = prompt(`Minute du carton ${type === 'yellow' ? 'jaune' : 'rouge'} pour ${player}:`);
                    if (minute) {
                        this.timelineEvents.push({
                            id: ++this.eventCounter,
                            minute: parseInt(minute),
                            player: player,
                            type: type === 'yellow' ? 'yellow_card' : 'red_card',
                            description: `Carton ${type === 'yellow' ? 'jaune' : 'rouge'} - ${team === 'home' ? '{{ optional(optional($match->homeTeam)->club)->short_name ?? optional(optional($match->homeTeam)->club)->name ?? "Home" }}' : '{{ optional(optional($match->awayTeam)->club)->short_name ?? optional(optional($match->awayTeam)->club)->name ?? "Away" }}'}`
                        });
                        this.sortTimeline();
                    }
                },
                addEvent() {
                    if (this.newEvent.minute && this.newEvent.player) {
                        this.timelineEvents.push({
                            id: ++this.eventCounter,
                            minute: parseInt(this.newEvent.minute),
                            player: this.newEvent.player,
                            type: this.newEvent.type,
                            description: this.getEventDescription(this.newEvent.type)
                        });
                        this.sortTimeline();
                        this.newEvent = { minute: '', player: '', type: 'goal' };
                    }
                },
                removeEvent(id) {
                    this.timelineEvents = this.timelineEvents.filter(event => event.id !== id);
                },
                sortTimeline() {
                    this.timelineEvents.sort((a, b) => a.minute - b.minute);
                },
                getEventDescription(type) {
                    const descriptions = {
                        'goal': 'But',
                        'yellow_card': 'Carton Jaune',
                        'red_card': 'Carton Rouge',
                        'substitution': 'Remplacement',
                        'injury': 'Blessure'
                    };
                    return descriptions[type] || 'Événement';
                },
                getEventClass(type) {
                    const classes = {
                        'goal': 'bg-green-100 border-l-4 border-green-500',
                        'yellow_card': 'bg-yellow-100 border-l-4 border-yellow-500',
                        'red_card': 'bg-red-100 border-l-4 border-red-500',
                        'substitution': 'bg-blue-100 border-l-4 border-blue-500',
                        'injury': 'bg-orange-100 border-l-4 border-orange-500'
                    };
                    return classes[type] || 'bg-gray-100 border-l-4 border-gray-500';
                },
                saveDraft() {
                    // Sauvegarder en brouillon
                    console.log('Sauvegarde brouillon...');
                },
                async submitReport() {
                    if (!this.isFormValid) {
                        alert('Veuillez remplir tous les champs obligatoires');
                        return;
                    }

                    // Préparer les données pour l'envoi
                    const reportData = {
                        ...this.formData,
                        goals: this.timelineEvents.filter(e => e.type === 'goal'),
                        yellow_cards: this.timelineEvents.filter(e => e.type === 'yellow_card'),
                        red_cards: this.timelineEvents.filter(e => e.type === 'red_card'),
                        substitutions: this.timelineEvents.filter(e => e.type === 'substitution'),
                        injuries: this.timelineEvents.filter(e => e.type === 'injury')
                    };

                    // Envoyer le rapport
                    try {
                        const response = await axios.post('/referee-save-report', reportData);
                        window.location.href = `/referee-report-success/${response.data.report_id}`;
                    } catch (error) {
                        console.error('Erreur lors de la soumission:', error);
                        alert('Erreur lors de la soumission du rapport');
                    }
                }
            }
        }).mount('#app');
    </script>
</body>
</html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rapport d'Arbitre - {{ optional(optional($match->homeTeam)->club)->short_name ?? optional(optional($match->homeTeam)->club)->name ?? 'TBD' }} vs {{ optional(optional($match->awayTeam)->club)->short_name ?? optional(optional($match->awayTeam)->club)->name ?? 'TBD' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100">
    <div id="app" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg mb-6">
                <div class="px-6 py-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold">Rapport d'Arbitre</h1>
                            <p class="text-orange-100 mt-2">
                                {{ optional(optional($match->homeTeam)->club)->short_name ?? optional(optional($match->homeTeam)->club)->name ?? 'TBD' }} vs {{ optional(optional($match->awayTeam)->club)->short_name ?? optional(optional($match->awayTeam)->club)->name ?? 'TBD' }}
                            </p>
                            <p class="text-orange-100 text-sm">
                                {{ $match->competition->name ?? 'Competition' }} • {{ $match->match_date ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <a href="/referee-create-report-working" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white hover:bg-opacity-30 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Retour aux Rapports
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button 
                            v-for="tab in tabs" 
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                activeTab === tab.id
                                    ? 'border-orange-500 text-orange-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                            ]"
                        >
                            @{{ tab.name }}
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitReport" class="space-y-6">
                @csrf
                <input type="hidden" name="match_id" value="{{ $match->id }}">

                <!-- Tab 1: Informations Match -->
                <div v-show="activeTab === 'info'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations Match</h2>
                        
                        <!-- Match Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Équipe Domicile</label>
                                <p class="mt-1 text-sm text-gray-900">{{ optional(optional($match->homeTeam)->club)->name ?? optional($match->homeTeam)->name ?? 'TBD' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Équipe Extérieur</label>
                                <p class="mt-1 text-sm text-gray-900">{{ optional(optional($match->awayTeam)->club)->name ?? optional($match->awayTeam)->name ?? 'TBD' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Compétition</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $match->competition->name ?? 'TBD' }}</p>
                            </div>
                        </div>

                        <!-- Match Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="final_score" class="block text-sm font-medium text-gray-700">Score Final *</label>
                                <input type="text" v-model="formData.final_score" name="final_score" id="final_score" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="e.g., 2-1" required>
                            </div>
                            <div>
                                <label for="half_time_score" class="block text-sm font-medium text-gray-700">Score Mi-temps</label>
                                <input type="text" v-model="formData.half_time_score" name="half_time_score" id="half_time_score" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="e.g., 1-0">
                            </div>
                            <div>
                                <label for="weather" class="block text-sm font-medium text-gray-700">Météo</label>
                                <select v-model="formData.weather" name="weather" id="weather" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Sélectionner...</option>
                                    <option value="Ensoleillé">Ensoleillé</option>
                                    <option value="Nuageux">Nuageux</option>
                                    <option value="Pluvieux">Pluvieux</option>
                                    <option value="Venteux">Venteux</option>
                                    <option value="Neigeux">Neigeux</option>
                                </select>
                            </div>
                            <div>
                                <label for="pitch_condition" class="block text-sm font-medium text-gray-700">État du Terrain</label>
                                <select v-model="formData.pitch_condition" name="pitch_condition" id="pitch_condition" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Sélectionner...</option>
                                    <option value="Excellent">Excellent</option>
                                    <option value="Bon">Bon</option>
                                    <option value="Moyen">Moyen</option>
                                    <option value="Mauvais">Mauvais</option>
                                    <option value="Très mauvais">Très mauvais</option>
                                </select>
                            </div>
                        </div>

                        <!-- Extra Time & Penalties -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label for="extra_time_minutes" class="block text-sm font-medium text-gray-700">Prolongations (minutes)</label>
                                <input type="number" v-model="formData.extra_time_minutes" name="extra_time_minutes" id="extra_time_minutes" min="0" max="30"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" v-model="formData.penalty_shootout" name="penalty_shootout" id="penalty_shootout" 
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <label for="penalty_shootout" class="ml-2 block text-sm text-gray-900">Tirs au but</label>
                            </div>
                            <div v-if="formData.penalty_shootout">
                                <label for="penalty_shootout_score" class="block text-sm font-medium text-gray-700">Score Tirs au but</label>
                                <input type="text" v-model="formData.penalty_shootout_score" name="penalty_shootout_score" id="penalty_shootout_score" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="e.g., 4-3">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Officiels -->
                <div v-show="activeTab === 'officials'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Officiels</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="main_referee" class="block text-sm font-medium text-gray-700">Arbitre Principal *</label>
                                <input type="text" v-model="formData.main_referee" name="main_referee" id="main_referee" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Nom de l'arbitre principal" required>
                            </div>
                            <div>
                                <label for="assistant_referee_1" class="block text-sm font-medium text-gray-700">Assistant Arbitre 1 *</label>
                                <input type="text" v-model="formData.assistant_referee_1" name="assistant_referee_1" id="assistant_referee_1" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Nom de l'assistant 1" required>
                            </div>
                            <div>
                                <label for="assistant_referee_2" class="block text-sm font-medium text-gray-700">Assistant Arbitre 2 *</label>
                                <input type="text" v-model="formData.assistant_referee_2" name="assistant_referee_2" id="assistant_referee_2" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Nom de l'assistant 2" required>
                            </div>
                            <div>
                                <label for="fourth_official" class="block text-sm font-medium text-gray-700">4ème Arbitre *</label>
                                <input type="text" v-model="formData.fourth_official" name="fourth_official" id="fourth_official" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Nom du 4ème arbitre" required>
                            </div>
                            <div>
                                <label for="var_referee" class="block text-sm font-medium text-gray-700">Arbitre VAR</label>
                                <input type="text" v-model="formData.var_referee" name="var_referee" id="var_referee" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Nom de l'arbitre VAR">
                            </div>
                            <div>
                                <label for="avar_referee" class="block text-sm font-medium text-gray-700">Assistant VAR</label>
                                <input type="text" v-model="formData.avar_referee" name="avar_referee" id="avar_referee" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Nom de l'assistant VAR">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Équipes -->
                <div v-show="activeTab === 'teams'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Équipes</h2>
                        
                        <!-- Home Team -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ optional(optional($match->homeTeam)->club)->name ?? 'Équipe Domicile' }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($homeTeamPlayers as $player)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $player->name }}</p>
                                            <p class="text-sm text-gray-600">#{{ $player->jersey_number ?? 'N/A' }} - {{ $player->position ?? 'N/A' }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button type="button" @click="addGoal('home', '{{ $player->name }}')" 
                                                    class="px-2 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">
                                                But
                                            </button>
                                            <button type="button" @click="addCard('yellow', 'home', '{{ $player->name }}')" 
                                                    class="px-2 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                                Jaune
                                            </button>
                                            <button type="button" @click="addCard('red', 'home', '{{ $player->name }}')" 
                                                    class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">
                                                Rouge
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Away Team -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ optional(optional($match->awayTeam)->club)->name ?? 'Équipe Extérieur' }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($awayTeamPlayers as $player)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $player->name }}</p>
                                            <p class="text-sm text-gray-600">#{{ $player->jersey_number ?? 'N/A' }} - {{ $player->position ?? 'N/A' }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button type="button" @click="addGoal('away', '{{ $player->name }}')" 
                                                    class="px-2 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">
                                                But
                                            </button>
                                            <button type="button" @click="addCard('yellow', 'away', '{{ $player->name }}')" 
                                                    class="px-2 py-1 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                                Jaune
                                            </button>
                                            <button type="button" @click="addCard('red', 'away', '{{ $player->name }}')" 
                                                    class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">
                                                Rouge
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Événements -->
                <div v-show="activeTab === 'events'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Événements</h2>
                        
                        <!-- Timeline des événements -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline des Événements</h3>
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                <div v-for="event in timelineEvents" :key="event.id" 
                                     :class="getEventClass(event.type)"
                                     class="flex items-center p-3 rounded-lg">
                                    <div class="flex-shrink-0 w-12 text-center">
                                        <span class="text-sm font-medium">@{{ event.minute }}'</span>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium">@{{ event.player }}</p>
                                        <p class="text-xs text-gray-600">@{{ event.description }}</p>
                                    </div>
                                    <button @click="removeEvent(event.id)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Ajouter un événement -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Ajouter un Événement</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Minute</label>
                                    <input type="number" v-model="newEvent.minute" min="1" max="120" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Joueur</label>
                                    <input type="text" v-model="newEvent.player" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Type</label>
                                    <select v-model="newEvent.type" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                        <option value="goal">But</option>
                                        <option value="yellow_card">Carton Jaune</option>
                                        <option value="red_card">Carton Rouge</option>
                                        <option value="substitution">Remplacement</option>
                                        <option value="injury">Blessure</option>
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" @click="addEvent" 
                                            class="w-full bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700">
                                        Ajouter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Discipline & Santé -->
                <div v-show="activeTab === 'discipline'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Discipline & Santé</h2>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="disciplinary_incidents" class="block text-sm font-medium text-gray-700">Incidents Disciplinaires</label>
                                <textarea v-model="formData.disciplinary_incidents" name="disciplinary_incidents" id="disciplinary_incidents" rows="4" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Décrivez les incidents disciplinaires..."></textarea>
                            </div>
                            
                            <div>
                                <label for="crowd_incidents" class="block text-sm font-medium text-gray-700">Incidents avec le Public</label>
                                <textarea v-model="formData.crowd_incidents" name="crowd_incidents" id="crowd_incidents" rows="4" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Décrivez les incidents avec le public..."></textarea>
                            </div>
                            
                            <div>
                                <label for="safety_issues" class="block text-sm font-medium text-gray-700">Problèmes de Sécurité</label>
                                <textarea v-model="formData.safety_issues" name="safety_issues" id="safety_issues" rows="4" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Décrivez les problèmes de sécurité..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 6: Observations -->
                <div v-show="activeTab === 'observations'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Observations</h2>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="general_comments" class="block text-sm font-medium text-gray-700">Observations Générales *</label>
                                <textarea v-model="formData.general_comments" name="general_comments" id="general_comments" rows="6" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Décrivez le déroulement général du match, l'ambiance, le comportement des équipes..." required></textarea>
                            </div>
                            
                            <div>
                                <label for="match_quality_assessment" class="block text-sm font-medium text-gray-700">Évaluation de la Qualité du Match</label>
                                <textarea v-model="formData.match_quality_assessment" name="match_quality_assessment" id="match_quality_assessment" rows="4" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Évaluez la qualité technique du match, le niveau de jeu..."></textarea>
                            </div>
                            
                            <div>
                                <label for="match_rating" class="block text-sm font-medium text-gray-700">Note du Match (1-10)</label>
                                <select v-model="formData.match_rating" name="match_rating" id="match_rating" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Sélectionner...</option>
                                    <option v-for="i in 10" :key="i" :value="i">@{{ i }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation et Validation -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-4">
                                <button type="button" @click="previousTab" 
                                        :disabled="activeTab === 'info'"
                                        :class="[
                                            activeTab === 'info' 
                                                ? 'bg-gray-300 cursor-not-allowed' 
                                                : 'bg-gray-600 hover:bg-gray-700',
                                            'text-white px-4 py-2 rounded-md'
                                        ]">
                                    Précédent
                                </button>
                                <button type="button" @click="nextTab" 
                                        :disabled="activeTab === 'observations'"
                                        :class="[
                                            activeTab === 'observations' 
                                                ? 'bg-gray-300 cursor-not-allowed' 
                                                : 'bg-orange-600 hover:bg-orange-700',
                                            'text-white px-4 py-2 rounded-md'
                                        ]">
                                    Suivant
                                </button>
                            </div>
                            
                            <div class="flex space-x-4">
                                <button type="button" @click="saveDraft" 
                                        class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">
                                    Sauvegarder Brouillon
                                </button>
                                <button type="submit" 
                                        :disabled="!isFormValid"
                                        :class="[
                                            isFormValid 
                                                ? 'bg-green-600 hover:bg-green-700' 
                                                : 'bg-gray-300 cursor-not-allowed',
                                            'text-white px-6 py-2 rounded-md'
                                        ]">
                                    Soumettre Rapport
                                </button>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="bg-orange-600 h-2 rounded-full transition-all duration-300" 
                                     :style="{ width: progressPercentage + '%' }"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">Progression: @{{ progressPercentage }}%</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    activeTab: 'info',
                    tabs: [
                        { id: 'info', name: 'Infos Match' },
                        { id: 'officials', name: 'Officiels' },
                        { id: 'teams', name: 'Équipes' },
                        { id: 'events', name: 'Événements' },
                        { id: 'discipline', name: 'Discipline' },
                        { id: 'observations', name: 'Observations' }
                    ],
                    formData: {
                        final_score: '',
                        half_time_score: '',
                        extra_time_minutes: 0,
                        penalty_shootout: false,
                        penalty_shootout_score: '',
                        weather: '',
                        pitch_condition: '',
                        main_referee: '',
                        assistant_referee_1: '',
                        assistant_referee_2: '',
                        fourth_official: '',
                        var_referee: '',
                        avar_referee: '',
                        disciplinary_incidents: '',
                        crowd_incidents: '',
                        safety_issues: '',
                        general_comments: '',
                        match_quality_assessment: '',
                        match_rating: ''
                    },
                    timelineEvents: [],
                    newEvent: {
                        minute: '',
                        player: '',
                        type: 'goal'
                    },
                    eventCounter: 0
                }
            },
            computed: {
                progressPercentage() {
                    const completedTabs = this.tabs.findIndex(tab => tab.id === this.activeTab) + 1;
                    return Math.round((completedTabs / this.tabs.length) * 100);
                },
                isFormValid() {
                    return this.formData.final_score && 
                           this.formData.main_referee && 
                           this.formData.assistant_referee_1 && 
                           this.formData.assistant_referee_2 && 
                           this.formData.fourth_official && 
                           this.formData.general_comments.length >= 10;
                }
            },
            methods: {
                nextTab() {
                    const currentIndex = this.tabs.findIndex(tab => tab.id === this.activeTab);
                    if (currentIndex < this.tabs.length - 1) {
                        this.activeTab = this.tabs[currentIndex + 1].id;
                    }
                },
                previousTab() {
                    const currentIndex = this.tabs.findIndex(tab => tab.id === this.activeTab);
                    if (currentIndex > 0) {
                        this.activeTab = this.tabs[currentIndex - 1].id;
                    }
                },
                addGoal(team, player) {
                    const minute = prompt(`Minute du but pour ${player}:`);
                    if (minute) {
                        this.timelineEvents.push({
                            id: ++this.eventCounter,
                            minute: parseInt(minute),
                            player: player,
                            type: 'goal',
                            description: `But - ${team === 'home' ? '{{ optional(optional($match->homeTeam)->club)->short_name ?? optional(optional($match->homeTeam)->club)->name ?? "Home" }}' : '{{ optional(optional($match->awayTeam)->club)->short_name ?? optional(optional($match->awayTeam)->club)->name ?? "Away" }}'}`
                        });
                        this.sortTimeline();
                    }
                },
                addCard(type, team, player) {
                    const minute = prompt(`Minute du carton ${type === 'yellow' ? 'jaune' : 'rouge'} pour ${player}:`);
                    if (minute) {
                        this.timelineEvents.push({
                            id: ++this.eventCounter,
                            minute: parseInt(minute),
                            player: player,
                            type: type === 'yellow' ? 'yellow_card' : 'red_card',
                            description: `Carton ${type === 'yellow' ? 'jaune' : 'rouge'} - ${team === 'home' ? '{{ optional(optional($match->homeTeam)->club)->short_name ?? optional(optional($match->homeTeam)->club)->name ?? "Home" }}' : '{{ optional(optional($match->awayTeam)->club)->short_name ?? optional(optional($match->awayTeam)->club)->name ?? "Away" }}'}`
                        });
                        this.sortTimeline();
                    }
                },
                addEvent() {
                    if (this.newEvent.minute && this.newEvent.player) {
                        this.timelineEvents.push({
                            id: ++this.eventCounter,
                            minute: parseInt(this.newEvent.minute),
                            player: this.newEvent.player,
                            type: this.newEvent.type,
                            description: this.getEventDescription(this.newEvent.type)
                        });
                        this.sortTimeline();
                        this.newEvent = { minute: '', player: '', type: 'goal' };
                    }
                },
                removeEvent(id) {
                    this.timelineEvents = this.timelineEvents.filter(event => event.id !== id);
                },
                sortTimeline() {
                    this.timelineEvents.sort((a, b) => a.minute - b.minute);
                },
                getEventDescription(type) {
                    const descriptions = {
                        'goal': 'But',
                        'yellow_card': 'Carton Jaune',
                        'red_card': 'Carton Rouge',
                        'substitution': 'Remplacement',
                        'injury': 'Blessure'
                    };
                    return descriptions[type] || 'Événement';
                },
                getEventClass(type) {
                    const classes = {
                        'goal': 'bg-green-100 border-l-4 border-green-500',
                        'yellow_card': 'bg-yellow-100 border-l-4 border-yellow-500',
                        'red_card': 'bg-red-100 border-l-4 border-red-500',
                        'substitution': 'bg-blue-100 border-l-4 border-blue-500',
                        'injury': 'bg-orange-100 border-l-4 border-orange-500'
                    };
                    return classes[type] || 'bg-gray-100 border-l-4 border-gray-500';
                },
                saveDraft() {
                    // Sauvegarder en brouillon
                    console.log('Sauvegarde brouillon...');
                },
                async submitReport() {
                    if (!this.isFormValid) {
                        alert('Veuillez remplir tous les champs obligatoires');
                        return;
                    }

                    // Préparer les données pour l'envoi
                    const reportData = {
                        ...this.formData,
                        goals: this.timelineEvents.filter(e => e.type === 'goal'),
                        yellow_cards: this.timelineEvents.filter(e => e.type === 'yellow_card'),
                        red_cards: this.timelineEvents.filter(e => e.type === 'red_card'),
                        substitutions: this.timelineEvents.filter(e => e.type === 'substitution'),
                        injuries: this.timelineEvents.filter(e => e.type === 'injury')
                    };

                    // Envoyer le rapport
                    try {
                        const response = await axios.post('/referee-save-report', reportData);
                        window.location.href = `/referee-report-success/${response.data.report_id}`;
                    } catch (error) {
                        console.error('Erreur lors de la soumission:', error);
                        alert('Erreur lors de la soumission du rapport');
                    }
                }
            }
        }).mount('#app');
    </script>
</body>
</html>


