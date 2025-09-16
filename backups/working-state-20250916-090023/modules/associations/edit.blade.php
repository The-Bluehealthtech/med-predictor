<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Association - {{ $association->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-red-50 to-pink-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center">✏️ Modifier Association</h1>
        
        <div class="mb-8 flex justify-between items-center">
            <a href="/associations-view" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                ← Retour aux associations
            </a>
            <a href="/modules" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                📋 Retour aux modules
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('associations-view.update', $association->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                        <input type="text" name="name" value="{{ old('name', $association->name) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom abrégé</label>
                        <input type="text" name="short_name" value="{{ old('short_name', $association->short_name) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pays *</label>
                        <input type="text" name="country" value="{{ old('country', $association->country) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confédération *</label>
                        <select name="confederation_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="">Sélectionner...</option>
                            @foreach($confederations as $confederation)
                                <option value="{{ $confederation->id }}" {{ $association->confederation_id == $confederation->id ? 'selected' : '' }}>
                                    {{ $confederation->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Classement FIFA</label>
                        <input type="number" name="fifa_ranking" value="{{ old('fifa_ranking', $association->fifa_ranking) }}" 
                               min="1" max="999" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut *</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="active" {{ $association->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $association->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ $association->status == 'suspended' ? 'selected' : '' }}>Suspendue</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                    <div>
                        <h4 class="text-lg font-medium text-gray-700 mb-4">Logo de l'association</h4>
                        
                        @if($association->association_logo_url)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Logo actuel :</p>
                                <img src="{{ asset('storage/' . $association->association_logo_url) }}" 
                                     alt="Logo actuel" class="h-24 w-24 object-contain border-2 border-gray-200 rounded-lg">
                            </div>
                        @endif
                        
                        <input type="file" name="association_logo" accept="image/*" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <p class="text-xs text-gray-500 mt-1">Formats : JPG, PNG, GIF. Max : 2MB</p>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-medium text-gray-700 mb-4">Drapeau du pays</h4>
                        
                        @if($association->nation_flag_url)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Drapeau actuel :</p>
                                <img src="{{ asset('image/' . $association->nation_flag_url) }}" 
                                     alt="Drapeau actuel" class="h-24 w-16 object-cover border-2 border-gray-200 rounded-lg">
                            </div>
                        @endif
                        
                        <input type="file" name="nation_flag" accept="image/*" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <p class="text-xs text-gray-500 mt-1">Formats : JPG, PNG, GIF. Max : 2MB</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="/associations-view" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        Annuler
                    </a>
                    <button type="submit" class="px-8 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        💾 Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
