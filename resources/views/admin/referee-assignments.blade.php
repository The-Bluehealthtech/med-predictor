@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Test - Gestion des Assignations d'Arbitres</h1>
        <p class="mt-2 text-gray-600">Page de test simplifiée</p>
        
        <div class="mt-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Matchs à assigner ({{ $matchesToAssign->count() }})</h2>
            @if($matchesToAssign->count() > 0)
                <ul class="space-y-2">
                    @foreach($matchesToAssign as $match)
                        <li class="p-4 bg-gray-50 rounded">
                            {{ $match->homeTeam->name ?? 'TBD' }} vs {{ $match->awayTeam->name ?? 'TBD' }}
                            ({{ $match->match_date ? $match->match_date->format('d/m/Y') : 'TBD' }})
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">Aucun match à assigner</p>
            @endif
        </div>
        
        <div class="mt-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Arbitres disponibles ({{ $referees->count() }})</h2>
            @if($referees->count() > 0)
                <ul class="space-y-2">
                    @foreach($referees as $referee)
                        <li class="p-4 bg-blue-50 rounded">
                            {{ $referee->name ?? 'Nom inconnu' }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">Aucun arbitre disponible</p>
            @endif
        </div>
    </div>
</div>
@endsection
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg mb-6">
            <div class="px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Gestion des Assignations d'Arbitres</h1>
                        <p class="text-blue-100 mt-2">Assigner des arbitres aux matchs de la Ligue 1 Tunisienne</p>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('test.referees') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white hover:bg-opacity-30 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Retour au Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matchs à Assigner -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Matchs de la Ligue 1 Tunisienne - À Assigner</h2>
                
                @if($matchesToAssign->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arbitres Assignés</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($matchesToAssign as $match)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $match->homeTeam->name ?? 'TBD' }} vs {{ $match->awayTeam->name ?? 'TBD' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $match->competition->name ?? 'Compétition' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->match_date ? $match->match_date->format('D, M j, Y g:i A') : 'TBD' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->venue ?? 'TBD' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $match->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($match->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst(str_replace('_', ' ', $match->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $assignedOfficials = $match->officials()->with('user')->get();
                                        @endphp
                                        @if($assignedOfficials->count() > 0)
                                            <div class="space-y-1">
                                                @foreach($assignedOfficials as $official)
                                                    <div class="text-xs">
                                                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $official->role)) }}:</span>
                                                        {{ $official->user->name ?? 'Non assigné' }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-red-500 text-xs">Aucun arbitre assigné</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="openAssignmentModal({{ $match->id }})" 
                                                class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-md text-xs">
                                            Assigner Arbitres
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun match à assigner</h3>
                        <p class="mt-1 text-sm text-gray-500">Tous les matchs ont déjà des arbitres assignés.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Matchs Assignés -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Matchs Assignés Récemment</h2>
                
                @if($recentlyAssigned->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arbitre Principal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assistants</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentlyAssigned as $match)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $match->homeTeam->club->name ?? 'TBD' }} vs {{ $match->awayTeam->club->name ?? 'TBD' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->match_date ? $match->match_date->format('D, M j, Y g:i A') : 'TBD' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $mainReferee = $match->officials()->where('role', 'main_referee')->with('user')->first();
                                        @endphp
                                        {{ $mainReferee->user->name ?? 'Non assigné' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $assistants = $match->officials()->whereIn('role', ['assistant_referee_1', 'assistant_referee_2'])->with('user')->get();
                                        @endphp
                                        @foreach($assistants as $assistant)
                                            <div class="text-xs">{{ $assistant->user->name ?? 'Non assigné' }}</div>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="openAssignmentModal({{ $match->id }})" 
                                                class="text-indigo-600 hover:text-indigo-900 bg-indigo-100 hover:bg-indigo-200 px-3 py-1 rounded-md text-xs">
                                            Modifier
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun match assigné récemment</h3>
                        <p class="mt-1 text-sm text-gray-500">Les matchs assignés apparaîtront ici.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal d'Assignation -->
<div id="assignmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Assigner des Arbitres</h3>
                <button onclick="closeAssignmentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="assignmentForm" method="POST" action="{{ route('admin.assign-referees') }}">
                @csrf
                <input type="hidden" id="matchId" name="match_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Match Sélectionné</label>
                    <div id="matchInfo" class="bg-gray-50 p-3 rounded-md text-sm"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="main_referee" class="block text-sm font-medium text-gray-700">Arbitre Principal *</label>
                        <select id="main_referee" name="main_referee" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un arbitre principal</option>
                            @foreach($referees as $referee)
                                <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="assistant_referee_1" class="block text-sm font-medium text-gray-700">Assistant Arbitre 1 *</label>
                        <select id="assistant_referee_1" name="assistant_referee_1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un assistant</option>
                            @foreach($referees as $referee)
                                <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="assistant_referee_2" class="block text-sm font-medium text-gray-700">Assistant Arbitre 2 *</label>
                        <select id="assistant_referee_2" name="assistant_referee_2" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un assistant</option>
                            @foreach($referees as $referee)
                                <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="fourth_official" class="block text-sm font-medium text-gray-700">4ème Arbitre</label>
                        <select id="fourth_official" name="fourth_official" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un 4ème arbitre</option>
                            @foreach($referees as $referee)
                                <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAssignmentModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Assigner les Arbitres
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAssignmentModal(matchId) {
    // Récupérer les informations du match via AJAX
    fetch(`/admin/match-info/${matchId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('matchId').value = matchId;
            document.getElementById('matchInfo').innerHTML = `
                <strong>${data.home_team} vs ${data.away_team}</strong><br>
                <span class="text-gray-600">${data.date} - ${data.venue}</span>
            `;
            document.getElementById('assignmentModal').classList.remove('hidden');
        });
}

function closeAssignmentModal() {
    document.getElementById('assignmentModal').classList.add('hidden');
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('assignmentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAssignmentModal();
    }
});
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg mb-6">
            <div class="px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Gestion des Assignations d'Arbitres</h1>
                        <p class="text-blue-100 mt-2">Assigner des arbitres aux matchs de la Ligue 1 Tunisienne</p>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white hover:bg-opacity-30 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Retour au Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matchs à Assigner -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Matchs de la Ligue 1 Tunisienne - À Assigner</h2>
                
                @if($matchesToAssign->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arbitres Assignés</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($matchesToAssign as $match)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $match->homeTeam->name ?? 'TBD' }} vs {{ $match->awayTeam->name ?? 'TBD' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $match->competition->name ?? 'Compétition' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->match_date ? $match->match_date->format('D, M j, Y g:i A') : 'TBD' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->venue ?? 'TBD' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $match->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($match->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst(str_replace('_', ' ', $match->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $assignedOfficials = $match->officials()->with('user')->get();
                                        @endphp
                                        @if($assignedOfficials->count() > 0)
                                            <div class="space-y-1">
                                                @foreach($assignedOfficials as $official)
                                                    <div class="text-xs">
                                                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $official->role)) }}:</span>
                                                        {{ $official->user->name ?? 'Non assigné' }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-red-500 text-xs">Aucun arbitre assigné</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="openAssignmentModal({{ $match->id }})" 
                                                class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-md text-xs">
                                            Assigner Arbitres
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun match à assigner</h3>
                        <p class="mt-1 text-sm text-gray-500">Tous les matchs ont déjà des arbitres assignés.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Matchs Assignés -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Matchs Assignés Récemment</h2>
                
                @if($recentlyAssigned->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arbitre Principal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assistants</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentlyAssigned as $match)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $match->homeTeam->club->name ?? 'TBD' }} vs {{ $match->awayTeam->club->name ?? 'TBD' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->match_date ? $match->match_date->format('D, M j, Y g:i A') : 'TBD' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $mainReferee = $match->officials()->where('role', 'main_referee')->with('user')->first();
                                        @endphp
                                        {{ $mainReferee->user->name ?? 'Non assigné' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $assistants = $match->officials()->whereIn('role', ['assistant_referee_1', 'assistant_referee_2'])->with('user')->get();
                                        @endphp
                                        @foreach($assistants as $assistant)
                                            <div class="text-xs">{{ $assistant->user->name ?? 'Non assigné' }}</div>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="openAssignmentModal({{ $match->id }})" 
                                                class="text-indigo-600 hover:text-indigo-900 bg-indigo-100 hover:bg-indigo-200 px-3 py-1 rounded-md text-xs">
                                            Modifier
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun match assigné récemment</h3>
                        <p class="mt-1 text-sm text-gray-500">Les matchs assignés apparaîtront ici.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal d'Assignation -->
<div id="assignmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Assigner des Arbitres</h3>
                <button onclick="closeAssignmentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="assignmentForm" method="POST" action="{{ route('admin.assign-referees') }}">
                @csrf
                <input type="hidden" id="matchId" name="match_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Match Sélectionné</label>
                    <div id="matchInfo" class="bg-gray-50 p-3 rounded-md text-sm"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="main_referee" class="block text-sm font-medium text-gray-700">Arbitre Principal *</label>
                        <select id="main_referee" name="main_referee" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un arbitre principal</option>
                            @foreach($referees as $referee)
                                <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="assistant_referee_1" class="block text-sm font-medium text-gray-700">Assistant Arbitre 1 *</label>
                        <select id="assistant_referee_1" name="assistant_referee_1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un assistant</option>
                            @foreach($referees as $referee)
                                <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="assistant_referee_2" class="block text-sm font-medium text-gray-700">Assistant Arbitre 2 *</label>
                        <select id="assistant_referee_2" name="assistant_referee_2" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un assistant</option>
                            @foreach($referees as $referee)
                                <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="fourth_official" class="block text-sm font-medium text-gray-700">4ème Arbitre</label>
                        <select id="fourth_official" name="fourth_official" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un 4ème arbitre</option>
                            @foreach($referees as $referee)
                                <option value="{{ $referee->id }}">{{ $referee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAssignmentModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Assigner les Arbitres
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAssignmentModal(matchId) {
    // Récupérer les informations du match via AJAX
    fetch(`/admin/match-info/${matchId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('matchId').value = matchId;
            document.getElementById('matchInfo').innerHTML = `
                <strong>${data.home_team} vs ${data.away_team}</strong><br>
                <span class="text-gray-600">${data.date} - ${data.venue}</span>
            `;
            document.getElementById('assignmentModal').classList.remove('hidden');
        });
}

function closeAssignmentModal() {
    document.getElementById('assignmentModal').classList.add('hidden');
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('assignmentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAssignmentModal();
    }
});
</script>
@endsection

