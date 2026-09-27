<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer le Logo - {{ $association->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto p-6">
        <!-- En-tête -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ __('🏆 Gérer le Logo') }}</h1>
                    <p class="text-gray-600 mt-2">Association : <strong>{{ $association->name }}</strong></p>
                    <p class="text-gray-600">Pays : <strong>{{ $association->country }}</strong></p>
                    @if($countryCode)
                        <p class="text-gray-600">Code pays : <strong>{{ $countryCode }}</strong></p>
                    @endif
                </div>
                <a href="{{ url()->previous() }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('errors.generic_back') }}
                </a>
            </div>
        </div>

        <!-- Messages de succès/erreur -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Logo actuel -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4">📸 Logo Actuel</h3>
                
                <div class="space-y-4">
                    @if($association->association_logo_url)
                        <!-- Logo personnalisé -->
                        <div class="text-center">
                            <img src="{{ $association->association_logo_url }}" 
                                 alt="Logo personnalisé {{ $association->name }}"
                                 class="w-32 h-32 object-contain mx-auto border-2 border-gray-200 rounded-lg">
                            <p class="text-sm text-gray-600 mt-2">{{ __('Logo personnalisé actuel') }}</p>
                        </div>
                        
                        <!-- Bouton pour réinitialiser -->
                        <form action="{{ route('associations.reset-national-logo', $association->id) }}" method="POST" class="text-center">
                            @csrf
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors">{{ __('🔄 Réinitialiser au logo national') }}</button>
                        </form>
                    @else
                        <!-- Logo national -->
                        @if($nationalLogoExists)
                            <div class="text-center">
                                <img src="{{ $nationalLogoUrl }}" 
                                     alt="Logo national {{ $association->name }}"
                                     class="w-32 h-32 object-contain mx-auto border-2 border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-600 mt-2">Logo national (API-Football)</p>
                            </div>
                        @else
                            <div class="text-center">
                                <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center mx-auto">
                                    <span class="text-4xl text-gray-400">🏆</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-2">{{ __('Aucun logo disponible') }}</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4">⚙️ Actions</h3>
                
                <div class="space-y-4">
                    <!-- Upload de nouveau logo -->
                    <div>
                        <h4 class="font-medium mb-2">{{ __('📤 Uploader un nouveau logo') }}</h4>
                        <form action="{{ route('associations.update-logo', $association->id) }}" 
                              method="POST" 
                              enctype="multipart/form-data"
                              class="space-y-3">
                            @csrf
                            <div>
                                <input type="file" 
                                       name="logo" 
                                       accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                       required>
                                <p class="text-xs text-gray-500 mt-1">{{ __('Formats acceptés : JPEG, PNG, JPG, GIF (max 2MB)') }}</p>
                            </div>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                                💾 Sauvegarder le logo
                            </button>
                        </form>
                    </div>

                    <!-- Mise à jour des logos nationaux -->
                    <div class="border-t pt-4">
                        <h4 class="font-medium mb-2">{{ __('🌍 Mise à jour des logos nationaux') }}</h4>
                        <p class="text-sm text-gray-600 mb-3">{{ __('Télécharger les derniers logos depuis l\'API-Football') }}</p>
                        <form action="{{ route('associations.update-national-logos') }}" method="POST" class="text-center">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">{{ __('🔄 Mettre à jour depuis l\'API') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations techniques -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4">{{ __('🔍 Informations Techniques') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p><strong>ID Association :</strong> {{ $association->id }}</p>
                    <p><strong>{{ __('clinical.name_label') }}</strong> {{ $association->name }}</p>
                    <p><strong>Pays :</strong> {{ $association->country }}</p>
                    <p><strong>Code pays ISO :</strong> {{ $countryCode ?? 'Non défini' }}</p>
                </div>
                <div>
                    <p><strong>{{ __('Logo personnalisé :') }}</strong> {{ $association->association_logo_url ? 'OUI' : 'NON' }}</p>
                    <p><strong>Logo national disponible :</strong> {{ $nationalLogoExists ? 'OUI' : 'NON' }}</p>
                    @if($nationalLogoExists)
                        <p><strong>Chemin logo national :</strong> associations/{{ $countryCode }}.png</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>

