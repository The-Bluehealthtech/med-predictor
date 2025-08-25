@extends('layouts.app')

@section('title', 'Modifier ' . $player->full_name . ' - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">⚽ Modifier {{ $player->full_name }}</h1>
            <p class="text-gray-600 mt-2">Modifier les informations du joueur</p>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Informations du Joueur</h2>
            </div>
            
            <form action="{{ route('player-registration.update', $player) }}" method="POST" class="p-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Player Picture Upload Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Photo du Joueur</h3>
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            @if($player->has_picture)
                                <img class="h-24 w-24 rounded-full border-4 border-gray-200 object-cover" src="{{ $player->player_picture_url }}" alt="{{ $player->full_name }}">
                            @else
                                <div class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center border-4 border-gray-200">
                                    <span class="text-gray-500 font-bold text-2xl">
                                        {{ substr($player->first_name, 0, 1) . substr($player->last_name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label for="player_picture" class="block text-sm font-medium text-gray-700 mb-2">
                                Changer la photo
                            </label>
                            <input type="file" name="player_picture" id="player_picture" accept="image/*"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Formats acceptés: JPEG, PNG, JPG, GIF. Taille max: 2MB</p>
                            @error('player_picture')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Prénom *
                        </label>
                        <input type="text" name="first_name" id="first_name" 
                               value="{{ old('first_name', $player->first_name) }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom *
                        </label>
                        <input type="text" name="last_name" id="last_name" 
                               value="{{ old('last_name', $player->last_name) }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de naissance *
                        </label>
                        <input type="date" name="date_of_birth" id="date_of_birth" 
                               value="{{ old('date_of_birth', $player->date_of_birth ? $player->date_of_birth->format('Y-m-d') : '') }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">
                            Nationalité *
                        </label>
                        <input type="text" name="nationality" id="nationality" 
                               value="{{ old('nationality', $player->nationality) }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('nationality')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                            Position *
                        </label>
                        <select name="position" id="position" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner une position</option>
                            <option value="ST" {{ old('position', $player->position) == 'ST' ? 'selected' : '' }}>Attaquant (ST)</option>
                            <option value="RW" {{ old('position', $player->position) == 'RW' ? 'selected' : '' }}>Ailier droit (RW)</option>
                            <option value="LW" {{ old('position', $player->position) == 'LW' ? 'selected' : '' }}>Ailier gauche (LW)</option>
                            <option value="CAM" {{ old('position', $player->position) == 'CAM' ? 'selected' : '' }}>Milieu offensif (CAM)</option>
                            <option value="CM" {{ old('position', $player->position) == 'CM' ? 'selected' : '' }}>Milieu central (CM)</option>
                            <option value="CDM" {{ old('position', $player->position) == 'CDM' ? 'selected' : '' }}>Milieu défensif (CDM)</option>
                            <option value="CB" {{ old('position', $player->position) == 'CB' ? 'selected' : '' }}>Défenseur central (CB)</option>
                            <option value="RB" {{ old('position', $player->position) == 'RB' ? 'selected' : '' }}>Arrière droit (RB)</option>
                            <option value="LB" {{ old('position', $player->position) == 'LB' ? 'selected' : '' }}>Arrière gauche (LB)</option>
                            <option value="GK" {{ old('position', $player->position) == 'GK' ? 'selected' : '' }}>Gardien (GK)</option>
                        </select>
                        @error('position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 mb-2">
                            Taille (cm)
                        </label>
                        <input type="number" name="height" id="height" 
                               value="{{ old('height', $player->height) }}" min="150" max="220"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('height')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                            Poids (kg)
                        </label>
                        <input type="number" name="weight" id="weight" 
                               value="{{ old('weight', $player->weight) }}" min="40" max="120"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('weight')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="overall_rating" class="block text-sm font-medium text-gray-700 mb-2">
                            Note globale
                        </label>
                        <input type="number" name="overall_rating" id="overall_rating" 
                               value="{{ old('overall_rating', $player->overall_rating) }}" min="1" max="99"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('overall_rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="potential_rating" class="block text-sm font-medium text-gray-700 mb-2">
                            Note potentielle
                        </label>
                        <input type="number" name="potential_rating" id="potential_rating" 
                               value="{{ old('potential_rating', $player->potential_rating) }}" min="1" max="99"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('potential_rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="preferred_foot" class="block text-sm font-medium text-gray-700 mb-2">
                            Pied préféré
                        </label>
                        <select name="preferred_foot" id="preferred_foot"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner</option>
                            <option value="Right" {{ old('preferred_foot', $player->preferred_foot) == 'Right' ? 'selected' : '' }}>Droit</option>
                            <option value="Left" {{ old('preferred_foot', $player->preferred_foot) == 'Left' ? 'selected' : '' }}>Gauche</option>
                        </select>
                        @error('preferred_foot')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="weak_foot" class="block text-sm font-medium text-gray-700 mb-2">
                            Pied faible (1-5)
                        </label>
                        <input type="number" name="weak_foot" id="weak_foot" 
                               value="{{ old('weak_foot', $player->weak_foot) }}" min="1" max="5"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('weak_foot')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="skill_moves" class="block text-sm font-medium text-gray-700 mb-2">
                            Gesticulations (1-5)
                        </label>
                        <input type="number" name="skill_moves" id="skill_moves" 
                               value="{{ old('skill_moves', $player->skill_moves) }}" min="1" max="5"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('skill_moves')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="international_reputation" class="block text-sm font-medium text-gray-700 mb-2">
                            Réputation internationale (1-5)
                        </label>
                        <input type="number" name="international_reputation" id="international_reputation" 
                               value="{{ old('international_reputation', $player->international_reputation) }}" min="1" max="5"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('international_reputation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="value_eur" class="block text-sm font-medium text-gray-700 mb-2">
                            Valeur marchande (€)
                        </label>
                        <input type="number" name="value_eur" id="value_eur" 
                               value="{{ old('value_eur', $player->value_eur) }}" min="0"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('value_eur')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="wage_eur" class="block text-sm font-medium text-gray-700 mb-2">
                            Salaire hebdomadaire (€)
                        </label>
                        <input type="number" name="wage_eur" id="wage_eur" 
                               value="{{ old('wage_eur', $player->wage_eur) }}" min="0"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('wage_eur')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                                    <a href="{{ route('player-registration.show', $player) }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 