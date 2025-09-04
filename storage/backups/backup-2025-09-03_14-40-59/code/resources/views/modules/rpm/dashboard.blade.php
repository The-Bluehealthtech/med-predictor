<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard RPM - Régulation & Préparation Matchs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Sessions d'entraînement -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Sessions d'entraînement</p>
                                <p class="text-2xl font-semibold text-gray-900">156</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Matchs -->
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
                                <p class="text-sm font-medium text-gray-500">Matchs</p>
                                <p class="text-2xl font-semibold text-gray-900">24</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charge de travail -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Charge de travail</p>
                                <p class="text-2xl font-semibold text-gray-900">85%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Présence -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Taux de présence</p>
                                <p class="text-2xl font-semibold text-gray-900">92%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Calendrier d'entraînement -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Calendrier d'Entraînement - Cette Semaine</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">L</span>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">Lundi - Entraînement Technique</p>
                                            <p class="text-sm text-gray-500">09:00 - 11:00 | Terrain Principal</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">25 joueurs</p>
                                        <p class="text-xs text-gray-500">Présence: 92%</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <span class="text-green-600 font-semibold">M</span>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">Mardi - Entraînement Physique</p>
                                            <p class="text-sm text-gray-500">14:00 - 16:00 | Salle de musculation</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">22 joueurs</p>
                                        <p class="text-xs text-gray-500">Présence: 88%</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                            <span class="text-purple-600 font-semibold">J</span>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">Jeudi - Match d'entraînement</p>
                                            <p class="text-sm text-gray-500">16:00 - 18:00 | Terrain Principal</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">20 joueurs</p>
                                        <p class="text-xs text-gray-500">Présence: 80%</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <span class="text-yellow-600 font-semibold">V</span>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">Vendredi - Récupération</p>
                                            <p class="text-sm text-gray-500">10:00 - 11:30 | Piscine</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">18 joueurs</p>
                                        <p class="text-xs text-gray-500">Présence: 72%</p>
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
                                <a href="{{ route('rpm.calendar') }}" class="block w-full text-left px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors" data-auto-key="auto.key1">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium text-blue-900">Voir le calendrier</span>
                                    </div>
                                </a>

                                <a href="{{ route('rpm.sessions') }}" class="block w-full text-left px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors" data-auto-key="auto.key2">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        <span class="font-medium text-green-900">Créer une session</span>
                                    </div>
                                </a>

                                <a href="{{ route('rpm.matches') }}" class="block w-full text-left px-4 py-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors" data-auto-key="auto.key1">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-medium text-yellow-900">Planifier un match</span>
                                    </div>
                                </a>

                                <a href="{{ route('rpm.load') }}" class="block w-full text-left px-4 py-3 bg-red-50 hover:bg-red-100 rounded-lg transition-colors" data-auto-key="auto.key2">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <span class="font-medium text-red-900">Monitoring charge</span>
                                    </div>
                                </a>

                                <a href="{{ route('rpm.attendance') }}" class="block w-full text-left px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors" data-auto-key="auto.key1">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="font-medium text-purple-900">Suivi présence</span>
                                    </div>
                                </a>

                                <a href="{{ route('rpm.reports') }}" class="block w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors" data-auto-key="auto.key2">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="font-medium text-gray-900">Générer rapport</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prochains matchs -->
            <div class="mt-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Prochains Matchs</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-4"></div>
                                    <div>
                                        <p class="font-medium text-gray-900">Match amical - Équipe A vs Club Local</p>
                                        <p class="text-sm text-gray-500">15 décembre 2024 - 16:00</p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-blue-600">Terrain Principal</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-4"></div>
                                    <div>
                                        <p class="font-medium text-gray-900">Match de championnat - Équipe A vs Rival</p>
                                        <p class="text-sm text-gray-500">22 décembre 2024 - 20:00</p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-green-600">Stade Municipal</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-4"></div>
                                    <div>
                                        <p class="font-medium text-gray-900">Match de coupe - Équipe A vs Adversaire</p>
                                        <p class="text-sm text-gray-500">29 décembre 2024 - 18:30</p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-purple-600">Stade National</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Initialiser le module RPM avec des données réelles
        document.addEventListener('DOMContentLoaded', function() {
            const rpmContainer = document.getElementById('rpm-dashboard');
            if (rpmContainer) {
                const userData = JSON.parse(rpmContainer.dataset.user);
                const permissions = JSON.parse(rpmContainer.dataset.permissions);
                const module = rpmContainer.dataset.module;
                
                console.log('Module RPM initialisé:', { userData, permissions, module });
                
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
                    console.log('Action RPM avec clé automatique:', autoKey);
                    
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
                            module: 'rpm',
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