@extends('layouts.app')

@section('title', __('competitions.discipline_sanctions_page.page_title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-gavel text-red-600 mr-3"></i>
                {{ __('competitions.discipline_sanctions_page.page_title') }}
            </h1>
            <p class="text-gray-600 mt-2">{{ __('competitions.discipline_sanctions_page.subtitle') }}</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="exportSanctions()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                📥 {{ __('competitions.discipline_sanctions_page.export_button') }}
            </button>
            <button onclick="addNewSanction()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                ➕ {{ __('competitions.discipline_sanctions_page.add_button') }}
            </button>
            <a href="{{ route('modules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                ← {{ __('competitions.discipline_sanctions_page.back_button') }}
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.discipline_sanctions_page.stat_total_label') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sanctions->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.discipline_sanctions_page.stat_pending_label') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sanctions->where('statut_code', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.discipline_sanctions_page.stat_validated_label') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sanctions->where('statut_code', 'validated')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-euro-sign text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('competitions.discipline_sanctions_page.stat_total_fines_label') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sanctions->sum('amende') }} TND</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des sanctions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('competitions.discipline_sanctions_page.section_title') }}</h2>
        </div>
        
        @if($sanctions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.discipline_sanctions_page.col_player') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.discipline_sanctions_page.col_club') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.discipline_sanctions_page.col_match') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.discipline_sanctions_page.col_type') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.discipline_sanctions_page.col_status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.discipline_sanctions_page.col_fine') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.discipline_sanctions_page.col_suspension') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('competitions.discipline_sanctions_page.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sanctions as $sanction)
                            <tr class="hover:bg-gray-50" data-sanction-id="{{ $sanction['id'] }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $sanction['joueur'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $sanction['club'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $sanction['match'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($sanction['type_code'] === 'yellow_card')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ __('competitions.discipline_sanctions_page.type_yellow_card') }}
                                        </span>
                                    @elseif($sanction['type_code'] === 'red_card')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ __('competitions.discipline_sanctions_page.type_red_card') }}
                                        </span>
                                    @elseif($sanction['type_code'] === 'second_yellow')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Deuxième carton jaune → carton rouge
                                        </span>
                                    @elseif($sanction['type_code'] === 'disciplinary_incident')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                            {{ __('competitions.discipline_sanctions_page.type_disciplinary_incident') }}
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $sanction['type'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($sanction['statut_code'] === 'validated')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ __('competitions.discipline_sanctions_page.status_validated') }}
                                        </span>
                                    @elseif($sanction['statut_code'] === 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                            {{ __('competitions.discipline_sanctions_page.status_pending') }}
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $sanction['statut'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($sanction['amende'] > 0)
                                        <span class="text-red-600 font-semibold">{{ $sanction['amende'] }} TND</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($sanction['suspension'] > 0)
                                        <span class="text-orange-600 font-semibold">{{ $sanction['suspension'] }} {{ __('competitions.discipline_sanctions_page.suspension_days_unit') }}</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($sanction['statut_code'] === 'pending')
                                        <button onclick="validateSanction({{ $sanction['id'] }})" class="text-green-600 hover:text-green-900 px-2 py-1 rounded" title="{{ __('competitions.discipline_sanctions_page.validate_button') }}">
                                            ✅ {{ __('competitions.discipline_sanctions_page.validate_button') }}
                                        </button>
                                        <button onclick="rejectSanction({{ $sanction['id'] }})" class="text-red-600 hover:text-red-900 px-2 py-1 rounded" title="{{ __('competitions.discipline_sanctions_page.reject_button') }}">
                                            ❌ {{ __('competitions.discipline_sanctions_page.reject_button') }}
                                        </button>
                                        @endif
                                        @if($sanction['type_code'] !== 'none')
                                        <button onclick="editSanction({{ $sanction['id'] }})" class="text-yellow-600 hover:text-yellow-900 px-2 py-1 rounded" title="{{ __('competitions.discipline_sanctions_page.edit_button') }}">
                                            ✏️ {{ __('competitions.discipline_sanctions_page.edit_button') }}
                                        </button>
                                        @endif
                                        <button onclick="viewSanction({{ $sanction['id'] }})" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded" title="{{ __('competitions.discipline_sanctions_page.view_button_title') }}">
                                            👁️ {{ __('competitions.discipline_sanctions_page.view_button') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6">
                <div class="text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('competitions.discipline_sanctions_page.empty_state_title') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('competitions.discipline_sanctions_page.empty_state_text') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal d'édition des sanctions -->
<div id="editSanctionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    ✏️ {{ __('competitions.discipline_sanctions_page.edit_modal_title') }}
                </h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <form id="editSanctionForm">
                <input type="hidden" id="editSanctionId" name="sanction_id">
                
                <div class="mb-4">
                    <label for="editAmende" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.discipline_sanctions_page.fine_label') }}
                    </label>
                    <input type="number" id="editAmende" name="amende" min="0" step="0.01" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0.00">
                </div>
                
                <div class="mb-4">
                    <label for="editSuspension" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.discipline_sanctions_page.suspension_label') }}
                    </label>
                    <input type="number" id="editSuspension" name="suspension" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0">
                </div>
                
                <div class="mb-4">
                    <label for="editMotif" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.discipline_sanctions_page.reason_label') }}
                    </label>
                    <textarea id="editMotif" name="motif" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="{{ __('competitions.discipline_sanctions_page.reason_placeholder') }}"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        {{ __('competitions.discipline_sanctions_page.cancel_button') }}
                    </button>
                    <button type="button" onclick="saveSanctionChanges()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        💾 {{ __('competitions.discipline_sanctions_page.save_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'ajout de nouvelle sanction -->
<div id="addSanctionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    ➕ {{ __('competitions.discipline_sanctions_page.add_modal_title') }}
                </h3>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <form id="addSanctionForm">
                <div class="mb-4">
                    <label for="addJoueur" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.discipline_sanctions_page.player_label') }}
                    </label>
                    <select id="addJoueur" name="joueur" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('competitions.discipline_sanctions_page.select_player_placeholder') }}</option>
                        {{-- Liste réelle des joueurs déjà concernés par une sanction (voir CompetitionController::associationDisciplineSanctions) --}}
                        @foreach($joueursList as $joueurOption)
                            <option value="{{ $joueurOption }}">{{ $joueurOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="addMatch" class="block text-sm font-medium text-gray-700 mb-2">Match</label>
                    <select id="addMatch" name="match_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Sélectionner un match</option>
                        @foreach($matchesList as $matchOption)
                            <option value="{{ $matchOption->id }}">{{ $matchOption->competition?->name }} — {{ $matchOption->match_date }} — {{ $matchOption->homeTeam?->club?->name }} / {{ $matchOption->awayTeam?->club?->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="addDirigeant" class="block text-sm font-medium text-gray-700 mb-2">Dirigeant</label>
                    <select id="addDirigeant" name="dirigeant_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Aucun dirigeant</option>
                        @foreach($dirigeantsList as $dirigeant)
                            <option value="{{ $dirigeant->id }}">{{ $dirigeant->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="addType" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.discipline_sanctions_page.type_label') }}
                    </label>
                    <select id="addType" name="type" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="yellow_card">{{ __('competitions.discipline_sanctions_page.type_yellow_card') }}</option>
                        <option value="red_card">{{ __('competitions.discipline_sanctions_page.type_red_card') }}</option>
                        <option value="second_yellow">Deuxième carton jaune entraînant un carton rouge</option>
                        <option value="disciplinary_incident">{{ __('competitions.discipline_sanctions_page.type_disciplinary_incident') }}</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="addAmende" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.discipline_sanctions_page.fine_label') }}
                    </label>
                    <input type="number" id="addAmende" name="amende" min="0" step="0.01" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0.00">
                </div>
                
                <div class="mb-4">
                    <label for="addSuspension" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.discipline_sanctions_page.suspension_label') }}
                    </label>
                    <input type="number" id="addSuspension" name="suspension" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0">
                </div>
                
                <div class="mb-4">
                    <label for="addMotif" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('competitions.discipline_sanctions_page.reason_label') }}
                    </label>
                    <select id="addMotif" name="motif" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner un motif FIFA</option>
                        <optgroup label="Avertissements — cartons jaunes">
                            <option value="J1">J1 — Comportement antisportif</option>
                            <option value="J2">J2 — Désapprobation en paroles ou en actes</option>
                            <option value="J3">J3 — Infractions répétées aux Lois du Jeu</option>
                            <option value="J4">J4 — Retarder la reprise du jeu</option>
                            <option value="J5">J5 — Non-respect de la distance requise</option>
                            <option value="J6">J6 — Entrer ou revenir sans autorisation</option>
                            <option value="J7">J7 — Quitter délibérément le terrain sans autorisation</option>
                        </optgroup>
                        <optgroup label="Exclusions — cartons rouges">
                            <option value="R1">R1 — Faute grossière</option>
                            <option value="R2">R2 — Acte de brutalité</option>
                            <option value="R3">R3 — Cracher</option>
                            <option value="R4">R4 — DOGSO-H (main)</option>
                            <option value="R5">R5 — DOGSO-F (faute)</option>
                            <option value="R6">R6 — Propos ou gestes blessants, injurieux ou grossiers</option>
                            <option value="2J">2J — Deuxième avertissement dans le même match entraînant un rouge</option>
                        </optgroup>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        {{ __('competitions.discipline_sanctions_page.cancel_button') }}
                    </button>
                    <button type="button" onclick="saveNewSanction()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        ➕ {{ __('competitions.discipline_sanctions_page.add_submit_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de détails des sanctions -->
<div id="viewSanctionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    👁️ {{ __('competitions.discipline_sanctions_page.view_modal_title') }}
                </h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            
            <input type="hidden" id="viewSanctionId" name="sanction_id">
            
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">{{ __('competitions.discipline_sanctions_page.view_player_label') }}</span>
                    <span id="viewJoueur" class="text-sm text-gray-900"></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">{{ __('competitions.discipline_sanctions_page.view_club_label') }}</span>
                    <span id="viewClub" class="text-sm text-gray-900"></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">{{ __('competitions.discipline_sanctions_page.view_match_label') }}</span>
                    <span id="viewMatch" class="text-sm text-gray-900"></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">{{ __('competitions.discipline_sanctions_page.view_type_label') }}</span>
                    <span id="viewType" class="text-sm text-gray-900"></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">{{ __('competitions.discipline_sanctions_page.view_status_label') }}</span>
                    <span id="viewStatut" class="text-sm text-gray-900"></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">{{ __('competitions.discipline_sanctions_page.view_fine_label') }}</span>
                    <span id="viewAmende" class="text-sm text-gray-900"></span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">{{ __('competitions.discipline_sanctions_page.view_suspension_label') }}</span>
                    <span id="viewSuspension" class="text-sm text-gray-900"></span>
                </div>
            </div>
            
            <div class="flex justify-end mt-6">
                <button onclick="closeViewModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    {{ __('competitions.discipline_sanctions_page.close_button') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const DISCIPLINE_SANCTIONS_SUSPENSION_UNIT = @json(__('competitions.discipline_sanctions_page.suspension_days_unit'));

// Fonction pour valider une sanction
// NOTE (audit factice -> reel, 2026-09) : ce bouton affichait auparavant un
// faux message de succes ("validee avec succes") sans jamais rien
// enregistrer (le commentaire d'origine l'admettait : "Ici vous pourriez
// faire un appel AJAX pour valider"). Les sanctions de cette page sont
// calculees a la volee a partir des rapports d'arbitres (cartons), il n'y a
// pas de table "sanctions" ni de statut reel a modifier : on informe donc
// honnetement l'utilisateur plutot que de simuler un succes.
function validateSanction(sanctionId) {
    if (confirm(@json(__('competitions.discipline_sanctions_page.js_validate_confirm')))) {
        alert(@json(__('competitions.discipline_sanctions_page.js_validate_unavailable')));
    }
}

// Fonction pour rejeter une sanction
// NOTE (audit factice -> reel, 2026-09) : meme constat que validateSanction()
// ci-dessus, ce bouton affichait un faux message de succes sans rien
// enregistrer.
function rejectSanction(sanctionId) {
    if (confirm(@json(__('competitions.discipline_sanctions_page.js_reject_confirm')))) {
        alert(@json(__('competitions.discipline_sanctions_page.js_reject_unavailable')));
    }
}

// Fonction pour voir les détails d'une sanction
function viewSanction(sanctionId) {
    // Ouvrir le modal de détails
    document.getElementById('viewSanctionModal').classList.remove('hidden');
    document.getElementById('viewSanctionId').value = sanctionId;
    
    // Récupérer les données de la ligne du tableau
    const row = document.querySelector(`tr[data-sanction-id="${sanctionId}"]`);
    if (row) {
        const cells = row.querySelectorAll('td');
        const joueur = cells[0].textContent.trim();
        const club = cells[1].textContent.trim();
        const match = cells[2].textContent.trim();
        const type = cells[3].textContent.trim();
        const statut = cells[4].textContent.trim();
        const amende = cells[5].textContent.trim();
        const suspension = cells[6].textContent.trim();
        
        // Remplir le modal avec les données
        document.getElementById('viewJoueur').textContent = joueur;
        document.getElementById('viewClub').textContent = club;
        document.getElementById('viewMatch').textContent = match;
        document.getElementById('viewType').textContent = type;
        document.getElementById('viewStatut').textContent = statut;
        document.getElementById('viewAmende').textContent = amende;
        document.getElementById('viewSuspension').textContent = suspension;
    }
}

// Fonction pour fermer le modal de détails
function closeViewModal() {
    document.getElementById('viewSanctionModal').classList.add('hidden');
}

// Fonction pour éditer une sanction
function editSanction(sanctionId) {
    // Ouvrir le modal d'édition
    document.getElementById('editSanctionModal').classList.remove('hidden');
    document.getElementById('editSanctionId').value = sanctionId;
    
    // Récupérer les données de la ligne du tableau
    const row = document.querySelector(`tr[data-sanction-id="${sanctionId}"]`);
    if (row) {
        const cells = row.querySelectorAll('td');
        const amende = cells[5].textContent.trim().replace(' TND', '').replace('-', '0');
        const suspension = cells[6].textContent.trim().replace(' ' + DISCIPLINE_SANCTIONS_SUSPENSION_UNIT, '').replace('-', '0');
        
        // Remplir le modal avec les données
        document.getElementById('editAmende').value = amende;
        document.getElementById('editSuspension').value = suspension;
    }
}

// Fonction pour fermer le modal d'édition
function closeEditModal() {
    document.getElementById('editSanctionModal').classList.add('hidden');
}

// Fonction pour sauvegarder les modifications
// NOTE (audit factice -> reel, 2026-09) : cette fonction affichait
// auparavant un faux message de succes ("Sanction modifiee avec succes !")
// puis rechargeait la page, alors qu'aucune donnee n'etait reellement
// enregistree (voir "Simulation de sauvegarde" ci-dessous, seulement logue
// dans la console). Les sanctions affichees ici sont calculees a la volee a
// partir des rapports d'arbitres : il n'existe pas de table "sanctions"
// reelle dans laquelle enregistrer une modification individuelle. On
// informe donc honnetement l'utilisateur plutot que de simuler un succes
// puis recharger une page qui n'aurait de toute facon pas change.
function saveSanctionChanges() {
    const amende = document.getElementById('editAmende').value;
    const suspension = document.getElementById('editSuspension').value;

    // Validation
    if (amende < 0 || suspension < 0) {
        alert(@json(__('competitions.discipline_sanctions_page.js_negative_values_error')));
        return;
    }

    alert(@json(__('competitions.discipline_sanctions_page.js_save_unavailable')));
    closeEditModal();
}

// Fonction pour exporter les sanctions
function exportSanctions() {
    console.log(@json(__('competitions.discipline_sanctions_page.js_export_console_log')));
    
    // Créer un fichier CSV avec les données
    const table = document.querySelector('table');
    const rows = table.querySelectorAll('tbody tr');
    
    let csv = [
        @json(__('competitions.discipline_sanctions_page.col_player')),
        @json(__('competitions.discipline_sanctions_page.col_club')),
        @json(__('competitions.discipline_sanctions_page.col_match')),
        @json(__('competitions.discipline_sanctions_page.col_type')),
        @json(__('competitions.discipline_sanctions_page.col_status')),
        @json(__('competitions.discipline_sanctions_page.csv_fine_header')),
        @json(__('competitions.discipline_sanctions_page.col_suspension'))
    ].join(',') + '\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const joueur = cells[0].textContent.trim();
        const club = cells[1].textContent.trim();
        const match = cells[2].textContent.trim();
        const type = cells[3].textContent.trim();
        const statut = cells[4].textContent.trim();
        const amende = cells[5].textContent.trim();
        const suspension = cells[6].textContent.trim();
        
        csv += `"${joueur}","${club}","${match}","${type}","${statut}","${amende}","${suspension}"\n`;
    });
    
    // Télécharger le fichier
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'sanctions_disciplinaires.csv';
    a.click();
    window.URL.revokeObjectURL(url);
    
    alert(@json(__('competitions.discipline_sanctions_page.js_export_done')));
}

// Fonction pour ajouter une nouvelle sanction
document.getElementById('addType')?.addEventListener('change', function () {
    const motif = document.getElementById('addMotif');
    const prefix = this.value === 'yellow_card' ? 'J' : (this.value === 'red_card' ? 'R' : (this.value === 'second_yellow' ? '2J' : ''));
    Array.from(motif.options).forEach(option => {
        option.hidden = !!prefix && option.value && !option.value.startsWith(prefix);
    });
    motif.value = '';
});

// Appliquer le filtre dès l'ouverture de la page : aucun motif d'un autre
// type ne doit être sélectionnable avant un changement manuel du type.
document.getElementById('addType')?.dispatchEvent(new Event('change'));

function addNewSanction() {
    // Ouvrir le modal d'ajout
    document.getElementById('addSanctionModal').classList.remove('hidden');
}

// Fonction pour fermer le modal d'ajout
// NOTE (audit factice -> reel, 2026-09) : manquait dans le fichier d'origine,
// alors que le modal d'ajout ("Nouvelle Sanction") l'appelait deja via
// onclick="closeAddModal()" — les boutons "Annuler"/la croix ne faisaient
// donc rien.
function closeAddModal() {
    document.getElementById('addSanctionModal').classList.add('hidden');
}

// Fonction pour enregistrer une nouvelle sanction
// NOTE (audit factice -> reel, 2026-09) : le bouton "Enregistrer" du modal
// d'ajout appelait onclick="saveNewSanction()" mais cette fonction n'existait
// nulle part dans le fichier — cliquer sur ce bouton ne faisait donc
// litteralement rien (erreur JS silencieuse). Il n'existe pas de table
// "sanctions" reelle dans laquelle creer une sanction manuelle : on informe
// donc honnetement l'utilisateur plutot que de laisser le bouton casse.
function saveNewSanction() {
    const type = document.getElementById('addType').value;
    const motif = document.getElementById('addMotif').value;
    const expectedPrefix = type === 'yellow_card' ? 'J' : (type === 'red_card' ? 'R' : (type === 'second_yellow' ? '2J' : ''));
    if (expectedPrefix && !motif.startsWith(expectedPrefix)) {
        alert('Le motif sélectionné ne correspond pas au type de sanction.');
        return;
    }
    alert(@json(__('competitions.discipline_sanctions_page.js_add_unavailable')));
    closeAddModal();
}
</script>
@endsection
