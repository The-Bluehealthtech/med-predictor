@extends('layouts.app')

@section('title', 'Nouveau Joueur - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">⚽ Nouveau Joueur</h1>
            <p class="text-gray-600 mt-2">Créer un nouveau joueur manuellement</p>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Informations du Joueur</h2>
            </div>
            
            <form action="{{ route('player-registration.store') }}" method="POST" class="p-6" enctype="multipart/form-data">
                @csrf
                
                <!-- Player Picture Upload Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Photo du Joueur</h3>
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center border-4 border-gray-200">
                                <span class="text-gray-500 font-bold text-2xl">?</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <label for="player_picture" class="block text-sm font-medium text-gray-700 mb-2">
                                Ajouter une photo
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
                
                @php
                    $user = Auth::user();
                    $isClubUser = in_array($user->role, ['club_admin', 'club_manager', 'club_medical']);
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Prénom *
                        </label>
                        <input type="text" name="first_name" id="first_name" 
                               value="{{ old('first_name') }}" required
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
                               value="{{ old('last_name') }}" required
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
                               value="{{ old('date_of_birth') }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">
                            Nationalité *
                        </label>
                        <select name="nationality" id="nationality" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner une nationalité</option>
                            @foreach($nationalities as $nationality)
                                <option value="{{ $nationality }}" {{ old('nationality') == $nationality ? 'selected' : '' }}>
                                    {{ $nationality }}
                                </option>
                            @endforeach
                        </select>
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
                            @foreach($positions as $key => $position)
                                <option value="{{ $key }}" {{ old('position') == $key ? 'selected' : '' }}>
                                    {{ $position }}
                                </option>
                            @endforeach
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
                               value="{{ old('height') }}" min="150" max="220"
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
                               value="{{ old('weight') }}" min="40" max="120"
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
                               value="{{ old('overall_rating') }}" min="1" max="99"
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
                               value="{{ old('potential_rating') }}" min="1" max="99"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('potential_rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($isClubUser)
                        <input type="hidden" name="club_id" value="{{ $user->club_id }}">
                        <div class="col-span-2 mb-4">
                            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-2 rounded">
                                Cette inscription sera envoyée à l'association pour approbation.
                            </div>
                        </div>
                    @elseif($clubs->isNotEmpty())
                    <div>
                        <label for="club_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Club
                        </label>
                        <select name="club_id" id="club_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un club</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                                    {{ $club->name }} ({{ $club->city }}, {{ $club->country }})
                                </option>
                            @endforeach
                        </select>
                        @error('club_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    @if($teams->isNotEmpty())
                    <div>
                        <label for="team_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Équipe
                        </label>
                        <select name="team_id" id="team_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner une équipe</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('team_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    @if($associations->isNotEmpty())
                    <div>
                        <label for="association_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Association
                        </label>
                        <select name="association_id" id="association_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner une association</option>
                            @foreach($associations as $association)
                                <option value="{{ $association->id }}" {{ old('association_id') == $association->id ? 'selected' : '' }}>
                                    {{ $association->name }} ({{ $association->country }})
                                </option>
                            @endforeach
                        </select>
                        @error('association_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" name="email" id="email" 
                               value="{{ old('email') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone
                        </label>
                        <input type="tel" name="phone" id="phone" 
                               value="{{ old('phone') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Supporting Documents Upload Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Documents justificatifs</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="id_document" class="block text-sm font-medium text-gray-700 mb-2">
                                Pièce d'identité (PDF, JPG, PNG)
                            </label>
                            <input type="file" name="id_document" id="id_document" accept=".pdf,image/*"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('id_document')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="medical_certificate" class="block text-sm font-medium text-gray-700 mb-2">
                                Certificat médical (PDF, JPG, PNG)
                            </label>
                            <input type="file" name="medical_certificate" id="medical_certificate" accept=".pdf,image/*"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('medical_certificate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="proof_of_age" class="block text-sm font-medium text-gray-700 mb-2">
                                Justificatif d'âge (PDF, JPG, PNG)
                            </label>
                            <input type="file" name="proof_of_age" id="proof_of_age" accept=".pdf,image/*"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('proof_of_age')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('player-registration.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        Créer le joueur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 