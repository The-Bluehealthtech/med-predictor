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
            
            <form action="{{ route('players.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                
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
                            <option value="Tunisie" {{ old('nationality') == 'Tunisie' ? 'selected' : '' }}>Tunisie</option>
                            <option value="France" {{ old('nationality') == 'France' ? 'selected' : '' }}>France</option>
                            <option value="Algérie" {{ old('nationality') == 'Algérie' ? 'selected' : '' }}>Algérie</option>
                            <option value="Maroc" {{ old('nationality') == 'Maroc' ? 'selected' : '' }}>Maroc</option>
                            <option value="Sénégal" {{ old('nationality') == 'Sénégal' ? 'selected' : '' }}>Sénégal</option>
                            <option value="Côte d'Ivoire" {{ old('nationality') == 'Côte d\'Ivoire' ? 'selected' : '' }}>Côte d'Ivoire</option>
                            <option value="Nigeria" {{ old('nationality') == 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                            <option value="Ghana" {{ old('nationality') == 'Ghana' ? 'selected' : '' }}>Ghana</option>
                            <option value="Cameroun" {{ old('nationality') == 'Cameroun' ? 'selected' : '' }}>Cameroun</option>
                            <option value="Égypte" {{ old('nationality') == 'Égypte' ? 'selected' : '' }}>Égypte</option>
                            <option value="Autre" {{ old('nationality') == 'Autre' ? 'selected' : '' }}>Autre</option>
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
                            <option value="ST" {{ old('position') == 'ST' ? 'selected' : '' }}>Attaquant (ST)</option>
                            <option value="RW" {{ old('position') == 'RW' ? 'selected' : '' }}>Ailier droit (RW)</option>
                            <option value="LW" {{ old('position') == 'LW' ? 'selected' : '' }}>Ailier gauche (LW)</option>
                            <option value="CAM" {{ old('position') == 'CAM' ? 'selected' : '' }}>Milieu offensif (CAM)</option>
                            <option value="CM" {{ old('position') == 'CM' ? 'selected' : '' }}>Milieu central (CM)</option>
                            <option value="CDM" {{ old('position') == 'CDM' ? 'selected' : '' }}>Milieu défensif (CDM)</option>
                            <option value="CB" {{ old('position') == 'CB' ? 'selected' : '' }}>Défenseur central (CB)</option>
                            <option value="RB" {{ old('position') == 'RB' ? 'selected' : '' }}>Arrière droit (RB)</option>
                            <option value="LB" {{ old('position') == 'LB' ? 'selected' : '' }}>Arrière gauche (LB)</option>
                            <option value="GK" {{ old('position') == 'GK' ? 'selected' : '' }}>Gardien (GK)</option>
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
                </div>

                <!-- Section Photo du Joueur -->
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📸 Photo du Joueur</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="player_picture" class="block text-sm font-medium text-gray-700 mb-2">
                                Photo du joueur
                            </label>
                            <input type="file" name="player_picture" id="player_picture" 
                                   accept="image/*" 
                                   class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                   onchange="previewImage(this)">
                            <p class="mt-1 text-sm text-gray-500">Formats acceptés: JPG, PNG, GIF. Taille max: 5MB</p>
                            @error('player_picture')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Aperçu de la photo
                            </label>
                            <div id="imagePreviewContainer" class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50">
                                <span class="text-gray-400 text-4xl">?</span>
                            </div>
                            <button type="button" id="removeImageBtn" onclick="removeImage()" 
                                    class="mt-2 text-sm text-red-600 hover:text-red-800 hidden">
                                🗑️ Supprimer la photo
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('players.index') }}" 
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

<script>
function previewImage(input) {
    const container = document.getElementById('imagePreviewContainer');
    const removeBtn = document.getElementById('removeImageBtn');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validation du type de fichier
        if (!file.type.startsWith('image/')) {
            alert('❌ Veuillez sélectionner un fichier image valide (JPG, PNG, GIF)');
            input.value = '';
            return;
        }
        
        // Validation de la taille (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert('❌ La taille du fichier ne doit pas dépasser 5MB');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            container.innerHTML = `<img src="${e.target.result}" alt="Aperçu" class="w-full h-full object-cover rounded-lg">`;
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
    container.innerHTML = '<span class="text-gray-400 text-4xl">?</span>';
    removeBtn.classList.add('hidden');
}
</script>
@endsection 