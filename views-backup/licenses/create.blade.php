@extends('layouts.app')

@section('title', 'Nouvelle Licence - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">📋 Nouvelle Licence</h1>
            <p class="text-gray-600 mt-2">Créer une nouvelle demande de licence</p>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Informations de la Licence</h2>
            </div>
            
            <form action="{{ route('licenses.store') }}" method="POST" class="p-6" enctype="multipart/form-data">
                @csrf
                
                <!-- License Type Selection -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Type de Licence</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors" onclick="selectLicenseType('player')">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <span class="text-blue-600 text-xl">⚽</span>
                                </div>
                                <h4 class="font-medium text-gray-900">Licence Joueur</h4>
                                <p class="text-sm text-gray-600">Pour les joueurs actifs</p>
                            </div>
                        </div>
                        <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-green-500 transition-colors" onclick="selectLicenseType('staff')">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <span class="text-green-600 text-xl">👥</span>
                                </div>
                                <h4 class="font-medium text-gray-900">Licence Staff</h4>
                                <p class="text-sm text-gray-600">Pour l'encadrement</p>
                            </div>
                        </div>
                        <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-purple-500 transition-colors" onclick="selectLicenseType('medical')">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <span class="text-purple-600 text-xl">🏥</span>
                                </div>
                                <h4 class="font-medium text-gray-900">Licence Médicale</h4>
                                <p class="text-sm text-gray-600">Pour le personnel médical</p>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="license_type" id="license_type" value="{{ old('license_type') }}" required>
                    @error('license_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @php
                    $user = Auth::user();
                    $isClubUser = in_array($user->role, ['club_admin', 'club_manager', 'club_medical']);
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="applicant_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom complet du demandeur *
                        </label>
                        <input type="text" name="applicant_name" id="applicant_name" 
                               value="{{ old('applicant_name') }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('applicant_name')
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
                            <option value="Française" {{ old('nationality') == 'Française' ? 'selected' : '' }}>Française</option>
                            <option value="Allemande" {{ old('nationality') == 'Allemande' ? 'selected' : '' }}>Allemande</option>
                            <option value="Italienne" {{ old('nationality') == 'Italienne' ? 'selected' : '' }}>Italienne</option>
                            <option value="Espagnole" {{ old('nationality') == 'Espagnole' ? 'selected' : '' }}>Espagnole</option>
                            <option value="Anglaise" {{ old('nationality') == 'Anglaise' ? 'selected' : '' }}>Anglaise</option>
                            <option value="Autre" {{ old('nationality') == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('nationality')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                            Position/Rôle *
                        </label>
                        <select name="position" id="position" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner une position</option>
                            <option value="Attaquant" {{ old('position') == 'Attaquant' ? 'selected' : '' }}>Attaquant</option>
                            <option value="Milieu" {{ old('position') == 'Milieu' ? 'selected' : '' }}>Milieu</option>
                            <option value="Défenseur" {{ old('position') == 'Défenseur' ? 'selected' : '' }}>Défenseur</option>
                            <option value="Gardien" {{ old('position') == 'Gardien' ? 'selected' : '' }}>Gardien</option>
                            <option value="Entraîneur" {{ old('position') == 'Entraîneur' ? 'selected' : '' }}>Entraîneur</option>
                            <option value="Médecin" {{ old('position') == 'Médecin' ? 'selected' : '' }}>Médecin</option>
                            <option value="Physiothérapeute" {{ old('position') == 'Physiothérapeute' ? 'selected' : '' }}>Physiothérapeute</option>
                            <option value="Staff" {{ old('position') == 'Staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                        @error('position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($isClubUser)
                        <input type="hidden" name="club_id" value="{{ $user->club_id }}">
                        <div class="col-span-2 mb-4">
                            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-2 rounded">
                                Cette demande de licence sera envoyée à l'association pour approbation.
                            </div>
                        </div>
                    @else
                    <div>
                        <label for="club_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Club *
                        </label>
                        <select name="club_id" id="club_id" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un club</option>
                            @foreach($clubs ?? [] as $club)
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

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" name="email" id="email" 
                               value="{{ old('email') }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone *
                        </label>
                        <input type="tel" name="phone" id="phone" 
                               value="{{ old('phone') }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="license_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Motif de la demande *
                        </label>
                        <textarea name="license_reason" id="license_reason" rows="3" required
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Expliquez le motif de votre demande de licence...">{{ old('license_reason') }}</textarea>
                        @error('license_reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="validity_period" class="block text-sm font-medium text-gray-700 mb-2">
                            Période de validité souhaitée *
                        </label>
                        <select name="validity_period" id="validity_period" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner une période</option>
                            <option value="1_year" {{ old('validity_period') == '1_year' ? 'selected' : '' }}>1 an</option>
                            <option value="2_years" {{ old('validity_period') == '2_years' ? 'selected' : '' }}>2 ans</option>
                            <option value="3_years" {{ old('validity_period') == '3_years' ? 'selected' : '' }}>3 ans</option>
                            <option value="5_years" {{ old('validity_period') == '5_years' ? 'selected' : '' }}>5 ans</option>
                        </select>
                        @error('validity_period')
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
                                Pièce d'identité (PDF, JPG, PNG) *
                            </label>
                            <input type="file" name="id_document" id="id_document" accept=".pdf,image/*" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('id_document')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="medical_certificate" class="block text-sm font-medium text-gray-700 mb-2">
                                Certificat médical (PDF, JPG, PNG) *
                            </label>
                            <input type="file" name="medical_certificate" id="medical_certificate" accept=".pdf,image/*" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('medical_certificate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="proof_of_age" class="block text-sm font-medium text-gray-700 mb-2">
                                Justificatif d'âge (PDF, JPG, PNG) *
                            </label>
                            <input type="file" name="proof_of_age" id="proof_of_age" accept=".pdf,image/*" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('proof_of_age')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="additional_documents" class="block text-sm font-medium text-gray-700 mb-2">
                                Documents supplémentaires (PDF, JPG, PNG)
                            </label>
                            <input type="file" name="additional_documents[]" id="additional_documents" accept=".pdf,image/*" multiple
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('additional_documents')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Process Flow Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Processus de validation</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">1</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Soumission par le club</h4>
                                <p class="text-sm text-gray-600">Le club soumet la demande de licence</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 mt-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">2</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Validation par l'association</h4>
                                <p class="text-sm text-gray-600">L'association examine et valide la demande</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 mt-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">3</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Retour au club</h4>
                                <p class="text-sm text-gray-600">La licence validée est retournée au club</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('licenses.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        Soumettre la demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function selectLicenseType(type) {
    // Remove active class from all license type cards
    document.querySelectorAll('[onclick^="selectLicenseType"]').forEach(card => {
        card.classList.remove('border-blue-500', 'border-green-500', 'border-purple-500');
        card.classList.add('border-gray-200');
    });
    
    // Add active class to selected card
    const selectedCard = event.currentTarget;
    if (type === 'player') {
        selectedCard.classList.remove('border-gray-200');
        selectedCard.classList.add('border-blue-500');
    } else if (type === 'staff') {
        selectedCard.classList.remove('border-gray-200');
        selectedCard.classList.add('border-green-500');
    } else if (type === 'medical') {
        selectedCard.classList.remove('border-gray-200');
        selectedCard.classList.add('border-purple-500');
    }
    
    // Set the hidden input value
    document.getElementById('license_type').value = type;
}
</script>
@endsection 