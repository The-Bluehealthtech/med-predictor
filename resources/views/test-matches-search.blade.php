<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Recherche Matches</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white shadow-sm rounded-lg mb-6">
                <div class="px-6 py-4">
                    <h1 class="text-2xl font-bold text-gray-900">Test Recherche Matches</h1>
                    <p class="text-gray-600">Test de la recherche dans les matches</p>
                </div>
            </div>

            <!-- Formulaire de recherche -->
            <div class="bg-white shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ request()->url() }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="team_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nom de l'équipe
                                </label>
                                <input type="text" name="team_name" id="team_name" 
                                       value="{{ request('team_name') }}"
                                       placeholder="Rechercher par équipe..."
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="competition" class="block text-sm font-medium text-gray-700 mb-1">
                                    Compétition
                                </label>
                                <input type="text" name="competition" id="competition" 
                                       value="{{ request('competition') }}"
                                       placeholder="Rechercher par compétition..."
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Rechercher
                            </button>
                            
                            <a href="{{ request()->url() }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Résultats -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        Résultats ({{ $matches->total() }} matches trouvés)
                    </h2>
                    
                    @if($matches->count() > 0)
                        <div class="space-y-4">
                            @foreach($matches as $match)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">
                                                {{ $match->homeTeam->name ?? 'TBD' }} vs {{ $match->awayTeam->name ?? 'TBD' }}
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                {{ $match->competition->name ?? 'Competition' }} • {{ $match->match_date ?? 'N/A' }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $match->venue ?? 'TBD' }} • {{ $match->status ?? 'TBD' }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $match->status ?? 'TBD' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $matches->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500">Aucun match trouvé</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
