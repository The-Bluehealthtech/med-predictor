<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logos des Clubs FTF - Ligue 1</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="max-w-6xl mx-auto p-6">
        <!-- En-tête -->
        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold text-blue-600 mb-4">🏆 Logos des Clubs FTF</h1>
            <p class="text-xl text-gray-600">Fédération Tunisienne de Football - Ligue 1</p>
            <p class="text-sm text-gray-500 mt-2">Tous les logos ont été générés automatiquement</p>
        </div>

        <!-- Grille des logos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- EST -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Esperance Sportive de Tunis']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-red-600 mb-2">EST</h3>
                <p class="text-sm text-gray-600">Esperance Sportive de Tunis</p>
                <p class="text-xs text-gray-500">Tunis</p>
            </div>

            <!-- ESS -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Etoile Sportive du Sahel']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-green-600 mb-2">ESS</h3>
                <p class="text-sm text-gray-600">Etoile Sportive du Sahel</p>
                <p class="text-xs text-gray-500">Sousse</p>
            </div>

            <!-- CA -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Club Africain']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-blue-600 mb-2">CA</h3>
                <p class="text-sm text-gray-600">Club Africain</p>
                <p class="text-xs text-gray-500">Tunis</p>
            </div>

            <!-- CSS -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'CS Sfaxien']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-yellow-600 mb-2">CSS</h3>
                <p class="text-sm text-gray-600">CS Sfaxien</p>
                <p class="text-xs text-gray-500">Sfax</p>
            </div>

            <!-- CAB -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Club Athlétique Bizertin']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-purple-600 mb-2">CAB</h3>
                <p class="text-sm text-gray-600">CA Bizertin</p>
                <p class="text-xs text-gray-500">Bizerte</p>
            </div>

            <!-- ST -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Stade Tunisien']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-indigo-600 mb-2">ST</h3>
                <p class="text-sm text-gray-600">Stade Tunisien</p>
                <p class="text-xs text-gray-500">Tunis</p>
            </div>

            <!-- USM -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Union Sportive Monastirienne']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-pink-600 mb-2">USM</h3>
                <p class="text-sm text-gray-600">US Monastirienne</p>
                <p class="text-xs text-gray-500">Monastir</p>
            </div>

            <!-- USBG -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Union Sportive de Ben Guerdane']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-orange-600 mb-2">USBG</h3>
                <p class="text-sm text-gray-600">US Ben Guerdane</p>
                <p class="text-xs text-gray-500">Ben Guerdane</p>
            </div>

            <!-- OB -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Olympique de Béja']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-teal-600 mb-2">OB</h3>
                <p class="text-sm text-gray-600">Olympique de Béja</p>
                <p class="text-xs text-gray-500">Béja</p>
            </div>

            <!-- ASG -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Avenir Sportif de Gabès']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-cyan-600 mb-2">ASG</h3>
                <p class="text-sm text-gray-600">Avenir Sportif de Gabès</p>
                <p class="text-xs text-gray-500">Gabès</p>
            </div>

            <!-- ESM -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Étoile Sportive de Métlaoui']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-lime-600 mb-2">ESM</h3>
                <p class="text-sm text-gray-600">ES de Métlaoui</p>
                <p class="text-xs text-gray-500">Métlaoui</p>
            </div>

            <!-- ESZ -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Étoile Sportive de Zarzis']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-amber-600 mb-2">ESZ</h3>
                <p class="text-sm text-gray-600">ES de Zarzis</p>
                <p class="text-xs text-gray-500">Zarzis</p>
            </div>

            <!-- JSO -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Jeunesse Sportive de el Omrane']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-emerald-600 mb-2">JSO</h3>
                <p class="text-sm text-gray-600">JS de el Omrane</p>
                <p class="text-xs text-gray-500">El Omrane</p>
            </div>

            <!-- EGSG -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'El Gawafel Sportives de Gafsa']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-rose-600 mb-2">EGSG</h3>
                <p class="text-sm text-gray-600">El Gawafel de Gafsa</p>
                <p class="text-xs text-gray-500">Gafsa</p>
            </div>

            <!-- ASS -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Association Sportive de Soliman']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-slate-600 mb-2">ASS</h3>
                <p class="text-sm text-gray-600">AS Soliman</p>
                <p class="text-xs text-gray-500">Soliman</p>
            </div>

            <!-- UST -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-24 h-24 mx-auto mb-4">
                    <x-club-logo-working 
                        :club="(object)['name' => 'Union Sportive de Tataouine']"
                        class="w-full h-full"
                    />
                </div>
                <h3 class="font-bold text-lg text-stone-600 mb-2">UST</h3>
                <p class="text-sm text-gray-600">US Tataouine</p>
                <p class="text-xs text-gray-500">Tataouine</p>
            </div>

        </div>

        <!-- Résumé -->
        <div class="mt-12 bg-white rounded-xl shadow-lg p-8 text-center">
            <h2 class="text-3xl font-bold text-green-600 mb-4">🎉 Résumé</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-4xl font-bold text-green-600 mb-2">16</div>
                    <p class="text-green-700 font-semibold">Clubs FTF</p>
                    <p class="text-sm text-green-600">Ligue 1</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-4xl font-bold text-blue-600 mb-2">16</div>
                    <p class="text-blue-700 font-semibold">Logos SVG</p>
                    <p class="text-sm text-blue-600">Générés</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="text-4xl font-bold text-purple-600 mb-2">100%</div>
                    <p class="text-purple-700 font-semibold">Succès</p>
                    <p class="text-sm text-purple-600">Taux</p>
                </div>
            </div>
        </div>

        <!-- Informations techniques -->
        <div class="mt-8 bg-gray-50 rounded-xl p-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">🔧 Informations techniques</h3>
            <div class="text-sm text-gray-600 space-y-2">
                <p><strong>Composant :</strong> <code class="bg-gray-200 px-2 py-1 rounded">x-club-logo-working</code></p>
                <p><strong>Format :</strong> SVG (vectoriel, redimensionnable)</p>
                <p><strong>Dossier :</strong> <code class="bg-gray-200 px-2 py-1 rounded">public/clubs/</code></p>
                <p><strong>Génération :</strong> Automatique via Node.js</p>
                <p><strong>Fallback :</strong> Icône 🏟️ si logo manquant</p>
            </div>
        </div>
    </div>
</body>
</html>

