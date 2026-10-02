@extends('layouts.app')

@section('title', 'Clubs de Football - Plateforme FIT')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-page-header
            title="Clubs"
            subtitle="Gérez les clubs affiliés, leur identité, leur rattachement institutionnel et leurs effectifs."
            eyebrow="Administration"
            :back-href="isset($filtered) && $filtered ? url('/associations-view') : route('modules.index')"
            :back-label="isset($filtered) && $filtered ? 'Retour aux associations' : 'Retour aux modules'"
            :count="$clubs->count()"
            count-label="club(s)"
        >
            @if(\App\Http\Controllers\OrganizationCardController::canEdit(auth()->user(), 'clubs'))
                <x-slot:actions>
                    <a href="{{ route('organization-cards.create', 'clubs') }}"
                       class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                        Ajouter un club
                    </a>
                </x-slot:actions>
            @endif
        </x-page-header>

        <!-- Filtre par association -->
        @if(isset($filtered) && $filtered && isset($association) && $association)
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    @if($association->association_logo_url)
                        <img src="{{ asset('storage/' . $association->association_logo_url) }}" 
                             alt="Logo {{ $association->name }}" 
                             class="h-12 w-12 object-contain rounded-lg border-2 border-blue-200">
                    @endif
                    <div>
                        <h3 class="text-lg font-semibold text-blue-800">{{ $association->name }}</h3>
                        <p class="text-sm text-blue-600">{{ $association->country }} - {{ $association->confederation ?? __('Confédération non spécifiée') }}</p>
                    </div>
                </div>
                <a href="/clubs-view" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    {{ __('🏟️ Voir tous les clubs') }}
                </a>
            </div>
        </div>
        @endif


        <!-- Liste des clubs -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if($clubs->count() > 0)
                @foreach($clubs as $club)
                <!-- Club Card -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <!-- Header de la carte -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-xl font-semibold text-gray-800">{{ $club->name }}</h3>
                                    @if($club->short_name)
                                        <span class="text-sm font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded">{{ $club->short_name }}</span>
                                    @endif
                                    @if($club->association && $club->association->short_name)
                                        <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded border border-red-200">
                                            {{ $club->association->short_name }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600">{{ $club->city ?? 'N/A' }}, {{ $club->country ?? __('Pays non spécifié') }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $club->status === 'active' ? 'bg-green-100 text-green-800' : ($club->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $club->status === 'active' ? 'Actif' : ($club->status === 'inactive' ? 'Inactif' : 'En attente') }}
                                </span>
                            </div>
                        </div>

                        <!-- Logo du club -->
                        <div class="flex justify-center mb-4">
                            @if($club->logo_url)
                                <img src="{{ \Illuminate\Support\Str::startsWith($club->logo_url, ['http://', 'https://']) ? $club->logo_url : asset('storage/' . $club->logo_url) }}" 
                                     alt="Logo {{ $club->name }}" 
                                     class="h-20 w-20 object-contain rounded-lg border-2 border-gray-200">
                            @else
                                <div class="h-20 w-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-2xl">{{ substr($club->name, 0, 2) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Informations du club -->
                        <div class="space-y-2 mb-4">
                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <span class="text-gray-400 mr-2">🏟️</span>
                                    <span>{{ $club->address ?? __('Adresse non spécifiée') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-400 mr-2">📞</span>
                                    <span>{{ $club->phone ?? __('Téléphone non spécifié') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-400 mr-2">📧</span>
                                    <span>{{ $club->email ?? __('Email non spécifié') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-gray-400 mr-2">🌐</span>
                                    <span>{{ $club->website ?? __('Site web non spécifié') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Année de fondation -->
                        @if($club->founded_year)
                        <div class="mb-4 p-2 bg-gray-50 rounded-lg text-center">
                            <span class="text-sm text-gray-600">{{ __("Fondé en") }} {{ $club->founded_year }}</span>
                        </div>
                        @endif

                        <!-- Association -->
                        @if($club->association)
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    @if($club->association->association_logo_url)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($club->association->association_logo_url, ['http://', 'https://']) ? $club->association->association_logo_url : asset('storage/' . $club->association->association_logo_url) }}" 
                                             alt="Logo {{ $club->association->name }}" 
                                             class="h-8 w-8 object-contain mr-3 rounded-lg border border-blue-200">
                                    @elseif($club->association->logo_path)
                                        <img src="{{ asset('storage/' . $club->association->logo_path) }}" 
                                             alt="Logo {{ $club->association->name }}" 
                                             class="h-8 w-8 object-contain mr-3 rounded-lg border border-blue-200">
                                    @else
                                        <div class="h-8 w-8 bg-gradient-to-br from-red-500 to-yellow-600 rounded mr-3 flex items-center justify-center border border-blue-200">
                                            <span class="text-white font-bold text-xs">{{ substr($club->association->name, 0, 2) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-blue-800">{{ $club->association->name }}</div>
                                        @if($club->association->short_name)
                                            <div class="text-xs text-blue-600 font-medium">{{ $club->association->short_name }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-blue-600">{{ $club->association->country ?? __('Pays non spécifié') }}</span>
                                </div>
                            </div>
                            <div class="mt-2 text-center">
                                <a href="/clubs-view?association_id={{ $club->association->id }}" 
                                   class="text-xs text-blue-600 hover:text-blue-800 underline">
                                    {{ __('🏟️ Voir tous les clubs de cette association') }}
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- Actions -->
                        <div class="space-y-2">
                            <!-- Actions principales -->
                            <div class="flex space-x-2">
                                <a href="{{ route('clubs-view.show') }}?id={{ $club->id }}" 
                                   class="flex-1 px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    {{ __('👁️ Voir détails') }}
                                </a>
                                @if(\App\Http\Controllers\OrganizationCardController::canEdit(auth()->user(), 'clubs', $club))
                                <a href="{{ route('organization-cards.edit', ['clubs', $club->id]) }}"
                                   class="flex-1 px-4 py-2 bg-yellow-600 text-white text-center rounded-lg hover:bg-yellow-700 transition-colors text-sm">
                                    {{ __('pcma_extra.label_723bbbfede8a') }}
                                </a>
                                @endif
                            </div>
                            
                            @if(\App\Http\Controllers\OrganizationCardController::canEdit(auth()->user(), 'clubs', $club))
                            <!-- Actions de gestion -->
                            <div class="flex space-x-2">
                                <!-- Bouton Supprimer -->
                                <button onclick="confirmDelete({{ $club->id }}, '{{ $club->name }}')" 
                                        class="flex-1 px-3 py-2 bg-red-600 text-white text-center rounded-lg hover:bg-red-700 transition-colors text-sm">
                                    🗑️ Supprimer
                                </button>
                                
                                <!-- Bouton Fusionner -->
                                <button onclick="showMergeOptions({{ $club->id }}, '{{ $club->name }}')" 
                                        class="flex-1 px-3 py-2 bg-purple-600 text-white text-center rounded-lg hover:bg-purple-700 transition-colors text-sm">
                                    🔗 Fusionner
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">🏟️</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Aucun club trouvé') }}</h3>
                    <p class="text-gray-500 mb-6">{{ __('Aucun club n\'est actuellement enregistré dans la base de données') }}</p>
                    <a href="/modules" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                        {{ __('📋 Retour aux modules') }}
                    </a>
                </div>
            @endif
        </div>

        <!-- Statistiques -->
        @if($clubs->count() > 0)
        <div class="mt-12 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">{{ __('📊 Statistiques des clubs') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $clubs->count() }}</div>
                    <div class="text-sm text-gray-600">{{ __('Clubs total') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $clubs->where('status', 'active')->count() }}</div>
                    <div class="text-sm text-gray-600">{{ __('Clubs actifs') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600">{{ $clubs->where('status', 'inactive')->count() }}</div>
                    <div class="text-sm text-gray-600">{{ __('Clubs inactifs') }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Modal de confirmation de suppression -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <div class="flex items-center mb-4">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <span class="text-red-600 text-xl">🗑️</span>
                    </div>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmer la suppression</h3>
                    <p class="text-sm text-gray-500 mb-6">{{ __('Êtes-vous sûr de vouloir supprimer le club') }}<strong id="clubNameToDelete"></strong> ?
                        <br><br>
                        <span class="text-red-600 font-medium">{{ __('⚠️ Cette action est irréversible !') }}</span>
                    </p>
                    <div class="flex space-x-3">
                        <button onclick="closeDeleteModal()" 
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            {{ __('clinical.cancel') }}
                        </button>
                        <button id="confirmDeleteBtn" 
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            {{ __('common.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de fusion -->
    <div id="mergeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
                <div class="flex items-center mb-4">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100">
                        <span class="text-purple-600 text-xl">🔗</span>
                    </div>
                </div>
                <div class="text-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Fusionner le club') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('Sélectionnez le club avec lequel vous voulez fusionner') }}<strong id="clubNameToMerge"></strong>
                    </p>
                </div>
                
                <div id="mergeOptions" class="space-y-3 mb-6">
                    <!-- Les options de fusion seront générées dynamiquement -->
                </div>
                
                <div class="flex space-x-3">
                    <button onclick="closeMergeModal()" 
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        {{ __('clinical.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentClubId = null;
        let currentClubName = '';

        // Fonction de confirmation de suppression
        function confirmDelete(clubId, clubName) {
            currentClubId = clubId;
            currentClubName = clubName;
            document.getElementById('clubNameToDelete').textContent = clubName;
            document.getElementById('deleteModal').classList.remove('hidden');
            
            // Configurer le bouton de confirmation
            document.getElementById('confirmDeleteBtn').onclick = function() {
                deleteClub(clubId);
            };
        }

        // Fermer la modal de suppression
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Supprimer le club
        function deleteClub(clubId) {
            fetch(`/clubs-view/delete/${clubId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger la page pour mettre à jour la liste
                    window.location.reload();
                } else {
                    alert(@json(__('Erreur lors de la suppression : ')) + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert(@json(__('Erreur lors de la suppression')));
            })
            .finally(() => {
                closeDeleteModal();
            });
        }

        // Afficher les options de fusion
        function showMergeOptions(clubId, clubName) {
            currentClubId = clubId;
            currentClubName = clubName;
            document.getElementById('clubNameToMerge').textContent = clubName;
            
            // Charger les clubs disponibles pour la fusion
            loadMergeOptions(clubId);
            
            document.getElementById('mergeModal').classList.remove('hidden');
        }

        // Fermer la modal de fusion
        function closeMergeModal() {
            document.getElementById('mergeModal').classList.add('hidden');
        }

        // Charger les options de fusion
        function loadMergeOptions(clubId) {
            fetch(`/clubs-view/merge-options/${clubId}`)
            .then(response => response.json())
            .then(data => {
                const mergeOptions = document.getElementById('mergeOptions');
                mergeOptions.innerHTML = '';
                
                if (data.clubs && data.clubs.length > 0) {
                    data.clubs.forEach(club => {
                        const option = document.createElement('div');
                        option.className = 'p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer';
                        option.onclick = () => selectMergeTarget(club.id, club.name);
                        
                        option.innerHTML = `
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900">${club.name}</div>
                                    <div class="text-sm text-gray-500">${club.city || 'N/A'}, ${club.country || 'N/A'}</div>
                                    <div class="text-xs text-gray-400">${club.players_count} joueurs, ${club.licenses_count} licences</div>
                                </div>
                                <div class="text-blue-600">→</div>
                            </div>
                        `;
                        
                        mergeOptions.appendChild(option);
                    });
                } else {
                    mergeOptions.innerHTML = '<p class="text-gray-500 text-center">Aucun club disponible pour la fusion</p>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('mergeOptions').innerHTML = '<p class="text-red-500 text-center">Erreur lors du chargement des options</p>';
            });
        }

        // Sélectionner la cible de fusion
        function selectMergeTarget(targetClubId, targetClubName) {
            if (confirm(`Êtes-vous sûr de vouloir fusionner "${currentClubName}" avec "${targetClubName}" ?\n\nCette action est irréversible !`)) {
                mergeClubs(currentClubId, targetClubId);
            }
        }

        // Fusionner les clubs
        function mergeClubs(sourceClubId, targetClubId) {
            fetch(`/clubs-view/merge`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    source_club_id: sourceClubId,
                    target_club_id: targetClubId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(@json(__('Fusion réussie !')));
                    window.location.reload();
                } else {
                    alert(@json(__('Erreur lors de la fusion : ')) + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert(@json(__('Erreur lors de la fusion')));
            })
            .finally(() => {
                closeMergeModal();
            });
        }
    </script>
</body>
</html>
