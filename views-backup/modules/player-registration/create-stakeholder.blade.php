@extends('layouts.app')

@section('title', 'Nouveau Stakeholder - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">👥 Nouveau Stakeholder</h1>
            <p class="text-gray-600 mt-2">Créer un nouveau stakeholder (arbitre, officiel, médecin, etc.)</p>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Informations du Stakeholder</h2>
            </div>
            
            <form action="{{ route('player-registration.store-stakeholder') }}" method="POST" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom complet *
                        </label>
                        <input type="text" name="name" id="name" 
                               value="{{ old('name') }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

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
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            Rôle *
                        </label>
                        <select name="role" id="role" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un rôle</option>
                            @foreach($stakeholderRoles as $key => $role)
                                <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
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

                    @if($clubs->isNotEmpty())
                    <div>
                        <label for="club_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Club (si applicable)
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

                    @if($associations->isNotEmpty())
                    <div>
                        <label for="association_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Association (si applicable)
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

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de naissance
                        </label>
                        <input type="date" name="date_of_birth" id="date_of_birth" 
                               value="{{ old('date_of_birth') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Numéro de licence
                        </label>
                        <input type="text" name="license_number" id="license_number" 
                               value="{{ old('license_number') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('license_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="license_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de licence
                        </label>
                        <select name="license_type" id="license_type"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner un type</option>
                            @foreach($licenseTypes as $key => $type)
                                <option value="{{ $key }}" {{ old('license_type') == $key ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('license_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">
                            Spécialisation (pour personnel médical)
                        </label>
                        <input type="text" name="specialization" id="specialization" 
                               value="{{ old('specialization') }}"
                               placeholder="ex: Cardiologie, Orthopédie, Physiothérapie"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('specialization')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-2">
                            Années d'expérience
                        </label>
                        <input type="number" name="experience_years" id="experience_years" 
                               value="{{ old('experience_years') }}" min="0" max="50"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('experience_years')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('player-registration.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        Créer le stakeholder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const clubSelect = document.getElementById('club_id');
    const associationSelect = document.getElementById('association_id');
    const specializationField = document.getElementById('specialization');

    // Show/hide fields based on role selection
    roleSelect.addEventListener('change', function() {
        const selectedRole = this.value;
        
        // Show club field for club-related roles
        if (clubSelect) {
            if (selectedRole.includes('club')) {
                clubSelect.parentElement.style.display = 'block';
            } else {
                clubSelect.parentElement.style.display = 'none';
                clubSelect.value = '';
            }
        }

        // Show association field for association-related roles
        if (associationSelect) {
            if (selectedRole.includes('association')) {
                associationSelect.parentElement.style.display = 'block';
            } else {
                associationSelect.parentElement.style.display = 'none';
                associationSelect.value = '';
            }
        }

        // Show specialization field for medical roles
        if (specializationField) {
            if (selectedRole.includes('medical') || selectedRole.includes('doctor') || selectedRole.includes('physio')) {
                specializationField.parentElement.style.display = 'block';
            } else {
                specializationField.parentElement.style.display = 'none';
                specializationField.value = '';
            }
        }
    });

    // Trigger change event on page load
    roleSelect.dispatchEvent(new Event('change'));
});
</script>
@endsection 