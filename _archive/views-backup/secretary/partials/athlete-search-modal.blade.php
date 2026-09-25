<!-- Modal Recherche Athlète -->
<div x-data="{ show: false, searchTerm: '', athletes: [] }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-search text-purple-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Rechercher un Athlète
                        </h3>
                        <div class="mt-2 space-y-4">
                            <!-- Barre de recherche -->
                            <div>
                                <label for="athlete_search" class="block text-sm font-medium text-gray-700">Recherche</label>
                                <div class="mt-1 relative">
                                    <input type="text" id="athlete_search" x-model="searchTerm" @input.debounce.300ms="searchAthletes()" placeholder="Nom ou FIFA Connect ID..." class="block w-full pr-10 border-gray-300 rounded-md leading-5 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Résultats -->
                            <div x-show="athletes.length > 0" class="max-h-60 overflow-y-auto">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Résultats</h4>
                                <div class="space-y-2">
                                    <template x-for="athlete in athletes" :key="athlete.id">
                                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <i class="fas fa-user text-gray-600"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900" x-text="athlete.name"></p>
                                                    <p class="text-sm text-gray-500" x-text="'FIFA ID: ' + athlete.fifa_connect_id"></p>
                                                </div>
                                            </div>
                                            <button type="button" @click="selectAthlete(athlete)" class="ml-2 inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                                Sélectionner
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Aucun résultat -->
                            <div x-show="searchTerm && athletes.length === 0" class="text-center py-4">
                                <i class="fas fa-search text-gray-400 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-500">Aucun athlète trouvé</p>
                            </div>

                            <!-- Instructions -->
                            <div x-show="!searchTerm" class="text-center py-4">
                                <i class="fas fa-info-circle text-gray-400 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-500">Tapez un nom ou un FIFA Connect ID pour rechercher</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" @click="show = false" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function searchAthletes() {
    if (this.searchTerm.length < 2) {
        this.athletes = [];
        return;
    }

    fetch(`/secretary/athletes/search?q=${encodeURIComponent(this.searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            this.athletes = data.athletes || [];
        })
        .catch(error => {
            console.error('Erreur lors de la recherche:', error);
            this.athletes = [];
        });
}

function selectAthlete(athlete) {
    // Ici vous pouvez implémenter la logique pour sélectionner un athlète
    console.log('Athlète sélectionné:', athlete);
    this.show = false;
    // Vous pouvez émettre un événement ou mettre à jour un champ caché
}
</script> 