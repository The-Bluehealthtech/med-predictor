<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion {{ $typeLabel }} - {{ $player->first_name }} {{ $player->last_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">


            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="float-right ml-4">@include('partials.logout-button', ['variant' => 'chip'])</div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">
                    {{ $typeIcon ?? '📸' }} Gestion de {{ $typeLabel ?? __('la photo') }} de {{ $player->first_name ?? 'Joueur' }} {{ $player->last_name ?? '' }}
                </h1>
                <p class="text-gray-600">Gérez {{ $typeLabel ?? __('la photo') }} de ce joueur</p>
            </div>

            <!-- Élément actuel -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $typeLabel }} actuel(le)</h2>
                
                @if($currentImageUrl)
                    <div class="flex items-center space-x-4">
                        <img src="{{ $currentImageUrl }}" 
                             alt="{{ $typeLabel }} de {{ $player->first_name }} {{ $player->last_name }}"
                             class="w-24 h-24 object-contain rounded-lg border-2 border-gray-200">
                        <div>
                            <p class="text-sm text-gray-600">
                                <strong>{{ __('Source :') }}</strong>
                                @if(filter_var($currentImageUrl, FILTER_VALIDATE_URL))
                                    URL externe
                                @else
                                    Fichier uploadé
                                @endif
                            </p>
                            <form action="{{ route('joueur.photo.delete', $player->id) }}" method="POST" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="type" value="{{ $type }}">
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-6xl mb-4">{{ $typeIcon }}</div>
                        <p class="text-gray-500">Aucun(e) {{ $typeLabel }} disponible</p>
                    </div>
                @endif
            </div>

            <!-- Upload de nouveau fichier ou lien externe -->
            @if(isset($externalLink) && in_array($type, ['nationality', 'association_flag']))
                <!-- Pour les drapeaux, afficher un lien vers flag-icons -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('🏳️ Gestion des drapeaux') }}</h2>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h3 class="text-lg font-medium text-blue-800 mb-2">{{ __('📚 Ressource recommandée : Flag Icons') }}</h3>
                        <p class="text-blue-700 mb-4">{{ __('Pour gérer les drapeaux de nationalité et d\'association, nous recommandons d\'utiliser le projet') }}<strong>Flag Icons</strong>{{ __('qui fournit une collection complète de drapeaux SVG de tous les pays.') }}</p>
                        
                        <div class="flex space-x-4">
                            <a href="{{ $externalLink }}" 
                               target="_blank"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                                {{ __('🌐 Voir Flag Icons sur GitHub') }}
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                            
                            <a href="https://flagicons.lipis.dev" 
                               target="_blank"
                               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">{{ __('🎏 Voir la démo') }}<svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gray-50 rounded border">
                            <h4 class="font-medium text-gray-800 mb-2">💡 Comment utiliser Flag Icons :</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Installez via npm : <code class="bg-gray-200 px-1 rounded">npm install flag-icons</code></li>
                                <li>• Importez le CSS : <code class="bg-gray-200 px-1 rounded">import "flag-icons/css/flag-icons.min.css"</code></li>
                                <li>• Utilisez : <code class="bg-gray-200 px-1 rounded">&lt;span class="fi fi-fr"&gt;&lt;/span&gt;</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <!-- Pour les autres types, afficher le formulaire d'upload normal -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">📤 Uploader un(e) nouveau(elle) {{ $typeLabel }}</h2>
                    
                    <form action="{{ route('joueur.photo.upload.post', $player->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Sélectionner une image') }}</label>
                            <input type="file" name="photo" accept="image/*" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-sm text-gray-500 mt-1">{{ __('Formats acceptés : JPEG, PNG, JPG, GIF (max 2MB)') }}</p>
                        </div>
                        
                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                            📤 Uploader
                        </button>
                    </form>
                </div>
            @endif

            <!-- URL externe -->
            @if(!in_array($type, ['nationality', 'association_flag']))
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">🔗 URL externe</h2>
                    
                    <form action="{{ route('joueur.photo.external', $player->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="{{ $type }}">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('URL de l\'image') }}
                            </label>
                            <input type="url" name="external_url" 
                                   value="{{ $currentExternalUrl ?? '' }}"
                                   placeholder="https://exemple.com/{{ $type }}.jpg"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-sm text-gray-500 mt-1">{{ __('Entrez l\'URL d\'une image externe') }}</p>
                        </div>
                        
                        <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">{{ __('🔗 Mettre à jour') }}</button>
                    </form>
                </div>
            @endif

            <!-- Génération d'avatar -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('🎨 Générer un avatar') }}</h2>
                
                <p class="text-gray-600 mb-4">{{ __('Générez automatiquement un avatar basé sur le nom du joueur en utilisant l\'API DiceBear.') }}</p>
                
                <form action="{{ route('joueur.photo.generate', $player->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-purple-500 text-white px-6 py-2 rounded hover:bg-purple-600">{{ __('🎨 Générer Avatar') }}</button>
                </form>
            </div>

            <!-- Retour -->
            <div class="text-center">
                <a href="{{ route('joueur.show', $player->id) }}" 
                   class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                    {{ __('← Retour au profil') }}
                </a>
            </div>

            <!-- Messages d'erreur -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Messages de succès -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mt-4">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>



