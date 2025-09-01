@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📝 Modifier la Licence</h1>
                    <p class="mt-2 text-gray-600">
                        Licence #{{ $license->fifa_license_number ?? $license->id }} - 
                        {{ $license->applicant_name ?? 'Nom non défini' }}
                    </p>
                    @if($license->status === 'rejected' || $license->status === 'pending_correction')
                        <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Demande de correction de l'association
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Cette licence nécessite des modifications avant réapprobation.</p>
                                        @if($license->rejection_reason)
                                            <p class="mt-1"><strong>Raison :</strong> {{ $license->rejection_reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <a href="{{ route('licenses.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                    ← Retour aux Licences
                </a>
            </div>
        </div>

        <!-- Formulaire de modification -->
        <div class="bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('licenses.update', $license) }}" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Informations du demandeur -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">👤 Informations du Demandeur</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="applicant_name" class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
                            <input type="text" name="applicant_name" id="applicant_name" 
                                   value="{{ old('applicant_name', $license->applicant_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" id="email" 
                                   value="{{ old('email', $license->email) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                            <input type="tel" name="phone" id="phone" 
                                   value="{{ old('phone', $license->phone) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date de naissance</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" 
                                   value="{{ old('date_of_birth', $license->date_of_birth ? $license->date_of_birth->format('Y-m-d') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">Nationalité</label>
                            <input type="text" name="nationality" id="nationality" 
                                   value="{{ old('nationality', $license->nationality) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Poste</label>
                            <select name="position" id="position" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Sélectionnez un poste</option>
                                <option value="Attaquant" @if(old('position', $license->position) == 'Attaquant') selected @endif>Attaquant</option>
                                <option value="Milieu" @if(old('position', $license->position) == 'Milieu') selected @endif>Milieu</option>
                                <option value="Défenseur" @if(old('position', $license->position) == 'Défenseur') selected @endif>Défenseur</option>
                                <option value="Gardien" @if(old('position', $license->position) == 'Gardien') selected @endif>Gardien</option>
                                <option value="Entraîneur" @if(old('position', $license->position) == 'Entraîneur') selected @endif>Entraîneur</option>
                                <option value="Staff médical" @if(old('position', $license->position) == 'Staff médical') selected @endif>Staff médical</option>
                                <option value="Arbitre" @if(old('position', $license->position) == 'Arbitre') selected @endif>Arbitre</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Informations de la licence -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">🏆 Informations de la Licence</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="license_type" class="block text-sm font-medium text-gray-700 mb-2">Type de licence</label>
                            <select name="license_type" id="license_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Sélectionnez le type</option>
                                <option value="amateur" @if(old('license_type', $license->license_type) == 'amateur') selected @endif>Amateur</option>
                                <option value="semi_pro" @if(old('license_type', $license->license_type) == 'semi_pro') selected @endif>Semi-Professionnel</option>
                                <option value="professional" @if(old('license_type', $license->license_type) == 'professional') selected @endif>Professionnel</option>
                                <option value="international" @if(old('license_type', $license->license_type) == 'international') selected @endif>International</option>
                            </select>
                        </div>
                        <div>
                            <label for="validity_period" class="block text-sm font-medium text-gray-700 mb-2">Période de validité</label>
                            <select name="validity_period" id="validity_period" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Sélectionnez la période</option>
                                <option value="1_year" @if(old('validity_period', $license->validity_period) == '1_year') selected @endif>1 an</option>
                                <option value="2_years" @if(old('validity_period', $license->validity_period) == '2_years') selected @endif>2 ans</option>
                                <option value="3_years" @if(old('validity_period', $license->validity_period) == '3_years') selected @endif>3 ans</option>
                                <option value="5_years" @if(old('validity_period', $license->validity_period) == '5_years') selected @endif>5 ans</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="license_reason" class="block text-sm font-medium text-gray-700 mb-2">Raison de la demande</label>
                            <textarea name="license_reason" id="license_reason" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>{{ old('license_reason', $license->license_reason) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Upload de photo -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📷 Photo du Demandeur</h3>
                    
                    <!-- Photo actuelle -->
                    @if($license->player && $license->player->player_picture)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Photo actuelle</label>
                            <div class="flex items-center space-x-4">
                                <img src="{{ asset('storage/' . $license->player->player_picture) }}" 
                                     alt="Photo actuelle" 
                                     class="w-20 h-20 object-cover rounded-lg border-2 border-gray-200">
                                <div class="text-sm text-gray-600">
                                    <p>Photo actuellement enregistrée</p>
                                    <p class="text-xs text-gray-500">Taille : {{ Storage::disk('public')->size($license->player->player_picture) ?? 'N/A' }} bytes</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Upload de nouvelle photo -->
                    <div>
                        <label for="player_photo" class="block text-sm font-medium text-gray-700 mb-2">Nouvelle photo (optionnel)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="player_photo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Télécharger un fichier</span>
                                        <input id="player_photo" name="player_photo" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">ou glisser-déposer</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, JPEG jusqu'à 5MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('licenses.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        💾 Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Modifier la licence</h1>
    <form method="POST" action="{{ route('licenses.update', $license) }}" class="max-w-lg space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium">Nom</label>
            <input type="text" name="name" id="name" class="border border-gray-300 rounded px-3 py-2 w-full" value="{{ old('name', $license->name) }}" required>
        </div>
        <div>
            <label for="type" class="block text-sm font-medium">Type</label>
            <select name="type" id="type" class="border border-gray-300 rounded px-3 py-2 w-full" required>
                <option value="Joueur" @if(old('type', $license->type)=='Joueur') selected @endif>Joueur</option>
                <option value="Staff" @if(old('type', $license->type)=='Staff') selected @endif>Staff</option>
                <option value="Médical" @if(old('type', $license->type)=='Médical') selected @endif>Médical</option>
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium">Statut</label>
            <select name="status" id="status" class="border border-gray-300 rounded px-3 py-2 w-full" required>
                <option value="Active" @if(old('status', $license->status)=='Active') selected @endif>Active</option>
                <option value="Inactive" @if(old('status', $license->status)=='Inactive') selected @endif>Inactive</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Enregistrer</button>
        <a href="{{ route('licenses.index') }}" class="ml-4 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>
@endsection 