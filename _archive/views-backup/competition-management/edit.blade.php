<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Competition') }}: {{ $competition->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('competition-management.competitions.update', $competition) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informations de base -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nom de la compétition')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $competition->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="short_name" :value="__('Nom court')" />
                                <x-text-input id="short_name" name="short_name" type="text" class="mt-1 block w-full" :value="old('short_name', $competition->short_name)" placeholder="Ex: Ligue 1" />
                                <x-input-error :messages="$errors->get('short_name')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Type et format -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="type" :value="__('Type de compétition')" />
                                <select name="type" id="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">{{ __('Sélectionner un type') }}</option>
                                    <option value="league" {{ old('type', $competition->type) === 'league' ? 'selected' : '' }}>{{ __('Championnat') }}</option>
                                    <option value="cup" {{ old('type', $competition->type) === 'cup' ? 'selected' : '' }}>{{ __('Coupe') }}</option>
                                    <option value="friendly" {{ old('type', $competition->type) === 'friendly' ? 'selected' : '' }}>{{ __('Match amical') }}</option>
                                    <option value="international" {{ old('type', $competition->type) === 'international' ? 'selected' : '' }}>{{ __('Compétition internationale') }}</option>
                                    <option value="tournament" {{ old('type', $competition->type) === 'tournament' ? 'selected' : '' }}>{{ __('Tournoi') }}</option>
                                    <option value="playoff" {{ old('type', $competition->type) === 'playoff' ? 'selected' : '' }}>{{ __('Playoff') }}</option>
                                    <option value="exhibition" {{ old('type', $competition->type) === 'exhibition' ? 'selected' : '' }}>{{ __('Match d\'exhibition') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="format" :value="__('Format de compétition')" />
                                <select name="format" id="format" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">{{ __('Sélectionner un format') }}</option>
                                    <option value="round_robin" {{ old('format', $competition->format) === 'round_robin' ? 'selected' : '' }}>{{ __('Aller-retour') }}</option>
                                    <option value="knockout" {{ old('format', $competition->format) === 'knockout' ? 'selected' : '' }}>{{ __('Élimination directe') }}</option>
                                    <option value="mixed" {{ old('format', $competition->format) === 'mixed' ? 'selected' : '' }}>{{ __('Mixte') }}</option>
                                    <option value="single_round" {{ old('format', $competition->format) === 'single_round' ? 'selected' : '' }}>{{ __('Match unique') }}</option>
                                    <option value="group_stage" {{ old('format', $competition->format) === 'group_stage' ? 'selected' : '' }}>{{ __('Phase de groupes + élimination') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('format')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Saison et dates -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="season" :value="__('Saison')" />
                                <x-text-input id="season" name="season" type="text" class="mt-1 block w-full" :value="old('season', $competition->season)" required placeholder="2024-2025" />
                                <x-input-error :messages="$errors->get('season')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="start_date" :value="__('Date de début')" />
                                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', $competition->start_date?->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="end_date" :value="__('Date de fin')" />
                                <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date', $competition->end_date?->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Limites d'équipes -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="min_teams" :value="__('Nombre minimum d\'équipes')" />
                                <x-text-input id="min_teams" name="min_teams" type="number" class="mt-1 block w-full" :value="old('min_teams', $competition->min_teams)" min="2" placeholder="4" />
                                <x-input-error :messages="$errors->get('min_teams')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="max_teams" :value="__('Nombre maximum d\'équipes')" />
                                <x-text-input id="max_teams" name="max_teams" type="number" class="mt-1 block w-full" :value="old('max_teams', $competition->max_teams)" min="2" placeholder="20" />
                                <x-input-error :messages="$errors->get('max_teams')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Dates importantes -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="registration_deadline" :value="__('Date limite d\'inscription')" />
                                <x-text-input id="registration_deadline" name="registration_deadline" type="date" class="mt-1 block w-full" :value="old('registration_deadline', $competition->registration_deadline?->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('registration_deadline')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="status" :value="__('Statut')" />
                                <select name="status" id="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="upcoming" {{ old('status', $competition->status) === 'upcoming' ? 'selected' : '' }}>{{ __('À venir') }}</option>
                                    <option value="active" {{ old('status', $competition->status) === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="completed" {{ old('status', $competition->status) === 'completed' ? 'selected' : '' }}>{{ __('Terminée') }}</option>
                                    <option value="cancelled" {{ old('status', $competition->status) === 'cancelled' ? 'selected' : '' }}>{{ __('Annulée') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Informations financières -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="entry_fee" :value="__('Frais d\'inscription (€)')" />
                                <x-text-input id="entry_fee" name="entry_fee" type="number" step="0.01" class="mt-1 block w-full" :value="old('entry_fee', $competition->entry_fee)" placeholder="0.00" />
                                <x-input-error :messages="$errors->get('entry_fee')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="prize_pool" :value="__('Dotation (€)')" />
                                <x-text-input id="prize_pool" name="prize_pool" type="number" step="0.01" class="mt-1 block w-full" :value="old('prize_pool', $competition->prize_pool)" placeholder="0.00" />
                                <x-input-error :messages="$errors->get('prize_pool')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Description et règles -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Description de la compétition...">{{ old('description', $competition->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="rules" :value="__('Règlement')" />
                                <textarea id="rules" name="rules" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Règlement spécifique...">{{ old('rules', $competition->rules) }}</textarea>
                                <x-input-error :messages="$errors->get('rules')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Options spéciales -->
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="require_federation_license" id="require_federation_license" value="1" 
                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                       {{ old('require_federation_license', $competition->require_federation_license) ? 'checked' : '' }}>
                                <x-input-label for="require_federation_license" :value="__('Licence de la fédération obligatoire pour participer')" class="ml-2" />
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="fifa_sync_enabled" id="fifa_sync_enabled" value="1" 
                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                       {{ old('fifa_sync_enabled', $competition->fifaConnectId) ? 'checked' : '' }}>
                                <x-input-label for="fifa_sync_enabled" :value="__('Synchroniser avec FIFA Connect')" class="ml-2" />
                            </div>
                        </div>

                        <!-- Équipes inscrites -->
                        @if($clubs->count() > 0)
                        <div>
                            <x-input-label :value="__('Équipes inscrites')" />
                            <div class="mt-2 space-y-2">
                                @foreach($clubs as $club)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="clubs[]" id="club_{{ $club->id }}" value="{{ $club->id }}" 
                                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                               {{ in_array($club->id, old('clubs', $competition->clubs->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <x-input-label for="club_{{ $club->id }}" :value="$club->name" class="ml-2" />
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('clubs')" class="mt-2" />
                        </div>
                        @endif

                        <!-- Boutons d'action -->
                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <a href="{{ route('competition-management.competitions.show', $competition) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                {{ __('Annuler') }}
                            </a>
                            <x-primary-button>
                                {{ __('Mettre à jour la compétition') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 