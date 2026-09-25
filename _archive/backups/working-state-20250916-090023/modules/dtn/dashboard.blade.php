<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard DTN - Direction Technique Nationale') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Équipes Nationales -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Équipes Nationales</p>
                                <p class="text-2xl font-semibold text-gray-900">12</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sélections Internationales -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Sélections</p>
                                <p class="text-2xl font-semibold text-gray-900">8</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expatriés -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Expatriés</p>
                                <p class="text-2xl font-semibold text-gray-900">45</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Matchs Internationaux -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Matchs Internationaux</p>
                                <p class="text-2xl font-semibold text-gray-900">24</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Équipes Nationales -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Équipes Nationales</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">A</span>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">Équipe A</p>
                                            <p class="text-sm text-gray-500">Séniors Masculins</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">25 joueurs</p>
                                        <p class="text-xs text-gray-500">Prochain match: 15/12/2024</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <span class="text-green-600 font-semibold">U23</span>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">Équipe U23</p>
                                            <p class="text-sm text-gray-500">Espoirs</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">22 joueurs</p>
                                        <p class="text-xs text-gray-500">Prochain match: 20/12/2024</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                            <span class="text-purple-600 font-semibold">F</span>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">Équipe Féminine</p>
                                            <p class="text-sm text-gray-500">Séniors Féminins</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">20 joueuses</p>
                                        <p class="text-xs text-gray-500">Prochain match: 18/12/2024</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
                            <div class="space-y-3">
                                <a href="{{ route('dtn.teams') }}" class="block w-full text-left px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors" data-auto-key="auto.key1">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        <span class="font-medium text-blue-900">Gérer les équipes</span>
                                    </div>
                                </a>

                                <a href="{{ route('dtn.selections') }}" class="block w-full text-left px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors" data-auto-key="auto.key2">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <span class="font-medium text-green-900">Créer une sélection</span>
                                    </div>
                                </a>

                                <a href="{{ route('dtn.expats') }}" class="block w-full text-left px-4 py-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors" data-auto-key="auto.key1">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="font-medium text-yellow-900">Gérer les expatriés</span>
                                    </div>
                                </a>

                                <a href="{{ route('dtn.medical') }}" class="block w-full text-left px-4 py-3 bg-red-50 hover:bg-red-100 rounded-lg transition-colors" data-auto-key="auto.key2">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        <span class="font-medium text-red-900">Données médicales</span>
                                    </div>
                                </a>

                                <a href="{{ route('dtn.reports') }}" class="block w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors" data-auto-key="auto.key1">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="font-medium text-gray-900">Générer un rapport</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prochains événements -->
            <div class="mt-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Prochains Événements</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-4"></div>
                                    <div>
                                        <p class="font-medium text-gray-900">Match International - Équipe A vs Espagne</p>
                                        <p class="text-sm text-gray-500">15 décembre 2024 - 20:00</p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-blue-600">Stade National</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-4"></div>
                                    <div>
                                        <p class="font-medium text-gray-900">Sélection U23 - Stage d'entraînement</p>
                                        <p class="text-sm text-gray-500">20 décembre 2024 - 09:00</p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-green-600">Centre Technique</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-4"></div>
                                    <div>
                                        <p class="font-medium text-gray-900">Match Féminin - Équipe F vs France</p>
                                        <p class="text-sm text-gray-500">18 décembre 2024 - 19:30</p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-purple-600">Stade Municipal</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Initialiser le module DTN avec des données réelles
        document.addEventListener('DOMContentLoaded', function() {
            const dtnContainer = document.getElementById('dtn-dashboard');
            if (dtnContainer) {
                const userData = JSON.parse(dtnContainer.dataset.user);
                const permissions = JSON.parse(dtnContainer.dataset.permissions);
                const module = dtnContainer.dataset.module;
                
                console.log('Module DTN initialisé:', { userData, permissions, module });
                
                // Ici vous pouvez ajouter des interactions JavaScript supplémentaires
                // Par exemple, des graphiques, des mises à jour en temps réel, etc.
            }

            // Gestion des clés automatiques sur les boutons
            const actionButtons = document.querySelectorAll('[data-auto-key]');
            actionButtons.forEach(button => {
                const autoKey = button.getAttribute('data-auto-key');
                
                // Ajouter un indicateur visuel pour les clés automatiques
                const keyIndicator = document.createElement('span');
                keyIndicator.className = 'ml-2 text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded';
                keyIndicator.textContent = autoKey;
                
                // Ajouter l'indicateur au bouton
                const buttonContent = button.querySelector('.flex.items-center');
                if (buttonContent) {
                    buttonContent.appendChild(keyIndicator);
                }
                
                // Ajouter un gestionnaire d'événements pour les clés automatiques
                button.addEventListener('click', function(e) {
                    console.log('Action DTN avec clé automatique:', autoKey);
                    
                    // Vous pouvez ajouter ici la logique pour gérer les clés automatiques
                    // Par exemple, envoyer des données de télémétrie, logging, etc.
                    
                    // Exemple: Envoyer une requête AJAX pour enregistrer l'action
                    fetch('/api/auto-key-action', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            auto_key: autoKey,
                            action: button.href,
                            module: 'dtn',
                            timestamp: new Date().toISOString()
                        })
                    }).catch(error => {
                        console.log('Erreur lors de l\'enregistrement de l\'action:', error);
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>