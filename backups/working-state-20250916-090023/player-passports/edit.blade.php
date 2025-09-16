@extends('layouts.app')

@section('title', 'Modifier le Passeport Joueur')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Modifier le Passeport</h1>
                    <p class="mt-1 text-sm text-gray-500">Passeport FIFA de {{ $playerPassport->player->first_name }} {{ $playerPassport->player->last_name }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('player-passports.show', $playerPassport) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('player-passports.update', $playerPassport) }}" enctype="multipart/form-data" class="space-y-8 p-6">
                @csrf
                @method('PUT')
                
                <!-- Player Information (Read-only) -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du Joueur</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 h-12 w-12">
                                @if($playerPassport->player->player_picture)
                                    <img class="h-12 w-12 rounded-full object-cover" src="{{ asset('storage/' . $playerPassport->player->player_picture) }}" alt="{{ $playerPassport->player->first_name }}">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($playerPassport->player->first_name, 0, 1) }}{{ substr($playerPassport->player->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">
                                    {{ $playerPassport->player->first_name }} {{ $playerPassport->player->last_name }}
                                </h4>
                                <p class="text-sm text-gray-500">{{ $playerPassport->player->position }} • {{ $playerPassport->player->nationality }}</p>
                                <p class="text-sm text-gray-500">{{ $playerPassport->player->club->name ?? 'Sans club' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Passport Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du Passeport</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="passport_number" class="block text-sm font-medium text-gray-700">Numéro de Passeport *</label>
                            <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number', $playerPassport->passport_number) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('passport_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="passport_type" class="block text-sm font-medium text-gray-700">Type de Passeport *</label>
                            <select name="passport_type" id="passport_type" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un type</option>
                                <option value="electronic" {{ old('passport_type', $playerPassport->passport_type) == 'electronic' ? 'selected' : '' }}>Électronique</option>
                                <option value="physical" {{ old('passport_type', $playerPassport->passport_type) == 'physical' ? 'selected' : '' }}>Physique</option>
                                <option value="temporary" {{ old('passport_type', $playerPassport->passport_type) == 'temporary' ? 'selected' : '' }}>Temporaire</option>
                            </select>
                            @error('passport_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Statut *</label>
                            <select name="status" id="status" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un statut</option>
                                <option value="active" {{ old('status', $playerPassport->status) == 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="pending_validation" {{ old('status', $playerPassport->status) == 'pending_validation' ? 'selected' : '' }}>En attente de validation</option>
                                <option value="suspended" {{ old('status', $playerPassport->status) == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                <option value="expired" {{ old('status', $playerPassport->status) == 'expired' ? 'selected' : '' }}>Expiré</option>
                                <option value="revoked" {{ old('status', $playerPassport->status) == 'revoked' ? 'selected' : '' }}>Révoqué</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="registration_number" class="block text-sm font-medium text-gray-700">Numéro d'Enregistrement</label>
                            <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number', $playerPassport->registration_number) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Numéro d'enregistrement FIFA">
                            @error('registration_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Dates -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dates</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="issue_date" class="block text-sm font-medium text-gray-700">Date d'Émission *</label>
                            <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', $playerPassport->issue_date->format('Y-m-d')) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('issue_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-700">Date d'Expiration *</label>
                            <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $playerPassport->expiry_date->format('Y-m-d')) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('expiry_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="renewal_date" class="block text-sm font-medium text-gray-700">Date de Renouvellement</label>
                            <input type="date" name="renewal_date" id="renewal_date" value="{{ old('renewal_date', $playerPassport->renewal_date ? $playerPassport->renewal_date->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('renewal_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Issuing Authority -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Autorité Émettrice</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="issuing_authority" class="block text-sm font-medium text-gray-700">Autorité Émettrice *</label>
                            <input type="text" name="issuing_authority" id="issuing_authority" value="{{ old('issuing_authority', $playerPassport->issuing_authority) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Ex: FIFA, Fédération Nationale">
                            @error('issuing_authority')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="issuing_country" class="block text-sm font-medium text-gray-700">Pays Émetteur *</label>
                            <input type="text" name="issuing_country" id="issuing_country" value="{{ old('issuing_country', $playerPassport->issuing_country) }}" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Ex: Suisse, France">
                            @error('issuing_country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- License Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de Licence</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="license_type" class="block text-sm font-medium text-gray-700">Type de Licence</label>
                            <select name="license_type" id="license_type"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un type</option>
                                <option value="professional" {{ old('license_type', $playerPassport->license_type) == 'professional' ? 'selected' : '' }}>Professionnel</option>
                                <option value="amateur" {{ old('license_type', $playerPassport->license_type) == 'amateur' ? 'selected' : '' }}>Amateur</option>
                                <option value="youth" {{ old('license_type', $playerPassport->license_type) == 'youth' ? 'selected' : '' }}>Jeune</option>
                                <option value="international" {{ old('license_type', $playerPassport->license_type) == 'international' ? 'selected' : '' }}>International</option>
                            </select>
                            @error('license_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="license_category" class="block text-sm font-medium text-gray-700">Catégorie de Licence</label>
                            <select name="license_category" id="license_category"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Sélectionner une catégorie</option>
                                <option value="A" {{ old('license_category', $playerPassport->license_category) == 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ old('license_category', $playerPassport->license_category) == 'B' ? 'selected' : '' }}>B</option>
                                <option value="C" {{ old('license_category', $playerPassport->license_category) == 'C' ? 'selected' : '' }}>C</option>
                                <option value="D" {{ old('license_category', $playerPassport->license_category) == 'D' ? 'selected' : '' }}>D</option>
                                <option value="E" {{ old('license_category', $playerPassport->license_category) == 'E' ? 'selected' : '' }}>E</option>
                            </select>
                            @error('license_category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="4"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Notes additionnelles sur le passeport...">{{ old('notes', $playerPassport->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('player-passports.show', $playerPassport) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Annuler
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 