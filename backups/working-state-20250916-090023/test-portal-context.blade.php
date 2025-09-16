<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Portail Context - Association Logo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">🧪 Test Portail Context - Association Logo</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Test avec association Maroc -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">🇲🇦 Association Maroc</h3>
                <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group">
                    @php
                        // Simuler une association
                        $association = (object)[
                            'name' => 'Maroc',
                            'country' => 'Maroc',
                            'id' => 7
                        ];
                    @endphp
                    
                    <x-association-logo 
                        :association="$association"
                        size="2xl"
                        :show-fallback="true"
                        class="w-full h-full"
                    />
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏆 Gérer
                        </a>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-600">Pays: Maroc → Code: MA → Logo: MA.png</p>
            </div>
            
            <!-- Test avec association Tunisie -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">🇹🇳 Association Tunisie</h3>
                <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group">
                    @php
                        // Simuler une association
                        $association = (object)[
                            'name' => 'FTF',
                            'country' => 'Tunisie',
                            'id' => 9
                        ];
                    @endphp
                    
                    <x-association-logo 
                        :association="$association"
                        size="2xl"
                        :show-fallback="true"
                        class="w-full h-full"
                    />
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏆 Gérer
                        </a>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-600">Pays: Tunisie → Code: TN → Logo: TN.png</p>
            </div>
            
            <!-- Test avec association France -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">🇫🇷 Association France</h3>
                <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group">
                    @php
                        // Simuler une association
                        $association = (object)[
                            'name' => 'Association Test',
                            'country' => 'France',
                            'id' => 1
                        ];
                    @endphp
                    
                    <x-association-logo 
                        :association="$association"
                        size="2xl"
                        :show-fallback="true"
                        class="w-full h-full"
                    />
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏆 Gérer
                        </a>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-600">Pays: France → Code: FR → Logo: FR.png</p>
            </div>
            
            <!-- Test sans association -->
            <div class="bg-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-4">❌ Sans Association</h3>
                <div class="w-40 h-40 bg-indigo-100 rounded-lg p-3 flex items-center justify-center relative group">
                    <x-association-logo 
                        :association="null"
                        size="2xl"
                        :show-fallback="true"
                        class="w-full h-full"
                    />
                    
                    <!-- Bouton Gérer qui apparaît au survol -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                            🏆 Gérer
                        </a>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-600">Aucune association → Fallback 🏆</p>
            </div>
        </div>
        
        <!-- Informations de debug -->
        <div class="mt-8 bg-white rounded-lg p-6 shadow-md">
            <h3 class="text-lg font-semibold mb-4">🔍 Informations de Debug</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold mb-2">Logos disponibles :</h4>
                    @php
                        $logoFiles = glob(public_path('associations/*.png'));
                        $availableLogos = array_map(function($file) {
                            return basename($file, '.png');
                        }, $logoFiles);
                        $availableLogos = array_slice($availableLogos, 0, 10);
                    @endphp
                    <ul class="text-sm text-gray-600">
                        @foreach($availableLogos as $code)
                            <li>• {{ $code }}.png</li>
                        @endforeach
                    </ul>
                    <p class="text-xs text-gray-500 mt-1">Total: {{ count(glob(public_path('associations/*.png'))) }} logos</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Test du modèle :</h4>
                    @php
                        try {
                            $testAssoc = new App\Models\Association();
                            $testAssoc->country = 'Maroc';
                            $testAssoc->name = 'Maroc';
                            echo '<p class="text-sm text-green-600">✅ Modèle fonctionne</p>';
                            echo '<p class="text-sm text-gray-600">Code pays: ' . $testAssoc->getCountryCode() . '</p>';
                            echo '<p class="text-sm text-gray-600">Logo national: ' . ($testAssoc->hasNationalLogo() ? 'OUI' : 'NON') . '</p>';
                        } catch (Exception $e) {
                            echo '<p class="text-sm text-red-600">❌ Erreur: ' . $e->getMessage() . '</p>';
                        }
                    @endphp
                </div>
            </div>
        </div>
    </div>
</body>
</html>

