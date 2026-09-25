@extends('layouts.app')

@section('title', 'Nouveau Joueur - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            @if($existingPlayer)
                <h1 class="text-3xl font-bold text-gray-900">📋 Demande de Licence - {{ $existingPlayer->first_name }} {{ $existingPlayer->last_name }}</h1>
                <p class="text-gray-600 mt-2">
                    @if($isReadOnly)
                        Demande de licence pour un joueur déjà enregistré (lecture seule)
                    @else
                        Demande de licence pour un joueur existant
                    @endif
                </p>
            @else
                <h1 class="text-3xl font-bold text-gray-900">⚽ Nouveau Joueur</h1>
                <p class="text-gray-600 mt-2">Créer un nouveau joueur manuellement</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Informations du Joueur</h2>
            </div>
            
            <form action="{{ route('player-registration.store') }}" method="POST" class="p-6" enctype="multipart/form-data">
                @csrf
                
                @if($existingPlayer)
                    <input type="hidden" name="player_id" value="{{ $existingPlayer->id }}">
                @endif
                
                <!-- Player Picture Upload Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Photo du Joueur</h3>
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div id="imagePreviewContainer" class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center border-4 border-gray-200 overflow-hidden">
                                @if($existingPlayer && $existingPlayer->player_picture)
                                    <img src="{{ asset('storage/' . $existingPlayer->player_picture) }}" 
                                         alt="Photo {{ $existingPlayer->first_name }}" 
                                         class="h-full w-full object-cover">
                                @else
                                    <span class="text-gray-500 font-bold text-2xl">?</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <label for="player_picture" class="block text-sm font-medium text-gray-700 mb-2">
                                @if($existingPlayer && $existingPlayer->player_picture)
                                    Photo actuelle ({{ $existingPlayer->first_name }})
                                @else
                                    Ajouter une photo
                                @endif
                            </label>
                            <input type="file" name="player_picture" id="player_picture" accept="image/*"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   onchange="previewImage(this)">
                            <p class="mt-1 text-sm text-gray-500">
                                Formats acceptés: JPEG, PNG, JPG. Taille max: 5MB
                                @if($isReadOnly)
                                    <br><span class="text-blue-600">⚠️ Photo modifiable même en mode lecture seule</span>
                                @endif
                            </p>
                            @error('player_picture')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <!-- Bouton pour supprimer l'image -->
                            <button type="button" id="removeImageBtn" onclick="removeImage()" 
                                    class="mt-2 text-sm text-red-600 hover:text-red-800 hidden">
                                ❌ Supprimer l'image
                            </button>
                        </div>
                    </div>
                </div>
                
                @php
                    $user = Auth::user();
                    $isClubUser = in_array($user->role, ['club_admin', 'club_manager', 'club_medical']);
                @endphp
                
                <!-- Informations de base du joueur -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de base</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Prénom *
                            </label>
                            <input type="text" name="first_name" id="first_name" 
                                   value="{{ old('first_name', $existingPlayer->first_name ?? '') }}" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                   {{ $isReadOnly ? 'readonly' : '' }}>
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom *
                            </label>
                            <input type="text" name="last_name" id="last_name" 
                                   value="{{ old('last_name', $existingPlayer->last_name ?? '') }}" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                   {{ $isReadOnly ? 'readonly' : '' }}>
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                                Date de naissance *
                            </label>
                            <input type="date" name="date_of_birth" id="date_of_birth" 
                                   value="{{ old('date_of_birth', $existingPlayer->date_of_birth ? $existingPlayer->date_of_birth->format('Y-m-d') : '') }}" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                   {{ $isReadOnly ? 'readonly' : '' }}>
                            @error('date_of_birth')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">
                                Nationalité *
                            </label>
                            <select name="nationality" id="nationality" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                    {{ $isReadOnly ? 'disabled' : '' }}>
                                <option value="">Sélectionner une nationalité</option>
                                @foreach($nationalities as $nationality)
                                    <option value="{{ $nationality }}" {{ (old('nationality', $existingPlayer->nationality ?? '') == $nationality) ? 'selected' : '' }}>
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
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                    {{ $isReadOnly ? 'disabled' : '' }}>
                                <option value="">Sélectionner une position</option>
                                @foreach($positions as $key => $position)
                                    <option value="{{ $key }}" {{ (old('position', $existingPlayer->position ?? '') == $key) ? 'selected' : '' }}>
                                        {{ $position }}
                                    </option>
                                @endforeach
                            </select>
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                    </div>
                </div>

                <!-- Informations Administratives -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations Administratives</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Adresse complète *
                            </label>
                            <textarea name="address" id="address" rows="3" required
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                      {{ $isReadOnly ? 'readonly' : '' }}>{{ old('address', $existingPlayer->address ?? '') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Téléphone de contact
                            </label>
                            <input type="tel" name="contact_phone" id="contact_phone" 
                                   value="{{ old('contact_phone', $existingPlayer->contact_phone ?? '') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                   {{ $isReadOnly ? 'readonly' : '' }}>
                            @error('contact_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email de contact
                            </label>
                            <input type="email" name="contact_email" id="contact_email" 
                                   value="{{ old('contact_email', $existingPlayer->contact_email ?? '') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                   {{ $isReadOnly ? 'readonly' : '' }}>
                            @error('contact_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="legal_guardian" class="block text-sm font-medium text-gray-700 mb-2">
                                Tuteur légal (si mineur)
                            </label>
                            <input type="text" name="legal_guardian" id="legal_guardian" 
                                   value="{{ old('legal_guardian', $existingPlayer->legal_guardian ?? '') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                   {{ $isReadOnly ? 'readonly' : '' }}>
                            @error('legal_guardian')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="school_professional_status" class="block text-sm font-medium text-gray-700 mb-2">
                                Situation scolaire/professionnelle
                            </label>
                            <select name="school_professional_status" id="school_professional_status"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                    {{ $isReadOnly ? 'disabled' : '' }}>
                                <option value="">Sélectionner un statut</option>
                                <option value="student" {{ (old('school_professional_status', $existingPlayer->school_professional_status ?? '') == 'student') ? 'selected' : '' }}>Étudiant</option>
                                <option value="employed" {{ (old('school_professional_status', $existingPlayer->school_professional_status ?? '') == 'employed') ? 'selected' : '' }}>Employé</option>
                                <option value="unemployed" {{ (old('school_professional_status', $existingPlayer->school_professional_status ?? '') == 'unemployed') ? 'selected' : '' }}>Sans emploi</option>
                                <option value="retired" {{ (old('school_professional_status', $existingPlayer->school_professional_status ?? '') == 'retired') ? 'selected' : '' }}>Retraité</option>
                            </select>
                            @error('school_professional_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="parental_consent" class="block text-sm font-medium text-gray-700 mb-2">
                                Consentement parental (si mineur)
                            </label>
                            <select name="parental_consent" id="parental_consent"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                    {{ $isReadOnly ? 'disabled' : '' }}>
                                <option value="">Sélectionner un statut</option>
                                <option value="granted" {{ (old('parental_consent', $existingPlayer->parental_consent ?? '') == 'granted') ? 'selected' : '' }}>Accordé</option>
                                <option value="pending" {{ (old('parental_consent', $existingPlayer->parental_consent ?? '') == 'pending') ? 'selected' : '' }}>En attente</option>
                                <option value="not_required" {{ (old('parental_consent', $existingPlayer->parental_consent ?? '') == 'not_required') ? 'selected' : '' }}>Non requis</option>
                            </select>
                            @error('parental_consent')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Données Sportives -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Données Sportives</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="license_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Type de licence *
                            </label>
                            <select name="license_type" id="license_type" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                    {{ $isReadOnly ? 'disabled' : '' }}>
                                <option value="">Sélectionner un type</option>
                                @foreach($licenseTypes as $key => $type)
                                    <option value="{{ $key }}" {{ (old('license_type', $existingPlayer->license_type ?? '') == $key) ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('license_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="player_category" class="block text-sm font-medium text-gray-700 mb-2">
                                Catégorie (calculée automatiquement)
                            </label>
                            <input type="text" name="player_category" id="player_category" 
                                   value="{{ $existingPlayer && $existingPlayer->date_of_birth ? \Carbon\Carbon::parse($existingPlayer->date_of_birth)->age < 18 ? 'Jeunes' : 'Séniors' : 'N/A' }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-100"
                                   readonly>
                            <p class="mt-1 text-sm text-gray-500">Calculée automatiquement selon l'âge</p>
                        </div>

                        <div>
                            <label for="previous_clubs" class="block text-sm font-medium text-gray-700 mb-2">
                                Clubs précédents
                            </label>
                            <textarea name="previous_clubs" id="previous_clubs" rows="2"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                      {{ $isReadOnly ? 'readonly' : '' }}>{{ old('previous_clubs', $existingPlayer->previous_clubs ?? '') }}</textarea>
                            @error('previous_clubs')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="previous_license_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Numéro de licence antérieure
                            </label>
                            <input type="text" name="previous_license_number" id="previous_license_number" 
                                   value="{{ old('previous_license_number', $existingPlayer->previous_license_number ?? '') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                   {{ $isReadOnly ? 'readonly' : '' }}>
                            @error('previous_license_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informations de licence et club -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de licence et club</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                Club *
                            </label>
                            <select name="club_id" id="club_id" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                    {{ $isReadOnly ? 'disabled' : '' }}>
                                <option value="">Sélectionner un club</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" {{ (old('club_id', $existingPlayer->club_id ?? '') == $club->id) ? 'selected' : '' }}>
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
                                Association *
                            </label>
                            <select name="association_id" id="association_id" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $isReadOnly ? 'opacity-50 cursor-not-allowed bg-gray-100' : '' }}"
                                    {{ $isReadOnly ? 'disabled' : '' }}>
                                <option value="">Sélectionner une association</option>
                                @foreach($associations as $association)
                                    <option value="{{ $association->id }}" {{ (old('association_id', $existingPlayer->association_id ?? '') == $association->id) ? 'selected' : '' }}>
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
                            <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Numéro de licence
                            </label>
                            <input type="text" name="license_number" id="license_number" 
                                   value="{{ old('license_number') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Auto-généré si vide">
                            @error('license_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="license_status" class="block text-sm font-medium text-gray-700 mb-2">
                                Statut de la licence
                            </label>
                            <select name="license_status" id="license_status"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending" {{ old('license_status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="active" {{ old('license_status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ old('license_status') == 'suspended' ? 'selected' : '' }}>Suspendue</option>
                                <option value="expired" {{ old('license_status') == 'expired' ? 'selected' : '' }}>Expirée</option>
                            </select>
                            @error('license_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>



                <!-- Vérification PCMA -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vérification PCMA</h3>
                    @if(isset($pcmaStatus))
                        @if($pcmaStatus['status'] === 'valid')
                            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-green-800 font-medium">PCMA Validé ✓</span>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        Valide
                                    </span>
                                </div>
                                <p class="text-green-700 text-sm mt-2">
                                    {{ $pcmaStatus['message'] }}
                                </p>
                                <div class="mt-3 text-sm text-green-600">
                                    <strong>Date de validation:</strong> {{ $pcmaStatus['pcma']->signed_at ? \Carbon\Carbon::parse($pcmaStatus['pcma']->signed_at)->format('d/m/Y') : 'N/A' }}
                                </div>
                            </div>

                        @else
                            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <span class="text-yellow-800 font-medium">PCMA Requis</span>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                        Manquant
                                    </span>
                                </div>
                                <p class="text-red-700 text-sm mt-2">
                                    {{ $pcmaStatus['message'] }}
                                </p>
                                <p class="text-red-600 text-sm mt-2">
                                    <strong>Important:</strong> Un PCMA (Protocole de Concertation Médicale d'Aptitude) valide et signé avec statut "cleared" est obligatoire pour soumettre la demande de licence.
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('modules.index') }}" class="text-red-600 hover:text-red-800 text-sm font-medium underline">
                                        Accéder aux modules PCMA →
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-gray-800 font-medium">Vérification PCMA</span>
                                </div>
                                <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                    En cours
                                </span>
                            </div>
                            <p class="text-gray-600 text-sm mt-2">
                                Vérification du statut PCMA en cours...
                            </p>
                        </div>
                    @endif
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
                            <label for="pcma_validation" class="block text-sm font-medium text-gray-700 mb-2">
                                Validation PCMA *
                            </label>
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-blue-800 font-medium">PCMA requis pour la licence</span>
                                </div>
                                <p class="text-blue-700 text-sm mt-2">
                                    Un PCMA (Protocole de Concertation Médicale d'Aptitude) valide et signé est requis pour soumettre la demande de licence à l'association.
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('pcma.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium underline">
                                        Vérifier le statut PCMA →
                                    </a>
                                </div>
                            </div>
                            @error('pcma_validation')
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

                <!-- Boutons d'action -->
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('player-registration.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Annuler
                    </a>
                    
                    @if($existingPlayer)
                        <!-- Bouton Sauvegarder (pour les demandes existantes) -->
                        <button type="submit" name="action" value="save" 
                                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                            💾 Sauvegarder
                        </button>
                        
                        <!-- Bouton Envoyer demande -->
                        <button type="submit" name="action" value="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                            📤 Envoyer demande
                        </button>
                    @else
                        <!-- Bouton Créer pour les nouveaux joueurs -->
                        <button type="submit" name="action" value="create" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                            Créer le joueur
                        </button>
                    @endif
                </div>
                
                <!-- Informations sur les actions -->
                @if($existingPlayer)
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">📋 Actions disponibles :</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li><strong>💾 Sauvegarder :</strong> Enregistre les données de la demande dans l'état "En cours" (brouillon)</li>
                            <li><strong>📤 Envoyer demande :</strong> Passe la demande à l'état "Demande envoyée" et l'envoie à l'association pour validation</li>
                        </ul>
                        <p class="text-xs text-blue-600 mt-2">
                            <strong>Note :</strong> Une fois envoyée, la demande ne peut plus être modifiée par le club.
                        </p>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
                <script>
                function previewImage(input) {
                    const file = input.files[0];
                    const container = document.getElementById('imagePreviewContainer');
                    const removeBtn = document.getElementById('removeImageBtn');
                    
                    if (file) {
                        // Vérifier le type de fichier
                        if (!file.type.startsWith('image/')) {
                            alert('❌ Erreur : Le fichier doit être une image (JPG, PNG, JPEG)');
                            input.value = '';
                            return;
                        }
                        
                        // Vérifier la taille (5MB = 5 * 1024 * 1024 bytes)
                        if (file.size > 5 * 1024 * 1024) {
                            alert('❌ Erreur : La taille de l\'image ne doit pas dépasser 5MB');
                            input.value = '';
                            return;
                        }
                        
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            container.innerHTML = `<img src="${e.target.result}" alt="Aperçu" class="h-24 w-24 rounded-full object-cover border-4 border-gray-200">`;
                            removeBtn.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                }

                function removeImage() {
                    const input = document.getElementById('player_picture');
                    const container = document.getElementById('imagePreviewContainer');
                    const removeBtn = document.getElementById('removeImageBtn');
                    
                    input.value = '';
                    container.innerHTML = '<span class="text-gray-500 font-bold text-2xl">?</span>';
                    removeBtn.classList.add('hidden');
                }

                // Validation en temps réel
                document.getElementById('player_picture').addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const errorElement = this.parentNode.querySelector('.text-red-600');
                    
                    if (file) {
                        // Vérifier le type
                        if (!file.type.match('image.*')) {
                            if (errorElement) {
                                errorElement.textContent = '❌ Le fichier doit être une image (JPG, PNG, JPEG)';
                            } else {
                                const error = document.createElement('p');
                                error.className = 'mt-1 text-sm text-red-600';
                                error.textContent = '❌ Le fichier doit être une image (JPG, PNG, JPEG)';
                                this.parentNode.appendChild(error);
                            }
                            this.value = '';
                            return;
                        }
                        
                        // Vérifier la taille
                        if (file.size > 5 * 1024 * 1024) {
                            if (errorElement) {
                                errorElement.textContent = '❌ La taille de l\'image ne doit pas dépasser 5MB';
                            } else {
                                const error = document.createElement('p');
                                error.className = 'mt-1 text-sm text-red-600';
                                error.textContent = '❌ La taille de l\'image ne doit pas dépasser 5MB';
                                this.parentNode.appendChild(error);
                            }
                            this.value = '';
                            return;
                        }
                        
                        // Supprimer l'erreur si tout est OK
                        if (errorElement) {
                            errorElement.remove();
                        }
                    }
                });
                </script>
            </div>
        </div>
    </div>
@endsection 