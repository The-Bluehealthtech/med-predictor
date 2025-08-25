<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inscrire une équipe à la compétition : ') }} {{ $competition->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Équipes déjà inscrites -->
            @if($registeredTeams->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Équipes déjà inscrites') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($registeredTeams as $team)
                                <div class="border rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900">{{ $team->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ __('Joueurs') }}: {{ $team->players->count() }}</p>
                                    <div class="mt-2">
                                        @foreach($team->players->take(3) as $teamPlayer)
                                            <span class="inline-block bg-gray-100 rounded-full px-2 py-1 text-xs text-gray-700 mr-1 mb-1">
                                                {{ $teamPlayer->player->first_name }} {{ $teamPlayer->player->last_name }}
                                            </span>
                                        @endforeach
                                        @if($team->players->count() > 3)
                                            <span class="text-xs text-gray-500">+{{ $team->players->count() - 3 }} autres</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulaire d'inscription -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Inscrire une nouvelle équipe') }}</h3>
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('competition-management.competitions.register-team', $competition) }}">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="team_name" :value="__('Nom de l\'équipe')" />
                            <x-text-input id="team_name" class="block mt-1 w-full" type="text" name="team_name" required autofocus />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="players" :value="__('Sélectionner les joueurs')" />
                            <select id="players" name="players[]" multiple class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach($players as $player)
                                    <option value="{{ $player->id }}">
                                        {{ $player->first_name }} {{ $player->last_name }} 
                                        @if($player->fifa_id)
                                            (FIFA: {{ $player->fifa_id }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs joueurs') }}</p>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Inscrire l\'équipe') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 