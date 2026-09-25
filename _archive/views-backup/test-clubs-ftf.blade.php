<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Logos Clubs FTF - Ligue 1</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto p-6">
        <!-- En-tête -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-blue-600 mb-2">🏆 Test Logos Clubs FTF</h1>
                <p class="text-xl text-gray-600">Fédération Tunisienne de Football - Ligue 1</p>
                <p class="text-sm text-gray-500 mt-2">Source : <a href="https://www.worldsoccerpins.com/football-logos-tunisia" target="_blank" class="text-blue-500 hover:underline">worldsoccerpins.com</a></p>
            </div>
        </div>

        <!-- Grille des clubs FTF -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-semibold mb-6 text-green-600">🏟️ Clubs FTF en Ligue 1</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                
                <!-- Esperance Sportive de Tunis -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-red-800 mb-3">Esperance Sportive de Tunis</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Esperance Sportive de Tunis']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-red-700">Code: EST</p>
                    <p class="text-xs text-red-600">Tunis</p>
                </div>

                <!-- Etoile Sportive du Sahel -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-green-800 mb-3">Etoile Sportive du Sahel</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Etoile Sportive du Sahel']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-green-700">Code: ESS</p>
                    <p class="text-xs text-green-600">Sousse</p>
                </div>

                <!-- Club Africain -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-blue-800 mb-3">Club Africain</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Club Africain']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-blue-700">Code: CA</p>
                    <p class="text-xs text-blue-600">Tunis</p>
                </div>

                <!-- CS Sfaxien -->
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-3">CS Sfaxien</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'CS Sfaxien']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-yellow-700">Code: CSS</p>
                    <p class="text-xs text-yellow-600">Sfax</p>
                </div>

                <!-- CA Bizertin -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-purple-800 mb-3">CA Bizertin</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Club Athlétique Bizertin']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-purple-700">Code: CAB</p>
                    <p class="text-xs text-purple-600">Bizerte</p>
                </div>

                <!-- Stade Tunisien -->
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-indigo-800 mb-3">Stade Tunisien</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Stade Tunisien']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-indigo-700">Code: ST</p>
                    <p class="text-xs text-indigo-600">Tunis</p>
                </div>

                <!-- US Monastirienne -->
                <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-pink-800 mb-3">US Monastirienne</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Union Sportive Monastirienne']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-pink-700">Code: USM</p>
                    <p class="text-xs text-pink-600">Monastir</p>
                </div>

                <!-- US Ben Guerdane -->
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-orange-800 mb-3">US Ben Guerdane</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Union Sportive de Ben Guerdane']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-orange-700">Code: USBG</p>
                    <p class="text-xs text-orange-600">Ben Guerdane</p>
                </div>

                <!-- Olympique de Béja -->
                <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-teal-800 mb-3">Olympique de Béja</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Olympique de Béja']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-teal-700">Code: OB</p>
                    <p class="text-xs text-teal-600">Béja</p>
                </div>

                <!-- Avenir Sportif de Gabès -->
                <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-cyan-800 mb-3">Avenir Sportif de Gabès</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Avenir Sportif de Gabès']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-cyan-700">Code: ASG</p>
                    <p class="text-xs text-cyan-600">Gabès</p>
                </div>

                <!-- ES de Métlaoui -->
                <div class="bg-gradient-to-br from-lime-50 to-lime-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-lime-800 mb-3">ES de Métlaoui</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Étoile Sportive de Métlaoui']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-lime-700">Code: ESM</p>
                    <p class="text-xs text-lime-600">Métlaoui</p>
                </div>

                <!-- ES de Zarzis -->
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-amber-800 mb-3">ES de Zarzis</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Étoile Sportive de Zarzis']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-amber-700">Code: ESZ</p>
                    <p class="text-xs text-amber-600">Zarzis</p>
                </div>

                <!-- Jeunesse Sportive de el Omrane -->
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-emerald-800 mb-3">JS de el Omrane</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Jeunesse Sportive de el Omrane']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-emerald-700">Code: JSO</p>
                    <p class="text-xs text-emerald-600">El Omrane</p>
                </div>

                <!-- El Gawafel Sportives de Gafsa -->
                <div class="bg-gradient-to-br from-rose-50 to-rose-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-rose-800 mb-3">El Gawafel de Gafsa</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'El Gawafel Sportives de Gafsa']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-rose-700">Code: EGSG</p>
                    <p class="text-xs text-rose-600">Gafsa</p>
                </div>

                <!-- AS Soliman -->
                <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-slate-800 mb-3">AS Soliman</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Association Sportive de Soliman']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-slate-700">Code: ASS</p>
                    <p class="text-xs text-slate-600">Soliman</p>
                </div>

                <!-- US Tataouine -->
                <div class="bg-gradient-to-br from-stone-50 to-stone-100 rounded-lg p-4 text-center">
                    <h3 class="text-lg font-semibold text-stone-800 mb-3">US Tataouine</h3>
                    <div class="w-32 h-32 mx-auto mb-3">
                        <x-club-logo-working 
                            :club="(object)['name' => 'Union Sportive de Tataouine']"
                            size="2xl"
                            :show-fallback="true"
                            class="w-full h-full"
                        />
                    </div>
                    <p class="text-sm text-stone-700">Code: UST</p>
                    <p class="text-xs text-stone-600">Tataouine</p>
                </div>

            </div>
        </div>

        <!-- Section d'information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-4 text-blue-600">ℹ️ Informations</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-3 text-green-600">📊 Statut actuel</h3>
                    <ul class="space-y-2 text-sm">
                        <li>✅ Composant <code>x-club-logo-working</code> créé</li>
                        <li>✅ Mapping des clubs FTF mis à jour</li>
                        <li>✅ Script de téléchargement configuré</li>
                        <li>🔄 Logos à télécharger depuis worldsoccerpins.com</li>
                        <li>🎯 Prêt pour intégration dans le portail patient</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-3 text-blue-600">🚀 Prochaines étapes</h3>
                    <ul class="space-y-2 text-sm">
                        <li>1. Télécharger les logos depuis worldsoccerpins.com</li>
                        <li>2. Intégrer dans le portail patient existant</li>
                        <li>3. Tester avec de vrais joueurs/clubs</li>
                        <li>4. Ajouter les boutons "Gérer" pour les logos</li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-lg font-semibold mb-2 text-blue-800">🔗 Liens utiles</h3>
                <div class="space-y-2 text-sm">
                    <p><strong>Source des logos :</strong> <a href="https://www.worldsoccerpins.com/football-logos-tunisia" target="_blank" class="text-blue-600 hover:underline">worldsoccerpins.com/football-logos-tunisia</a></p>
                    <p><strong>Script de téléchargement :</strong> <code>scripts/download-ftf-club-logos.js</code></p>
                    <p><strong>Composant :</strong> <code>resources/views/components/club-logo-working.blade.php</code></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

