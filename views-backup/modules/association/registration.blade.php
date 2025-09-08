@extends('layouts.app')

@section('title', 'Association Registration - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-green-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">🏛️</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Association Registration
                                </h1>
                                <p class="text-sm text-gray-600">Enregistrement avec détection de fraude GPT-4</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('association.dashboard') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour au Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Registration Form -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">📝 Formulaire d'Enregistrement</h2>
                <p class="text-sm text-gray-600 mt-1">Remplissez les informations de l'association</p>
            </div>
            
            <form id="associationRegistrationForm" action="{{ route('association.registration.store') }}" method="POST" class="p-6" enctype="multipart/form-data">
                @csrf
                
                <!-- Association Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="association_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom de l'Association *
                        </label>
                        <input type="text" name="association_name" id="association_name" 
                               value="{{ old('association_name') }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('association_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="association_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type d'Association *
                        </label>
                        <select name="association_type" id="association_type" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un type</option>
                            <option value="National Association" {{ old('association_type') == 'National Association' ? 'selected' : '' }}>Association Nationale</option>
                            <option value="Regional Association" {{ old('association_type') == 'Regional Association' ? 'selected' : '' }}>Association Régionale</option>
                            <option value="League Administrator" {{ old('association_type') == 'League Administrator' ? 'selected' : '' }}>Administrateur de Ligue</option>
                            <option value="Refereeing Body" {{ old('association_type') == 'Refereeing Body' ? 'selected' : '' }}>Organisme d'Arbitrage</option>
                        </select>
                        @error('association_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email de Contact *
                        </label>
                        <input type="email" name="contact_email" id="contact_email" 
                               value="{{ old('contact_email') }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('contact_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone de Contact *
                        </label>
                        <input type="tel" name="contact_phone" id="contact_phone" 
                               value="{{ old('contact_phone') }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse *
                        </label>
                        <textarea name="address" id="address" rows="3" required
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                            Pays *
                        </label>
                        <select name="country" id="country" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un pays</option>
                            <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>France</option>
                            <option value="Tunisia" {{ old('country') == 'Tunisia' ? 'selected' : '' }}>Tunisie</option>
                            <option value="Morocco" {{ old('country') == 'Morocco' ? 'selected' : '' }}>Maroc</option>
                            <option value="Algeria" {{ old('country') == 'Algeria' ? 'selected' : '' }}>Algérie</option>
                            <option value="Senegal" {{ old('country') == 'Senegal' ? 'selected' : '' }}>Sénégal</option>
                            <option value="Ivory Coast" {{ old('country') == 'Ivory Coast' ? 'selected' : '' }}>Côte d'Ivoire</option>
                            <option value="Cameroon" {{ old('country') == 'Cameroon' ? 'selected' : '' }}>Cameroun</option>
                            <option value="Nigeria" {{ old('country') == 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                            <option value="Ghana" {{ old('country') == 'Ghana' ? 'selected' : '' }}>Ghana</option>
                            <option value="Egypt" {{ old('country') == 'Egypt' ? 'selected' : '' }}>Égypte</option>
                        </select>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Logo Upload -->
                <div class="mb-6">
                    <label for="association_logo" class="block text-sm font-medium text-gray-700 mb-2">
                        Logo de l'Association
                    </label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-20 w-20 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                <span class="text-gray-500 font-bold text-xl">🏛️</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="association_logo" id="association_logo" accept="image/*"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Formats acceptés: JPEG, PNG, JPG. Taille max: 2MB</p>
                            @error('association_logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description de l'Association
                    </label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fraud Detection Section -->
                <div class="border-t border-gray-200 pt-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">🛡️ Détection de Fraude GPT-4</h3>
                        <div class="flex items-center">
                            <button type="button" id="fraudDetectionBtn" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                🔍 Activer la Détection
                            </button>
                        </div>
                    </div>
                    
                    <div id="fraudDetectionStatus" class="hidden">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-yellow-600"></div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-800">
                                        Analyse en cours avec GPT-4...
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="fraudDetectionResults" class="hidden">
                        <div class="bg-white border border-gray-200 rounded-md p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Résultats de l'Analyse GPT-4</h4>
                            <div id="fraudAnalysisContent" class="text-sm text-gray-700"></div>
                        </div>
                    </div>

                    <div id="fraudDetectionError" class="hidden">
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <span class="text-red-600">⚠️</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">
                                        Erreur lors de l'analyse GPT-4
                                    </p>
                                    <p id="fraudErrorDetails" class="text-sm text-red-700 mt-1"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-4">
                        <button type="submit" id="submitBtn"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium transition-colors">
                            ✅ Enregistrer l'Association
                        </button>
                        <button type="button" onclick="window.history.back()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md text-sm font-medium transition-colors">
                            Annuler
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Validation automatique
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            GPT-4 Intégré
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fraudDetectionBtn = document.getElementById('fraudDetectionBtn');
    const fraudDetectionStatus = document.getElementById('fraudDetectionStatus');
    const fraudDetectionResults = document.getElementById('fraudDetectionResults');
    const fraudDetectionError = document.getElementById('fraudDetectionError');
    const fraudAnalysisContent = document.getElementById('fraudAnalysisContent');
    const fraudErrorDetails = document.getElementById('fraudErrorDetails');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('associationRegistrationForm');

    let fraudAnalysisCompleted = false;

    fraudDetectionBtn.addEventListener('click', async function() {
        // Get form data
        const formData = new FormData(form);
        const associationData = {
            association_name: formData.get('association_name'),
            association_type: formData.get('association_type'),
            contact_email: formData.get('contact_email'),
            contact_phone: formData.get('contact_phone'),
            address: formData.get('address'),
            country: formData.get('country'),
            description: formData.get('description')
        };

        // Show loading state
        fraudDetectionStatus.classList.remove('hidden');
        fraudDetectionResults.classList.add('hidden');
        fraudDetectionError.classList.add('hidden');
        fraudDetectionBtn.disabled = true;
        fraudDetectionBtn.textContent = '🔍 Analyse en cours...';

        try {
            // Call GPT-4 fraud detection API
            const response = await fetch('/api/v1/association/fraud-detection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(associationData)
            });

            const result = await response.json();

            if (response.ok) {
                // Show results
                fraudAnalysisContent.innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Score de Risque:</span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${result.risk_score < 30 ? 'bg-green-100 text-green-800' : result.risk_score < 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">
                                ${result.risk_score}%
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Statut:</span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${result.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${result.status === 'approved' ? '✅ Approuvé' : '❌ Rejeté'}
                            </span>
                        </div>
                        <div class="mt-3">
                            <span class="font-medium">Analyse GPT-4:</span>
                            <p class="mt-1 text-gray-600">${result.analysis}</p>
                        </div>
                        ${result.recommendations ? `
                        <div class="mt-3">
                            <span class="font-medium">Recommandations:</span>
                            <p class="mt-1 text-gray-600">${result.recommendations}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
                
                fraudDetectionResults.classList.remove('hidden');
                fraudAnalysisCompleted = true;
                
                // Update button
                fraudDetectionBtn.textContent = '🔄 Réanalyser';
                fraudDetectionBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                fraudDetectionBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                
            } else {
                throw new Error(result.message || 'Erreur lors de l\'analyse');
            }

        } catch (error) {
            console.error('Fraud detection error:', error);
            fraudErrorDetails.textContent = error.message;
            fraudDetectionError.classList.remove('hidden');
        } finally {
            fraudDetectionStatus.classList.add('hidden');
            fraudDetectionBtn.disabled = false;
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        if (!fraudAnalysisCompleted) {
            e.preventDefault();
            alert('⚠️ Veuillez d\'abord activer la détection de fraude GPT-4 avant de soumettre le formulaire.');
            return;
        }
        
        // Allow form submission if fraud analysis is completed
        submitBtn.disabled = true;
        submitBtn.textContent = '⏳ Enregistrement...';
    });
});

// Real-time form validation
document.addEventListener('input', function(e) {
    if (e.target.matches('input, select, textarea')) {
        const field = e.target;
        const value = field.value.trim();
        
        if (field.hasAttribute('required') && !value) {
            field.classList.add('border-red-300');
        } else {
            field.classList.remove('border-red-300');
        }
    }
});
</script>
@endsection 